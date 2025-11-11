<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Google\Client as GoogleClient;
use League\OAuth2\Client\Provider\LinkedIn;
use App\Models\UserModel;

class AuthController extends ResourceController
{
    protected $request;
    protected $userModel;
    protected $jwtKey;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jwtKey = getenv('JWT_SECRET') ?: 'your_jwt_secret_key';
    }

    /**
     * Helper: Get input from JSON or form data
     */
    private function getInput($key)
    {
        $json = $this->request->getJSON(true);
        if ($json && isset($json[$key])) {
            return $json[$key];
        }
        return $this->request->getPost($key);
    }

    /* -----------------------------------------------------------------
       1️⃣ REGISTER (Email + Password)
    ----------------------------------------------------------------- */
    public function register()
    {
        $rules = [
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'firstName' => 'required',
            'lastName' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $verificationCode = random_int(100000, 999999);

        $data = [
            'email' => $this->getInput('email'),
            'password' => password_hash($this->getInput('password'), PASSWORD_BCRYPT),
            'first_name' => $this->getInput('firstName'),
            'last_name' => $this->getInput('lastName'),
            'provider' => 'email',
            'verification_code' => $verificationCode,
            'is_verified' => 0
        ];

        $userId = $this->userModel->insert($data);

        if (!$userId) {
            return $this->fail('Unable to create account');
        }

        $this->sendVerificationEmail($data['email'], $verificationCode);

        return $this->respondCreated([
            'status' => 201,
            'message' => 'Registration successful. Please check your email for verification code',
            'email' => $data['email']
        ]);
    }

    /* -----------------------------------------------------------------
       📧 Send Verification Email (Currently Logging Only)
    ----------------------------------------------------------------- */
    private function sendVerificationEmail($email, $code)
    {
        log_message('info', "Sending verification code $code to $email");

        // Write backup log for debugging
        file_put_contents(
            WRITEPATH . 'logs/email-log.log',
            "[" . date('Y-m-d H:i:s') . "] Email: $email Code: $code\n",
            FILE_APPEND
        );
    }

    /* -----------------------------------------------------------------
       ✅ VERIFY EMAIL
    ----------------------------------------------------------------- */
    public function verifyEmail()
    {
        $email = $this->getInput('email');
        $code = $this->getInput('code');

        $user = $this->userModel->where('email', $email)->first();
        if (!$user) return $this->failNotFound('User not found');

        if ($user['verification_code'] != $code) {
            return $this->fail('Invalid verification code');
        }

        $this->userModel->update($user['id'], [
            'is_verified' => 1,
            'verification_code' => null
        ]);

        return $this->respond([
            'status' => 200,
            'message' => 'Email verified successfully'
        ]);
    }

    /* -----------------------------------------------------------------
       2️⃣ LOGIN
    ----------------------------------------------------------------- */
    public function login()
    {
        $email = $this->getInput('email');
        $password = $this->getInput('password');

        $user = $this->userModel->where('email', $email)->first();
        if (!$user || !password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Invalid email or password');
        }

        if (!$user['is_verified']) {
            return $this->fail('Please verify your email before logging in');
        }

        return $this->respond([
            'status' => 200,
            'message' => 'Login successful',
            'data' => $this->generateTokenResponse($user)
        ]);
    }

    /* -----------------------------------------------------------------
       🔄 GOOGLE OAUTH
    ----------------------------------------------------------------- */
    public function googleStart()
    {
        $client = new GoogleClient();
        $client->setClientId(getenv('GOOGLE_CLIENT_ID'));
        $client->setRedirectUri(base_url('auth/oauth/google/callback'));
        $client->addScope(['email', 'profile']);

        return $this->respond([
            'status' => 200,
            'message' => 'Redirect to Google Login',
            'redirect_url' => $client->createAuthUrl()
        ]);
    }

    public function googleCallback()
    {
        try {
            $client = new GoogleClient();
            $client->setClientId(getenv('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
            $client->setRedirectUri(base_url('auth/oauth/google/callback'));

            $token = $client->fetchAccessTokenWithAuthCode($this->request->getVar('code'));
            if (isset($token['error'])) return $this->failUnauthorized('Google Auth failed');

            $client->setAccessToken($token['access_token']);
            $googleUser = $client->verifyIdToken();

            $user = $this->userModel->where('email', $googleUser['email'])->first();

            if (!$user) {
                $userId = $this->userModel->insert([
                    'email' => $googleUser['email'],
                    'first_name' => $googleUser['given_name'],
                    'last_name' => $googleUser['family_name'] ?? '',
                    'provider' => 'google',
                    'provider_id' => $googleUser['sub'],
                    'is_verified' => 1
                ]);
                $user = $this->userModel->find($userId);
            }

            return $this->respond([
                'status' => 200,
                'message' => 'Google login successful',
                'data' => $this->generateTokenResponse($user)
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
       🔗 LINKEDIN OAUTH
    ----------------------------------------------------------------- */
    public function linkedinStart()
    {
        $provider = new LinkedIn([
            'clientId' => getenv('LINKEDIN_CLIENT_ID'),
            'clientSecret' => getenv('LINKEDIN_CLIENT_SECRET'),
            'redirectUri' => base_url('auth/oauth/linkedin/callback')
        ]);

        $authUrl = $provider->getAuthorizationUrl();
        session()->set('oauth2state', $provider->getState());

        return $this->respond([
            'status' => 200,
            'message' => 'Redirect to LinkedIn Login',
            'redirect_url' => $authUrl
        ]);
    }

    public function linkedinCallback()
    {
        $provider = new LinkedIn([
            'clientId' => getenv('LINKEDIN_CLIENT_ID'),
            'clientSecret' => getenv('LINKEDIN_CLIENT_SECRET'),
            'redirectUri' => base_url('auth/oauth/linkedin/callback')
        ]);

        if ($this->request->getVar('state') !== session()->get('oauth2state')) {
            session()->remove('oauth2state');
            return $this->failUnauthorized('Invalid LinkedIn state');
        }

        try {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $this->request->getVar('code')
            ]);

            $linkedInUser = $provider->getResourceOwner($token)->toArray();
            $email = $linkedInUser['emailAddress'] ?? null;

            if (!$email) return $this->fail('No email returned from LinkedIn');

            $user = $this->userModel->where('email', $email)->first();

            if (!$user) {
                $userId = $this->userModel->insert([
                    'email' => $email,
                    'first_name' => $linkedInUser['firstName']['localized']['en_US'] ?? '',
                    'last_name' => $linkedInUser['lastName']['localized']['en_US'] ?? '',
                    'provider' => 'linkedin',
                    'provider_id' => $linkedInUser['id'],
                    'is_verified' => 1
                ]);
                $user = $this->userModel->find($userId);
            }

            return $this->respond([
                'status' => 200,
                'message' => 'LinkedIn login successful',
                'data' => $this->generateTokenResponse($user)
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
       🔥 JWT GENERATOR
    ----------------------------------------------------------------- */
    private function generateTokenResponse($user)
    {
        $payload = [
            'uid' => $user['id'],
            'email' => $user['email'],
            'iat' => time(),
            'exp' => time() + 3600
        ];

        $refreshPayload = [
            'uid' => $user['id'],
            'exp' => time() + 604800
        ];

        return [
            'access_token' => JWT::encode($payload, $this->jwtKey, 'HS256'),
            'refresh_token' => JWT::encode($refreshPayload, $this->jwtKey, 'HS256'),
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'firstName' => $user['first_name'],
                'lastName' => $user['last_name']
            ]
        ];
    }
}

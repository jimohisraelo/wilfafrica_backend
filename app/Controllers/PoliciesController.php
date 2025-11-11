<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\UserModel;

class PoliciesController extends ResourceController
{
    /**
     * @var IncomingRequest
     */
    protected $request;
    protected $format = 'json';
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // GET /policies/bundle
    public function bundle()
    {
        // Change it to the right thing
        return $this->respond([
            'termsUrl' => base_url('docs/terms.pdf'),
            'privacyUrl' => base_url('docs/privacy.pdf'),
            'communityGuidelines' => "Respectful communication & collaboration required.",
            'verificationProcess' => "Your chapter leader reviews your profile and approves membership.",
            'jobPostingGuidelines' => "Only post verified, paid and contract-clear jobs."
        ], ResponseInterface::HTTP_OK);
    }

    // POST /policies/accept
    public function accept()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        if (!$authHeader) return $this->failUnauthorized("Missing Authorization header");

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        } catch (\Exception $e) {
            return $this->failUnauthorized("Invalid token");
        }

        $userId = $decoded->id
            ?? $decoded->user_id
            ?? $decoded->uid
            ?? ($decoded->data->id ?? null)
            ?? null;

        if (! $userId) return $this->failUnauthorized("Token missing user ID");

        $data = $this->request->getJSON(true);

        if (!isset($data['termsAccepted']) || $data['termsAccepted'] !== true) {
            return $this->failValidationErrors("termsAccepted must be true");
        }

        $acceptedAt = date('Y-m-d H:i:s');

        $this->userModel->update($userId, [
            'policies_accepted_at' => $acceptedAt
        ]);

        return $this->respond(['acceptedAt' => $acceptedAt]);
    }
}

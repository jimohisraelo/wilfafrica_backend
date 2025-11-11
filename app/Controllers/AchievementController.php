<?php

namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use App\Models\AchievementModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AchievementController extends ResourceController
{
     /**
     * @var IncomingRequest
     */
    protected $request;
    
    protected $achievementModel;
    protected $jwtKey;

    public function __construct()
    {
        $this->achievementModel = new AchievementModel();
        $this->jwtKey = getenv('JWT_SECRET') ?: 'your_jwt_secret_key';
    }

    private function getUserIdFromToken()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        if (!$authHeader) return null;

        $token = trim(str_replace('Bearer', '', $authHeader));
        if (!$token) return null;

        try {
            $decoded = JWT::decode($token, new Key($this->jwtKey, 'HS256'));
            return $decoded->uid ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // GET /profiles/me/achievements
    public function list()
    {
        $userId = $this->getUserIdFromToken();
        if (!$userId) return $this->failUnauthorized();

        $items = $this->achievementModel->where('user_id', $userId)->findAll();
        return $this->respond(['achievements' => $items]);
    }

    // POST /profiles/me/achievements
    public function add()
    {
        $userId = $this->getUserIdFromToken();
        if (!$userId) return $this->failUnauthorized();

        $data = $this->request->getJSON(true);
        $data['user_id'] = $userId;

        $id = $this->achievementModel->insert($data);
        return $this->respondCreated(['achievement' => $this->achievementModel->find($id)]);
    }
}

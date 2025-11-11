<?php

namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use App\Models\PortfolioModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class PortfolioController extends ResourceController
{
     /**
     * @var IncomingRequest
     */
    protected $request;
    protected $portfolioModel;
    protected $jwtKey;

    public function __construct()
    {
        $this->portfolioModel = new PortfolioModel();
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

    // GET /profiles/me/portfolio
    public function list()
    {
        $userId = $this->getUserIdFromToken();
        if (!$userId) return $this->failUnauthorized();

        $items = $this->portfolioModel->where('user_id', $userId)->findAll();
        return $this->respond(['items' => $items]);
    }

    // POST /profiles/me/portfolio
    public function add()
    {
        $userId = $this->getUserIdFromToken();
        if (!$userId) return $this->failUnauthorized();

        $data = $this->request->getJSON(true);
        $data['user_id'] = $userId;

        $id = $this->portfolioModel->insert($data);
        return $this->respondCreated(['item' => $this->portfolioModel->find($id)]);
    }
}

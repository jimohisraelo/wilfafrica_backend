<?php

namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use App\Models\ExperienceModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ExperienceController extends ResourceController
{
     /**
     * @var IncomingRequest
     */
    protected $request;

    protected $experienceModel;
    protected $jwtKey;

    public function __construct()
    {
        $this->experienceModel = new ExperienceModel();
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

    // GET /profiles/me/experience
    public function list()
    {
        $userId = $this->getUserIdFromToken();
        if (!$userId) return $this->failUnauthorized();

        $items = $this->experienceModel->where('user_id', $userId)->findAll();
        return $this->respond(['experience' => $items]);
    }

    // POST /profiles/me/experience
    public function add()
    {
        $userId = $this->getUserIdFromToken();
        if (!$userId) return $this->failUnauthorized();

        $data = $this->request->getJSON(true);
        $data['user_id'] = $userId;

        $id = $this->experienceModel->insert($data);
        $item = $this->experienceModel->find($id);

        return $this->respondCreated(['item' => $item]);
    }

    // PUT /profiles/me/experience/:id
    public function update($id = null)
    {
        $userId = $this->getUserIdFromToken();
        if (!$userId) return $this->failUnauthorized();

        $item = $this->experienceModel->where('user_id', $userId)->find($id);
        if (!$item) return $this->failNotFound('Experience not found');

        $data = $this->request->getJSON(true);
        $this->experienceModel->update($id, $data);

        return $this->respond(['item' => $this->experienceModel->find($id)]);
    }

    // DELETE /profiles/me/experience/:id
    public function delete($id = null)
    {
        $userId = $this->getUserIdFromToken();
        if (!$userId) return $this->failUnauthorized();

        $item = $this->experienceModel->where('user_id', $userId)->find($id);
        if (!$item) return $this->failNotFound('Experience not found');

        $this->experienceModel->delete($id);
        return $this->respond(['message' => 'Experience deleted']);
    }
}

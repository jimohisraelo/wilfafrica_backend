<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProfileModel;

class ProfileController extends ResourceController
{
    /**
     * @var IncomingRequest
     */
    protected $request;
    protected $profileModel;

    public function __construct()
    {
        $this->profileModel = new ProfileModel();
    }

    /**
     * GET /profiles/me
     * Return current user's profile
     */
    public function me()
    {
        $userId = getUserFromToken();
        if (!$userId) return $this->failUnauthorized('Unauthorized');

        $profile = $this->profileModel->where('user_id', $userId)->first();

        if (!$profile) {
            $this->profileModel->insert(['user_id' => $userId]);
            $profile = $this->profileModel->where('user_id', $userId)->first();
        }

        return $this->respond(['profile' => $profile]);
    }

    /**
     * PUT /profiles/me
     * Update profile basics
     */
    public function update($id = null)
{
    $userId = getUserFromToken();
    if (!$userId) return $this->failUnauthorized('Unauthorized');

    $profile = $this->profileModel->where('user_id', $userId)->first();

    $data = $this->request->getJSON(true);

    $allowedFields = ['headline', 'bio', 'location', 'links'];
    $updateData = [];
    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $updateData[$field] = $data[$field];
        }
    }

    if ($profile) {
        $this->profileModel->update($profile['id'], $updateData);
    } else {
        $updateData['user_id'] = $userId;
        $this->profileModel->insert($updateData);
    }

    $updatedProfile = $this->profileModel->where('user_id', $userId)->first();
    return $this->respond(['profile' => $updatedProfile]);
}


    /**
     * POST /profiles/me/avatar
     * Upload user avatar
     */
    public function uploadAvatar()
    {
        $userId = getUserFromToken();
        if (!$userId) return $this->failUnauthorized('Unauthorized');

        $profile = $this->profileModel->where('user_id', $userId)->first();
        if (!$profile) return $this->failNotFound('Profile not found');

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->fail('No valid file uploaded');
        }

        // Ensure uploads directory exists
        $uploadPath = WRITEPATH . 'uploads/avatars/';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        $avatarUrl = base_url('writable/uploads/avatars/' . $newName);

        $this->profileModel->update($profile['id'], ['avatar' => $avatarUrl]);

        return $this->respond(['avatarUrl' => $avatarUrl]);
    }

    /**
     * PUT /profiles/me/opentowork
     * Toggle Open-to-Work banner
     */
    public function toggleOpenToWork()
    {
        $userId = getUserFromToken();
        if (!$userId) return $this->failUnauthorized('Unauthorized');

        $profile = $this->profileModel->where('user_id', $userId)->first();
        if (!$profile) return $this->failNotFound('Profile not found');

        $data = $this->request->getJSON(true);

        $status = $data['status'] ?? 0;
        $note = $data['note'] ?? null;

        $updateData = [
            'open_to_work' => $status,
            'note' => $note
        ];

        $this->profileModel->update($profile['id'], $updateData);

        return $this->respond(['openToWork' => $updateData]);
    }
}

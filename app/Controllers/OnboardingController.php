<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\OnboardingModel;
use App\Models\RoleModel;
use App\Models\SpecializationModel;
use App\Models\ChapterModel;

class OnboardingController extends ResourceController
{
    protected $request;
    protected $onboardingModel;
    protected $roleModel;
    protected $specializationModel;
    protected $chapterModel;

    public function __construct()
    {
        $this->onboardingModel = new OnboardingModel();
        $this->roleModel = new RoleModel();
        $this->specializationModel = new SpecializationModel();
        $this->chapterModel = new ChapterModel();
    }

    private function user()
    {
        $uid = getUserFromToken(); // Assumes you have this helper
        if (!$uid) return null;
        return $this->onboardingModel->find($uid);
    }

    /* STEP 1: Start Onboarding */
    public function start()
    {
        $user = $this->user();
        if (!$user) return $this->failUnauthorized();

        $this->onboardingModel->update($user['id'], [
            'onboarding_step' => 'roles',
            'onboarding_progress' => 10
        ]);

        return $this->respond([
            'nextStep' => 'roles',
            'progress' => 10
        ]);
    }

    /* STEP 2: Set Roles + Primary Role */
    public function setRoles()
    {
        $user = $this->user();
        if (!$user) return $this->failUnauthorized();

        $data = $this->request->getJSON(true);
        $roles = $data['roles'] ?? [];
        $primaryRole = $data['primaryRole'] ?? null;

        if (empty($roles)) {
            return $this->failValidationErrors("At least one role must be selected.");
        }

        // Validate that primary role is among selected roles
        if ($primaryRole && !in_array($primaryRole, $roles)) {
            return $this->failValidationErrors("Primary role must be one of the selected roles.");
        }

        // Validate roles exist in DB
        $validRoles = $this->roleModel->whereIn('id', $roles)->findAll();
        if (count($validRoles) !== count($roles)) {
            return $this->failValidationErrors("One or more roles are invalid.");
        }

        $this->onboardingModel->update($user['id'], [
            'roles' => json_encode($roles),
            'primary_role' => $primaryRole ?? $roles[0],
            'onboarding_step' => 'specializations',
            'onboarding_progress' => 20
        ]);

        return $this->respond([
            'message' => 'Roles saved successfully.',
            'nextStep' => 'specializations',
            'progress' => 20
        ]);
    }

    /* STEP 3: Set Specializations */
public function setSpecializations()
{
    $user = $this->user();
    if (!$user) return $this->failUnauthorized();

    $data = $this->request->getJSON(true);

    if (!isset($data['specializations']) || !is_array($data['specializations'])) {
        return $this->failValidationErrors("Specializations must be provided as a JSON object.");
    }

    $this->onboardingModel->update($user['id'], [
        'specializations' => json_encode($data['specializations']),
        'onboarding_step' => 'chapter',
        'onboarding_progress' => 35
    ]);

    return $this->respond([
        'message' => "Specializations saved.",
        'nextStep' => 'chapter',
        'progress' => 35
    ]);
}


    /* STEP 4: Pick Chapter */
    public function pickChapter()
    {
        $user = $this->user();
        if (!$user) return $this->failUnauthorized();

        $data = $this->request->getJSON(true);
        $chapterId = $data['chapterId'] ?? null;

        if (!$chapterId || !$this->chapterModel->find($chapterId)) {
            return $this->failValidationErrors("Invalid chapter selected.");
        }

        $this->onboardingModel->update($user['id'], [
            'chapter_id' => $chapterId,
            'join_status' => 'pending_review',
            'onboarding_step' => 'links_or_cv',
            'onboarding_progress' => 50
        ]);

        return $this->respond([
            'nextStep' => 'links_or_cv',
            'progress' => 50
        ]);
    }

    /* STEP 5: Upload Resume */
    public function uploadCV()
    {
        $user = $this->user();
        if (!$user) return $this->failUnauthorized();

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) return $this->fail('Invalid file.');

        $path = WRITEPATH . 'uploads/cv/';
        if (!is_dir($path)) mkdir($path, 0755, true);

        $name = $file->getRandomName();
        $file->move($path, $name);

        $resumeUrl = base_url('writable/uploads/cv/' . $name);

        $this->onboardingModel->update($user['id'], [
            'resume_url' => $resumeUrl,
            'onboarding_step' => 'survey',
            'onboarding_progress' => 70
        ]);

        return $this->respond([
            'cvUrl' => $resumeUrl,
            'nextStep' => 'survey',
            'progress' => 70
        ]);
    }

    /* STEP 6: Add Links */
    public function addLinks()
    {
        $user = $this->user();
        if (!$user) return $this->failUnauthorized();

        $data = $this->request->getJSON(true);

        $this->onboardingModel->update($user['id'], [
            'linkedin_url' => $data['linkedinUrl'] ?? null,
            'imdb_url' => $data['imdbUrl'] ?? null,
            'website_url' => $data['websiteUrl'] ?? null,
            'onboarding_step' => 'survey',
            'onboarding_progress' => 70
        ]);

        return $this->respond([
            'nextStep' => 'survey',
            'progress' => 70
        ]);
    }

    /* STEP 7: Submit Survey */
    public function submitSurvey()
    {
        $user = $this->user();
        if (!$user) return $this->failUnauthorized();

        $this->onboardingModel->update($user['id'], [
            'survey_submitted' => 1,
            'onboarding_step' => 'policies',
            'onboarding_progress' => 85
        ]);

        return $this->respond([
            'nextStep' => 'policies',
            'progress' => 85
        ]);
    }

    /* STEP 8: Accept Policies */
    public function acceptPolicies()
    {
        $user = $this->user();
        if (!$user) return $this->failUnauthorized();

        $this->onboardingModel->update($user['id'], [
            'policies_accepted_at' => date('Y-m-d H:i:s'),
            'onboarding_step' => 'complete',
            'onboarding_progress' => 95
        ]);

        return $this->respond([
            'nextStep' => 'complete',
            'progress' => 95
        ]);
    }

    /* STEP 9: Complete Onboarding */
    public function complete()
    {
        $user = $this->user();
        if (!$user) return $this->failUnauthorized();

        if (!$user['resume_url'] && !$user['linkedin_url']) {
            return $this->fail("You must upload a CV or provide LinkedIn.");
        }

        $this->onboardingModel->update($user['id'], [
            'is_onboarding_complete' => 1,
            'onboarding_progress' => 100
        ]);

        return $this->respond([
            'activated' => true,
            'message' => "Welcome to the platform!"
        ]);
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class OnboardingModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
    'onboarding_step', 'roles', 'primary_role', 'chapter_id', 'onboarding_progress',
    'specializations',
    'resume_url', 'linkedin_url', 'imdb_url', 'website_url', 'survey_submitted',
    'policies_accepted_at', 'is_onboarding_complete', 'join_status'
];

    protected $returnType = 'array';
}

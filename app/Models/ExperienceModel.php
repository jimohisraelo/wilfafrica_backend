<?php
namespace App\Models;

use CodeIgniter\Model;

class ExperienceModel extends Model
{
    protected $table = 'experience';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'role',
        'project',
        'org',
        'from_date',
        'to_date',
        'credits',
        'media'
    ];
}

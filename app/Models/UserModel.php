<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'email', 'password', 'first_name', 'last_name', 'provider',
        'provider_id', 'verification_code', 'is_verified',
        'policies_accepted_at'
    ];
}

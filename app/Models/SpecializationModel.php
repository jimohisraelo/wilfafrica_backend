<?php

namespace App\Models;

use CodeIgniter\Model;

class SpecializationModel extends Model
{
    protected $table = 'specializations';
    protected $primaryKey = 'id';
    protected $allowedFields = ['role_id', 'name',];
    protected $returnType = 'array';
}

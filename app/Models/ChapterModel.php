<?php

namespace App\Models;

use CodeIgniter\Model;

class ChapterModel extends Model
{
    protected $table = 'chapters';
    protected $primaryKey = 'id';
    protected $allowedFields = ['country', 'name'];
    protected $returnType = 'array';
}

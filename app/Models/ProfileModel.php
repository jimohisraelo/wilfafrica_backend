<?php
namespace App\Models;

use CodeIgniter\Model;

class ProfileModel extends Model
{
    protected $table = 'profiles';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'headline', 'bio', 'location', 'links', 'avatar', 'open_to_work', 'note'];
}

<?php

// RolesSeeder.php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'Producer'],
            ['name' => 'Director'],
            ['name' => 'Writer'],
            ['name' => 'Actress'],
            ['name' => 'Crew'],
            ['name' => 'Entertainment Business'],
        ];
        $this->db->table('roles')->insertBatch($roles);

        $specializations = [
            ['role_id'=>3,'name'=>'TV Writing'],
            ['role_id'=>3,'name'=>'Film Writing'],
            ['role_id'=>3,'name'=>'Both TV & Film'],
            ['role_id'=>5,'name'=>'Cinematographer'],
            ['role_id'=>5,'name'=>'Editor'],
            ['role_id'=>5,'name'=>'Composer'],
            ['role_id'=>5,'name'=>'Sound Designer'],
            ['role_id'=>5,'name'=>'Production Designer'],
            ['role_id'=>5,'name'=>'Costume Designer'],
            ['role_id'=>5,'name'=>'Makeup Artist'],
            ['role_id'=>5,'name'=>'Gaffer'],
            ['role_id'=>6,'name'=>'Entertainment Law'],
            ['role_id'=>6,'name'=>'Distribution'],
            ['role_id'=>6,'name'=>'Finance'],
            ['role_id'=>6,'name'=>'Marketing'],
            ['role_id'=>6,'name'=>'Representation'],
            ['role_id'=>6,'name'=>'Public Relations'],
        ];
        $this->db->table('specializations')->insertBatch($specializations);
    }
}

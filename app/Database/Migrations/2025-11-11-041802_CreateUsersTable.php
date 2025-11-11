<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'email'                 => ['type' => 'VARCHAR', 'constraint' => 255],
            'password'              => ['type' => 'VARCHAR', 'constraint' => 255],
            'first_name'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'last_name'             => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'provider'              => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'provider_id'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'verification_code'     => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'is_verified'           => ['type' => 'TINYINT', 'default' => 0],
            'roles'                 => ['type' => 'TEXT', 'null' => true],
            'primary_role'          => ['type' => 'INT', 'null' => true],
            'chapter_id'            => ['type' => 'INT', 'null' => true],
            'specializations'       => ['type' => 'TEXT', 'null' => true],
            'onboarding_step'       => ['type' => 'INT', 'default' => 1],
            'onboarding_progress'   => ['type' => 'INT', 'default' => 0],
            'resume_url'            => ['type' => 'TEXT', 'null' => true],
            'linkedin_url'          => ['type' => 'TEXT', 'null' => true],
            'imdb_url'              => ['type' => 'TEXT', 'null' => true],
            'website_url'           => ['type' => 'TEXT', 'null' => true],
            'survey_submitted'      => ['type' => 'TINYINT', 'default' => 0],
            'policies_accepted_at'  => ['type' => 'DATETIME', 'null' => true],
            'is_onboarding_complete'=> ['type' => 'TINYINT', 'default' => 0],
            'join_status'           => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'pending'],
            'created_at'            => ['type' => 'DATETIME', 'null' => true],
            'updated_at'            => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}

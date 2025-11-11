<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProfilesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'unsigned' => true],
            'headline'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'bio'         => ['type' => 'TEXT', 'null' => true],
            'location'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'links'       => ['type' => 'TEXT', 'null' => true],
            'avatar'      => ['type' => 'TEXT', 'null' => true],
            'open_to_work'=> ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'note'        => ['type' => 'TEXT', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('profiles');
    }

    public function down()
    {
        $this->forge->dropTable('profiles');
    }
}

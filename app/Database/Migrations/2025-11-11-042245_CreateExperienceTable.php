<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExperienceTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],

            'project'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'role'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'org'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],

            'from_date'  => ['type' => 'DATE', 'null' => true],
            'to_date'    => ['type' => 'DATE', 'null' => true],

            'credits'    => ['type' => 'TEXT', 'null' => true],
            'media'      => ['type' => 'TEXT', 'null' => true],

            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('experience');
    }

    public function down()
    {
        $this->forge->dropTable('experience');
    }
}

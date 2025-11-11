<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAchievementsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],

            'title'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'year'       => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'issuer'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'proofUrl'   => ['type' => 'TEXT', 'null' => true],

            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('achievements');
    }

    public function down()
    {
        $this->forge->dropTable('achievements');
    }
}

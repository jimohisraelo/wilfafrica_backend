<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSpecializationsTable extends Migration
{
    public function up()
{
    $this->forge->addField([
        'id' => ['type' => 'INT', 'auto_increment' => true],
        'role_id' => ['type' => 'INT'],
        'name' => ['type' => 'VARCHAR', 'constraint' => 150],
        'created_at' => ['type' => 'DATETIME', 'null' => true],
        'updated_at' => ['type' => 'DATETIME', 'null' => true],
    ]);
    $this->forge->addKey('id', true);
    $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
    $this->forge->createTable('specializations');
}

public function down()
{
    $this->forge->dropTable('specializations');
}

}

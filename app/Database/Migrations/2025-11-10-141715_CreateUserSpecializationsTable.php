<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserSpecializationsTable extends Migration
{
    public function up()
{
    $this->forge->addField([
        'id' => ['type' => 'INT', 'auto_increment' => true],
        'user_id' => ['type' => 'INT'],
        'specialization_id' => ['type' => 'INT'],
    ]);
    $this->forge->addKey('id', true);
    $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('specialization_id', 'specializations', 'id', 'CASCADE', 'CASCADE');
    $this->forge->createTable('user_specializations');
}

public function down()
{
    $this->forge->dropTable('user_specializations');
}

}

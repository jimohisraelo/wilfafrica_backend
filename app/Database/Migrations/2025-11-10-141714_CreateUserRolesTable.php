<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserRolesTable extends Migration
{
    public function up()
{
    $this->forge->addField([
        'id' => ['type' => 'INT', 'auto_increment' => true],
        'user_id' => ['type' => 'INT'],
        'role_id' => ['type' => 'INT'],
        'is_primary' => ['type' => 'TINYINT', 'default' => 0],
    ]);
    $this->forge->addKey('id', true);
    $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
    $this->forge->createTable('user_roles');
}

public function down()
{
    $this->forge->dropTable('user_roles');
}

}

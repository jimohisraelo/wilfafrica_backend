<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChaptersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'country' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'chapter_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('chapters');
    }

    public function down()
    {
        $this->forge->dropTable('chapters');
    }
}

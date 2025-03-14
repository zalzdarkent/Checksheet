<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TbChecksheet extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'mesin' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'bulan' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'departemen' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'seksi' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tb_checksheet');
    }

    public function down()
    {
        $this->forge->dropTable('tb_checksheet');
    }
}

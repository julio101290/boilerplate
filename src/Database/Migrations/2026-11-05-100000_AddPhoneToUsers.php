<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhoneToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'null'       => true,
                'default'    => null,
                'after'      => 'lastname',
            ],
        ];

        if (!$this->db->fieldExists('phone', 'users')) {
            $this->forge->addColumn('users', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('phone', 'users')) {
            $this->forge->dropColumn('users', 'phone');
        }
    }
}
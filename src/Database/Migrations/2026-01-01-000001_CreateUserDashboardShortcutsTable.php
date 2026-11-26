<?php

namespace julio101290\boilerplate\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserDashboardShortcutsTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'url' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'fas fa-link',
            ],
            'display_type' => [
                'type'       => 'ENUM',
                'constraint' => ['shortcut', 'tab', 'both'],
                'default'    => 'shortcut',
            ],
            'is_default_tab' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('user_dashboard_shortcuts', true);
    }

    public function down()
    {
        $this->forge->dropTable('user_dashboard_shortcuts', true);
    }
}
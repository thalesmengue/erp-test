<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_coupons_table extends CI_Migration
{
    public function up(): void
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => FALSE,
                'unique' => TRUE
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['percentage', 'fixed'],
                'null' => FALSE,
                'comment' => 'percentage = %, fixed = fixed amount in cents'
            ],
            'value' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE,
                'comment' => 'For percentage: value in % (ex: 10 = 10%), for fixed: value in cents'
            ],
            'minimum_amount' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
                'comment' => 'Minimum order amount in cents to apply coupon'
            ],
            'usage_limit' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
                'comment' => 'Maximum number of uses (NULL = unlimited)'
            ],
            'used_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default' => 'active',
                'null' => FALSE
            ],
			'created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
			'updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('code', TRUE);
        $this->dbforge->add_key('status');
        $this->dbforge->add_key('expires_at');

        $this->dbforge->create_table('coupons');
    }

    public function down(): void
    {
        $this->dbforge->drop_table('coupons');
    }
}

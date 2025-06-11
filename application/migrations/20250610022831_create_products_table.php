<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_products_table extends CI_Migration
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
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => FALSE
			],
			'price' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => FALSE,
				'comment' => 'Price in cents (ex: R$ 10,99 = 1099)'
			],
			'description' => [
				'type' => 'TEXT',
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
		$this->dbforge->add_key('name');
		$this->dbforge->add_key('status');

		$this->dbforge->create_table('products');

		echo "Table 'products' created successfully!\n";
	}

	public function down(): void
	{
		$this->dbforge->drop_table('products');
		echo "Table 'products' dropped!\n";
	}
}

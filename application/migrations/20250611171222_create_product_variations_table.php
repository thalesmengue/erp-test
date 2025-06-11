<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_product_variations_table extends CI_Migration
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
			'product_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => FALSE
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => FALSE,
				'comment' => 'Variation name (ex: Red, Size M, 128GB)'
			],
			'type' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
				'null' => FALSE,
				'comment' => 'Variation type (ex: color, size, storage)'
			],
			'price_adjustment' => [
				'type' => 'INT',
				'constraint' => 11,
				'null' => FALSE,
				'default' => 0,
				'comment' => 'Price adjustment in cents (can be negative)'
			],
			'sku' => [
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => TRUE,
				'comment' => 'Stock Keeping Unit'
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
		$this->dbforge->add_key('product_id');
		$this->dbforge->add_key(['product_id', 'type']);
		$this->dbforge->add_key('sku');

		$this->dbforge->create_table('product_variations');

		$this->db->query("
            ALTER TABLE product_variations 
            ADD CONSTRAINT fk_variations_product_id 
            FOREIGN KEY (product_id) REFERENCES products(id) 
            ON DELETE CASCADE ON UPDATE CASCADE
        ");
	}

	public function down(): void
	{
		$this->db->query("ALTER TABLE product_variations DROP FOREIGN KEY fk_variations_product_id");
		$this->dbforge->drop_table('product_variations');
	}
}

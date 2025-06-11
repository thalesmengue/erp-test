<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_stock_table extends CI_Migration
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
				'null' => TRUE
			],
			'variation_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => TRUE
			],
			'quantity' => [
				'type' => 'INT',
				'constraint' => 11,
				'null' => FALSE
			],
			'reserved_quantity' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => FALSE,
				'default' => 0
			],
			'created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
			'updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
		]);

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('product_id');
		$this->dbforge->add_key('variation_id');

		$this->dbforge->create_table('stock');

		$this->db->query("
            ALTER TABLE stock 
            ADD CONSTRAINT fk_stock_product 
            FOREIGN KEY (product_id) REFERENCES products(id) 
            ON DELETE CASCADE
        ");

		$this->db->query("
            ALTER TABLE stock 
            ADD CONSTRAINT fk_stock_variation 
            FOREIGN KEY (variation_id) REFERENCES product_variations(id) 
            ON DELETE CASCADE
        ");
	}

	public function down(): void
	{
		$this->db->query("ALTER TABLE stock DROP FOREIGN KEY fk_stock_product");
		$this->db->query("ALTER TABLE stock DROP FOREIGN KEY fk_stock_variation");
		$this->dbforge->drop_table('stock');
	}
}

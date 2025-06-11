<?php
// application/migrations/20250610150000_create_order_items_table.php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_order_items_table extends CI_Migration
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
			'order_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => FALSE
			],
			'product_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => FALSE
			],
			'variation_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => TRUE,
				'comment' => 'Product variation if selected (NULL for products without variations)'
			],
			'product_name' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => FALSE,
				'comment' => 'Store product name at time of purchase'
			],
			'variation_details' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => TRUE,
				'comment' => 'Store variation details at time of purchase (Color: Red, Size: XL)'
			],
			'unit_price' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => FALSE,
				'comment' => 'Final unit price in cents (base price + variation adjustment)'
			],
			'quantity' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => FALSE
			],
			'total_price' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => FALSE,
				'comment' => 'unit_price * quantity in cents'
			],
			'created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
			'updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
		]);

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('order_id');
		$this->dbforge->add_key('product_id');
		$this->dbforge->add_key('variation_id');

		$this->dbforge->create_table('order_items');

		$this->db->query("
            ALTER TABLE order_items 
            ADD CONSTRAINT fk_order_items_order_id 
            FOREIGN KEY (order_id) REFERENCES orders(id) 
            ON DELETE CASCADE ON UPDATE CASCADE
        ");

		$this->db->query("
            ALTER TABLE order_items 
            ADD CONSTRAINT fk_order_items_product_id 
            FOREIGN KEY (product_id) REFERENCES products(id) 
            ON DELETE CASCADE ON UPDATE CASCADE
        ");

		$this->db->query("
            ALTER TABLE order_items 
            ADD CONSTRAINT fk_order_items_variation_id 
            FOREIGN KEY (variation_id) REFERENCES product_variations(id) 
            ON DELETE CASCADE ON UPDATE CASCADE
        ");
	}

	public function down(): void
	{
		$this->db->query("ALTER TABLE order_items DROP FOREIGN KEY fk_order_items_order_id");
		$this->db->query("ALTER TABLE order_items DROP FOREIGN KEY fk_order_items_product_id");
		$this->db->query("ALTER TABLE order_items DROP FOREIGN KEY fk_order_items_variation_id");
		$this->dbforge->drop_table('order_items');
	}
}

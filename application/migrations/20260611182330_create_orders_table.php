<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_orders_table extends CI_Migration
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
			'order_number' => [
				'type' => 'VARCHAR',
				'constraint' => 20,
				'null' => FALSE,
				'comment' => 'Human readable order number (ex: ORD-2025-001)'
			],
			'customer_name' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => FALSE
			],
			'customer_email' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => FALSE
			],
			'customer_phone' => [
				'type' => 'VARCHAR',
				'constraint' => 20,
				'null' => TRUE
			],
			'address_cep' => [
				'type' => 'VARCHAR',
				'constraint' => 10,
				'null' => FALSE
			],
			'address_street' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => FALSE
			],
			'address_number' => [
				'type' => 'VARCHAR',
				'constraint' => 20,
				'null' => FALSE
			],
			'address_complement' => [
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => TRUE
			],
			'address_neighborhood' => [
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => FALSE
			],
			'address_city' => [
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => FALSE
			],
			'address_state' => [
				'type' => 'VARCHAR',
				'constraint' => 2,
				'null' => FALSE
			],
			'subtotal' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => FALSE,
				'comment' => 'Subtotal in cents (before discounts)'
			],
			'shipping_cost' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => FALSE,
				'default' => 0,
				'comment' => 'Shipping cost in cents'
			],
			'coupon_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => TRUE
			],
			'coupon_code' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
				'null' => TRUE,
				'comment' => 'Store coupon code for reference'
			],
			'discount_amount' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => FALSE,
				'default' => 0,
				'comment' => 'Discount amount in cents'
			],
			'total' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => FALSE,
				'comment' => 'Final total in cents (subtotal + shipping - discount)'
			],
			'status' => [
				'type' => 'ENUM',
				'constraint' => ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'],
				'default' => 'pending',
				'null' => FALSE
			],
			'notes' => [
				'type' => 'TEXT',
				'null' => TRUE
			],
			'created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
			'updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
		]);

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('order_number', TRUE);
		$this->dbforge->add_key('customer_email');
		$this->dbforge->add_key('status');
		$this->dbforge->add_key('created_at');
		$this->dbforge->add_key('coupon_id');

		$this->dbforge->create_table('orders');

		$this->db->query("
            ALTER TABLE orders 
            ADD CONSTRAINT fk_orders_coupon_id 
            FOREIGN KEY (coupon_id) REFERENCES coupons(id) 
            ON DELETE SET NULL ON UPDATE CASCADE
        ");
	}

	public function down(): void
	{
		$this->db->query("ALTER TABLE orders DROP FOREIGN KEY fk_orders_coupon_id");
		$this->dbforge->drop_table('orders');
	}
}

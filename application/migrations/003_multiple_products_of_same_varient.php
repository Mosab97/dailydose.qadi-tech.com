<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_multiple_products_of_same_varient extends CI_Migration
{
    public function up()
    {
         
          /* adding new fields in cart table */
        $fields = array(
            'addon_ids' => array(
                'type' => 'VARCHAR',
                'constraint' => '1024',
                'null' => TRUE,    
                'after' => 'product_variant_id'
            ),
            'addon_variant_combination' => array(
                'type' => 'MEDIUMTEXT',
                'null' => FALSE,
                'after' => 'is_saved_for_later'
            ),
        );
        $this->dbforge->add_column('cart', $fields);

         /* adding new fields in cart_add_ons table */
          $fields = array(
            'cart_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => FALSE,    
                'after' => 'user_id'
            ),
            'addon_variant_combination' => array(
                'type' => 'MEDIUMTEXT',
                'null' => FALSE,
                'after' => 'add_on_id'
            ),
        );
        $this->dbforge->add_column('cart_add_ons', $fields);

         /* Modifying field in users table */

         $fields = array(
            'serviceable_city' => array(
                'type' => 'TEXT',
                'null' => TRUE,
                'default' => NULL,
            ),
        );

        // Modify the column
        $this->dbforge->modify_column('users', $fields);

    }
    public function down()
    {
        $this->dbforge->drop_column('cart', 'addon_ids');
        $this->dbforge->drop_column('cart', 'addon_variant_combination');
        $this->dbforge->drop_column('cart_add_ons', 'cart_id');
        $this->dbforge->drop_column('cart_add_ons', 'addon_variant_combination');
        $this->dbforge->drop_column('users', 'serviceable_city');
    }
}

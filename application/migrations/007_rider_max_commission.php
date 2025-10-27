<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_rider_max_commission extends CI_Migration
{
    public function up()
    {
        // Add `max_commission` column to `users` table
        $fields_users = array(
            'max_commission' => array(
                'type'       => 'DOUBLE',
                'null'       => TRUE,
                'default'    => NULL,
                'comment'    => 'used for riders only when commission method is percentage',
                'after'      => 'commission'
            )
        );
        $this->dbforge->add_column('users', $fields_users);

        // Add `is_rider_otp_setting_on` column to `orders` table
        $fields_orders = array(
            'is_rider_otp_setting_on' => array(
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => TRUE,
                'default'    => NULL,
                'after'      => 'otp'
            )
        );
        $this->dbforge->add_column('orders', $fields_orders);

    // Add `platform` column to `users` table
        $fields = array(
            'platform' => array(
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => TRUE,
                'default'    => NULL,
                'after'      => 'created_at'
            )
        );
        $this->dbforge->add_column('users', $fields);
    }

    public function down()
    {
        // Remove columns
        $this->dbforge->drop_column('users', 'max_commission');
        $this->dbforge->drop_column('orders', 'is_rider_otp_setting_on');
        $this->dbforge->drop_column('users', 'platform');
    }
}

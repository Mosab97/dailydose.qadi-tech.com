<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_rider_registration extends CI_Migration
{
    public function up()
    {

        /* adding new fields in users table */
        $fields = array(
            'accept_orders' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '1',
                'null' => FALSE,
                'after' => 'active'
            ),

        );
        $this->dbforge->add_column('users', $fields);
    }
    public function down()
    {
        $this->dbforge->drop_column('users', 'accept_orders');
    }
}

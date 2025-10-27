<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_multiple_branch_login extends CI_Migration
{
    public function up()
    {
       
        /* adding new fields in rider_rating table */
          $fields = array(
            'branch_ids' => array(
                'type' => 'TEXT',
                'null' => TRUE,    
                'after' => 'role'
            ),
           
        );
        $this->dbforge->add_column('user_permissions', $fields);

        /* adding new fields in users table */
          $fields = array(
            'web_fcm_id' => array(
                'type' => 'TEXT',
                'null' => TRUE,    
                'after' => 'fcm_id'
            ),
           
        );
        $this->dbforge->add_column('users', $fields);

    }
    public function down()
    {
       
        $this->dbforge->drop_column('user_permissions', 'branch_ids');
        $this->dbforge->drop_column('users', 'web_fcm_id');

       
    }
}

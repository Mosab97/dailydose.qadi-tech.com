<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_sms_gateway extends CI_Migration
{
    public function up()
    {
         /* adding new table otps */
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'auto_increment' => TRUE,
                'NULL'           => FALSE
            ],
            'mobile' => [
                'type'           => 'VARCHAR',
                'constraint'     => '20',
                'NULL'           => FALSE
            ],
            'otp' => [
                'type'           => 'VARCHAR',
                'constraint'     => '256',
                'NULL'           => FALSE
            ],
            'varified' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE,
                'default'        => '0',
                'comment' => '1 : verify | 0: not verify	'
            ],
            'created_at' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE,
            ],

        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('otps');

         /* adding new fields in settings table */
          $data = array(
          
            array(
                'variable' => 'authentication_settings',
                'value' => '{"authentication_method":"firebase"}',
            ),
          
         );
        $this->db->insert_batch('settings', $data);

          /* adding new fields in offers table */
        $fields = array(
            'start_date' => array(
                'type' => 'VARCHAR',
                'constraint' => '28',
                'null' => FALSE,    
                'after' => 'branch_id'
            ),
            'end_date' => array(
                'type' => 'VARCHAR',
                'constraint' => '28',
                'null' => FALSE,
                'after' => 'start_date'
            ),
        );
        $this->dbforge->add_column('offers', $fields);

         /* adding new fields in partner_data table */
          $fields = array(
            'global_branch_time' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'default' => '1',
                'null' => FALSE,    
                'after' => 'default_branch'
            ),
        );
        $this->dbforge->add_column('branch', $fields);

    }
    public function down()
    {
        $this->dbforge->drop_table('otps');
        $this->dbforge->drop_column('offers', 'start_date');
        $this->dbforge->drop_column('offers', 'end_date');
        $this->dbforge->drop_column('global_branch_time', 'branch');
    }
}

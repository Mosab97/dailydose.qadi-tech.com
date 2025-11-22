<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_attribute_value_translations extends CI_Migration
{
    public function up()
    {
        // Create attribute_value_translations table
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => FALSE,
                'auto_increment' => TRUE,
            ],
            'attribute_value_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => FALSE,
            ],
            'language_code' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'value' => [
                'type' => 'VARCHAR',
                'constraint' => 1024,
            ],
            'date_created' => [
                'type' => 'TIMESTAMP',
                'null' => FALSE,
            ],
            'date_updated' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('attribute_value_id');
        $this->dbforge->add_key('language_code');
        
        $this->dbforge->create_table('attribute_value_translations', TRUE);

        // Add unique constraint for attribute_value_id + language_code combination
        $this->db->query('ALTER TABLE `attribute_value_translations` ADD UNIQUE KEY `attribute_value_lang_unique` (`attribute_value_id`, `language_code`)');
        
        // Set default timestamp for date_created
        $this->db->query('ALTER TABLE `attribute_value_translations` MODIFY `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
        
        // Set timestamp update trigger for date_updated
        $this->db->query('ALTER TABLE `attribute_value_translations` MODIFY `date_updated` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP');
    }

    public function down()
    {
        // Drop the attribute_value_translations table
        $this->dbforge->drop_table('attribute_value_translations', TRUE);
    }
}


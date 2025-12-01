<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_section_translations extends CI_Migration
{
    public function up()
    {
        // Create section_translations table
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => FALSE,
                'auto_increment' => TRUE,
            ],
            'section_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => FALSE,
            ],
            'language_code' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 512,
            ],
            'short_description' => [
                'type' => 'VARCHAR',
                'constraint' => 512,
                'null' => TRUE,
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
        $this->dbforge->add_key('section_id');
        $this->dbforge->add_key('language_code');
        
        $this->dbforge->create_table('section_translations', TRUE);

        // Add unique constraint for section_id + language_code combination
        $this->db->query('ALTER TABLE `section_translations` ADD UNIQUE KEY `section_lang_unique` (`section_id`, `language_code`)');
        
        // Set default timestamp for date_created
        $this->db->query('ALTER TABLE `section_translations` MODIFY `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
        
        // Set timestamp update trigger for date_updated
        $this->db->query('ALTER TABLE `section_translations` MODIFY `date_updated` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP');
    }

    public function down()
    {
        // Drop the section_translations table
        $this->dbforge->drop_table('section_translations', TRUE);
    }
}


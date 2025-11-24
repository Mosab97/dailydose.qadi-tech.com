<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_category_translations extends CI_Migration
{
    public function up()
    {
        // Create category_translations table
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => FALSE,
                'auto_increment' => TRUE,
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => FALSE,
            ],
            'language_code' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
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
        $this->dbforge->add_key('category_id');
        $this->dbforge->add_key('language_code');
        
        $this->dbforge->create_table('category_translations', TRUE);

        // Add unique constraint for category_id + language_code combination
        $this->db->query('ALTER TABLE `category_translations` ADD UNIQUE KEY `category_lang_unique` (`category_id`, `language_code`)');
        
        // Set default timestamp for date_created
        $this->db->query('ALTER TABLE `category_translations` MODIFY `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
        
        // Set timestamp update trigger for date_updated
        $this->db->query('ALTER TABLE `category_translations` MODIFY `date_updated` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP');
    }

    public function down()
    {
        // Drop the category_translations table
        $this->dbforge->drop_table('category_translations', TRUE);
    }
}


<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_settings_translations extends CI_Migration
{
    public function up()
    {
        // Create settings_translations table
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => FALSE,
                'auto_increment' => TRUE,
            ],
            'setting_variable' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'language_code' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'value' => [
                'type' => 'MEDIUMTEXT',
                'null' => FALSE,
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
        $this->dbforge->add_key('setting_variable');
        $this->dbforge->add_key('language_code');
        
        $this->dbforge->create_table('settings_translations', TRUE);

        // Add unique constraint for setting_variable + language_code combination
        $this->db->query('ALTER TABLE `settings_translations` ADD UNIQUE KEY `setting_lang_unique` (`setting_variable`, `language_code`)');
        
        // Set default timestamp for date_created
        $this->db->query('ALTER TABLE `settings_translations` MODIFY `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
        
        // Set timestamp update trigger for date_updated
        $this->db->query('ALTER TABLE `settings_translations` MODIFY `date_updated` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP');
    }

    public function down()
    {
        // Drop the settings_translations table
        $this->dbforge->drop_table('settings_translations', TRUE);
    }
}


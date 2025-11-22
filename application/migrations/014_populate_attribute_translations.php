<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_populate_attribute_translations extends CI_Migration
{
    public function up()
    {
        // Populate attribute_translations with existing attribute data (English as default)
        $this->db->query("
            INSERT INTO attribute_translations (attribute_id, language_code, name, date_created)
            SELECT id, 'en', name, date_created
            FROM attributes
            WHERE name IS NOT NULL AND name != ''
        ");

        // Populate attribute_value_translations with existing attribute value data (English as default)
        $this->db->query("
            INSERT INTO attribute_value_translations (attribute_value_id, language_code, value, date_created)
            SELECT id, 'en', value, NOW()
            FROM attribute_values
            WHERE value IS NOT NULL AND value != ''
        ");
    }

    public function down()
    {
        // Remove all English translations (rollback)
        $this->db->where('language_code', 'en');
        $this->db->delete('attribute_translations');

        $this->db->where('language_code', 'en');
        $this->db->delete('attribute_value_translations');
    }
}


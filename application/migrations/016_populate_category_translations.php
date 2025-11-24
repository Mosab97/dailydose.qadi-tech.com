<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_populate_category_translations extends CI_Migration
{
    public function up()
    {
        // Populate category_translations with existing category data (English as default)
        $this->db->query("
            INSERT INTO category_translations (category_id, language_code, name, date_created)
            SELECT id, 'en', name, NOW()
            FROM categories
            WHERE name IS NOT NULL AND name != ''
        ");
    }

    public function down()
    {
        // Remove all English translations (rollback)
        $this->db->where('language_code', 'en');
        $this->db->delete('category_translations');
    }
}


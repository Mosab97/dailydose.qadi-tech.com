<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_populate_initial_translations extends CI_Migration
{
    public function up()
    {
        // Populate product_translations with existing product data (English as default)
        $this->db->query("
            INSERT INTO product_translations (product_id, language_code, name, short_description, date_created)
            SELECT id, 'en', name, short_description, date_added
            FROM products
            WHERE name IS NOT NULL AND name != ''
        ");

        // Populate product_add_on_translations with existing add-on data (English as default)
        $this->db->query("
            INSERT INTO product_add_on_translations (add_on_id, language_code, title, description, date_created)
            SELECT id, 'en', title, description, date_created
            FROM product_add_ons
            WHERE title IS NOT NULL AND title != ''
        ");
    }

    public function down()
    {
        // Remove all English translations (rollback)
        $this->db->where('language_code', 'en');
        $this->db->delete('product_translations');

        $this->db->where('language_code', 'en');
        $this->db->delete('product_add_on_translations');
    }
}


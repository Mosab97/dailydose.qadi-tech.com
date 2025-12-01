<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_populate_section_translations extends CI_Migration
{
    public function up()
    {
        // Populate section_translations table with existing English data from sections table
        // This migration ensures backward compatibility by copying existing English content to translations table
        
        $this->db->query("
            INSERT INTO section_translations (section_id, language_code, title, short_description, date_created)
            SELECT 
                id as section_id,
                'en' as language_code,
                title,
                short_description,
                date_added as date_created
            FROM sections
            WHERE id NOT IN (
                SELECT section_id 
                FROM section_translations 
                WHERE language_code = 'en'
            )
        ");
    }

    public function down()
    {
        // Remove English translations that were created by this migration
        // Note: This will only remove translations that match the sections table exactly
        // More complex logic would be needed to safely identify migrated vs manually created translations
        $this->db->query("
            DELETE st FROM section_translations st
            INNER JOIN sections s ON st.section_id = s.id
            WHERE st.language_code = 'en' 
            AND st.title = s.title 
            AND (st.short_description = s.short_description OR (st.short_description IS NULL AND s.short_description IS NULL))
        ");
    }
}


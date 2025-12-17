<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_populate_settings_translations extends CI_Migration
{
    public function up()
    {
        // Populate settings_translations table with existing English data from settings table
        // This migration ensures backward compatibility by copying existing English content to translations table
        
        // Settings variables to migrate
        $settings_variables = ['contact_us', 'about_us', 'privacy_policy', 'terms_conditions'];
        
        foreach ($settings_variables as $variable) {
            $this->db->query("
                INSERT INTO settings_translations (setting_variable, language_code, value, date_created)
                SELECT 
                    variable as setting_variable,
                    'en' as language_code,
                    value,
                    NOW() as date_created
                FROM settings
                WHERE variable = '{$variable}'
                AND value IS NOT NULL
                AND value != ''
                AND NOT EXISTS (
                    SELECT 1 
                    FROM settings_translations 
                    WHERE setting_variable = '{$variable}' 
                    AND language_code = 'en'
                )
            ");
        }
    }

    public function down()
    {
        // Remove English translations that were created by this migration
        // Note: This will only remove translations that match the settings table exactly
        $settings_variables = ['contact_us', 'about_us', 'privacy_policy', 'terms_conditions'];
        
        foreach ($settings_variables as $variable) {
            $this->db->query("
                DELETE st FROM settings_translations st
                INNER JOIN settings s ON st.setting_variable = s.variable
                WHERE st.setting_variable = '{$variable}'
                AND st.language_code = 'en' 
                AND st.value = s.value
            ");
        }
    }
}


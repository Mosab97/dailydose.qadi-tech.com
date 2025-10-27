<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_add_slider_slug extends CI_Migration
{
    public function up()
    {
        // Add the 'slug' column to the 'sections' table
        $this->dbforge->add_column('sections', [
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
                'after' => 'title',
            ],
        ]);

        // Auto-generate slugs for existing rows
        $query = $this->db->get('sections');
        foreach ($query->result() as $row) {
            $slug = create_unique_slug($row->title, 'sections');
            $this->db->update('sections', ['slug' => $slug], ['id' => $row->id]);
        }

    }

    public function down()
    {
        // Remove the 'slug' column
        $this->dbforge->drop_column('sections', 'slug');
    }
}

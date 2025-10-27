<?php defined('BASEPATH') or exit('No direct script access allowed');

class Database_bakup extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper', 'download', 'file']);
        $this->load->model(['Setting_model']);
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'database-bakup';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Database | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Database | ' . $settings['app_name'];
            $this->data['about_us'] = get_settings('about_us');
            $this->data['tables'] = $this->db->list_tables();
            if (!isset($_SESSION['branch_id'])) {

                redirect('admin/branch', 'refresh');
            } else {

                $this->load->view('admin/template', $this->data);
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function backup()
    {

        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            show_error('Unauthorized access', 403);
        }

        $this->load->dbutil();

        // Get selected tables from the form submission
        $tables = $this->input->post('tables');

        // Backup preferences
        $prefs = array(
            'format'      => 'zip',
            'filename'    => 'database_backup.sql',
            'add_drop'    => TRUE,
            'add_insert'  => TRUE,
            'foreign_key_checks' => FALSE
        );

        // If no tables selected, backup the entire database
        if (empty($tables)) {
            $backup = $this->dbutil->backup($prefs);
        } else {
            $prefs['tables'] = $tables;
            $backup = $this->dbutil->backup($prefs);
        }

        // Define the file name
        $file_name = 'database_backup_' . date('Y-m-d_H-i-s') . '.zip';

        // Set headers for file download
        $this->load->helper('download');
        force_download($file_name, $backup);
    }
}

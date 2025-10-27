<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Combo_products extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation', 'upload']);
        $this->load->helper(['url', 'language', 'file']);
        $this->load->model(['product_model', 'category_model', 'rating_model']);

        if (!has_permissions('read', 'product')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        }
    }
    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = TABLES . 'manage-combo-product';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Combo Product Management | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Combo Product Management |' . $settings['app_name'];
            $this->data['categories'] = $this->category_model->get_categories();

            if (!isset($_SESSION['branch_id'])) {

                redirect('admin/branch', 'refresh');
            } else {

                $this->load->view('admin/template', $this->data);
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }


    public function create_combo_product()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'combo-products';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Add Combo | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Add Combo | ' . $settings['app_name'];
            if (isset($_GET['edit_id'])) {
                $this->data['fetched_data'] = fetch_details(['id' => $_GET['edit_id']], 'tags');
            }
            if (!isset($_SESSION['branch_id'])) {

                redirect('admin/branch', 'refresh');
            } else {

                $this->load->view('admin/template', $this->data);
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }
}

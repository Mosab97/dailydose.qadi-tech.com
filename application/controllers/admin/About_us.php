<?php
defined('BASEPATH') or exit('No direct script access allowed');


class About_us extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model('Setting_model');

        if (!has_permissions('read', 'about_us')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        }
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'about-us';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'About Us | ' . $settings['app_name'];
            $this->data['meta_description'] = 'About Us | ' . $settings['app_name'];
            
            // Load translations
            $translations = $this->Setting_model->get_setting_translations('about_us');
            
            // Ensure translations array is initialized even if empty
            if (empty($translations)) {
                $translations = [];
            }
            
            // If English translation doesn't exist, use main table value as fallback
            if (!isset($translations['en'])) {
                $about_us = get_settings('about_us');
                $translations['en'] = [
                    'value' => isset($about_us) ? $about_us : ''
                ];
            }
            
            $this->data['about_us'] = isset($translations['en']['value']) ? $translations['en']['value'] : '';
            $this->data['setting_translations'] = $translations;
            
            if (!isset($_SESSION['branch_id'])) {

                redirect('admin/branch', 'refresh');
            } else {

                $this->load->view('admin/template', $this->data);
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function update_about_us_settings()
    {

        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('update', 'about_us'), PERMISSION_ERROR_MSG, 'about_us')) {
                return false;
            }
            $this->form_validation->set_rules('about_us_input_description', 'About Us', 'trim|required|xss_clean');
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = array(
                    'about_us_input_description' => form_error('about_us_input_description'),
                );
                print_r(json_encode($this->response));
            } else {
                $about_us_input_description = strip_tags($_POST['about_us_input_description']);
                if (isset($about_us_input_description) && !empty($about_us_input_description)) {
                    // Collect translation data from POST
                    $setting_translations = [];
                    if (isset($_POST['setting_translations']) && is_array($_POST['setting_translations'])) {
                        $setting_translations = $_POST['setting_translations'];
                    }
                    
                    // Always ensure English translation is included from main field
                    if (!isset($setting_translations['en'])) {
                        $setting_translations['en'] = [];
                    }
                    if (empty($setting_translations['en']['value']) && !empty($_POST['about_us_input_description'])) {
                        $setting_translations['en']['value'] = $_POST['about_us_input_description'];
                    }
                    
                    $_POST['setting_translations'] = $setting_translations;

                    $this->Setting_model->update_about_us($_POST);
                    $this->response['error'] = false;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'System Setting Updated Successfully';
                } else {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'The About us filed is required.';
                }
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }
}

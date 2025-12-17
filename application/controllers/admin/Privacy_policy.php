<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Privacy_policy extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model('Setting_model');
    }

    public function index()
    {
        if (!has_permissions('read', 'privacy_policy')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        }
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'privacy-policy';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Privacy Policy | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Privacy Policy | ' . $settings['app_name'];
            
            // Load translations for privacy_policy
            $privacy_translations = $this->Setting_model->get_setting_translations('privacy_policy');
            if (empty($privacy_translations)) {
                $privacy_translations = [];
            }
            if (!isset($privacy_translations['en'])) {
                $privacy_policy = get_settings('privacy_policy');
                $privacy_translations['en'] = [
                    'value' => isset($privacy_policy) ? $privacy_policy : ''
                ];
            }
            $this->data['privacy_policy'] = isset($privacy_translations['en']['value']) ? $privacy_translations['en']['value'] : '';
            $this->data['privacy_policy_translations'] = $privacy_translations;
            
            // Load translations for terms_conditions
            $terms_translations = $this->Setting_model->get_setting_translations('terms_conditions');
            if (empty($terms_translations)) {
                $terms_translations = [];
            }
            if (!isset($terms_translations['en'])) {
                $terms_condition = get_settings('terms_conditions');
                $terms_translations['en'] = [
                    'value' => isset($terms_condition) ? $terms_condition : ''
                ];
            }
            $this->data['terms_n_condition'] = isset($terms_translations['en']['value']) ? $terms_translations['en']['value'] : '';
            $this->data['terms_n_conditions_translations'] = $terms_translations;
            
            if (!isset($_SESSION['branch_id'])) {

                redirect('admin/branch', 'refresh');
            } else {

                $this->load->view('admin/template', $this->data);
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }


    public function update_privacy_policy_settings()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('update', 'privacy_policy'), PERMISSION_ERROR_MSG, 'privacy_policy')) {
                return false;
            }

            $this->form_validation->set_rules('terms_n_conditions_input_description', 'Terms and Condition Description', 'trim|required|xss_clean');
            $this->form_validation->set_rules('privacy_policy_input_description', 'Privay Policy Description', 'trim|required|xss_clean');


            if (!$this->form_validation->run()) {

                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = array(
                    'terms_n_conditions_input_description' => form_error('terms_n_conditions_input_description'),
                    'privacy_policy_input_description' => form_error('privacy_policy_input_description'),
                );
                print_r(json_encode($this->response));
            } else {
                $terms_n_conditions_input_description = strip_tags($_POST['terms_n_conditions_input_description']);
                $privacy_policy_input_description = strip_tags($_POST['privacy_policy_input_description']);
                if(isset($terms_n_conditions_input_description) && !empty($terms_n_conditions_input_description) && isset($privacy_policy_input_description) && !empty($privacy_policy_input_description)){
                    // Collect translation data for privacy_policy
                    $privacy_policy_translations = [];
                    if (isset($_POST['privacy_policy_translations']) && is_array($_POST['privacy_policy_translations'])) {
                        $privacy_policy_translations = $_POST['privacy_policy_translations'];
                    }
                    if (!isset($privacy_policy_translations['en'])) {
                        $privacy_policy_translations['en'] = [];
                    }
                    if (empty($privacy_policy_translations['en']['value']) && !empty($_POST['privacy_policy_input_description'])) {
                        $privacy_policy_translations['en']['value'] = $_POST['privacy_policy_input_description'];
                    }
                    $_POST['privacy_policy_translations'] = $privacy_policy_translations;
                    
                    // Collect translation data for terms_conditions
                    $terms_n_conditions_translations = [];
                    if (isset($_POST['terms_n_conditions_translations']) && is_array($_POST['terms_n_conditions_translations'])) {
                        $terms_n_conditions_translations = $_POST['terms_n_conditions_translations'];
                    }
                    if (!isset($terms_n_conditions_translations['en'])) {
                        $terms_n_conditions_translations['en'] = [];
                    }
                    if (empty($terms_n_conditions_translations['en']['value']) && !empty($_POST['terms_n_conditions_input_description'])) {
                        $terms_n_conditions_translations['en']['value'] = $_POST['terms_n_conditions_input_description'];
                    }
                    $_POST['terms_n_conditions_translations'] = $terms_n_conditions_translations;
                    
                    $this->Setting_model->update_privacy_policy($_POST);
                    $this->Setting_model->update_terms_n_condtions($_POST);
                    $this->response['error'] = false;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'System Setting Updated Successfully';
                    print_r(json_encode($this->response));

                }
               else{
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'Terms and Condition Description And Privay Policy Description fields are required';
                    print_r(json_encode($this->response));
                    return false;
                }
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function privacy_policy_page()
    {
        $settings = get_settings('system_settings', true);
        $this->data['title'] = 'Privacy Policy | ' . $settings['app_name'];
        $this->data['meta_description'] = 'Privacy Policy | ' . $settings['app_name'];
        $this->data['privacy_policy'] = get_settings('privacy_policy');
        $this->load->view('admin/pages/view/privacy-policy', $this->data);
    }

    public function terms_and_conditions_page()
    {
        $settings = get_settings('system_settings', true);
        $this->data['title'] = 'Terms & Conditions | ' . $settings['app_name'];
        $this->data['meta_description'] = 'Terms & Conditions | ' . $settings['app_name'];
        $this->data['terms_and_conditions'] = get_settings('terms_conditions');
        $this->load->view('admin/pages/view/terms-and-conditions', $this->data);
    }
}

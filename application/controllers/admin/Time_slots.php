<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Time_slots extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model('Time_slots_model');
        if (!has_permissions('read', 'time_slots')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        }
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'time-slots';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Add Time Slot | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Add Time Slot | ' . $settings['app_name'];
            $this->data['branch_timing'] = fetch_details(['branch_id' => $_SESSION['branch_id']], 'branch_timings');
            $this->data['fetched_data'] = fetch_details(['branch_id' => $_SESSION['branch_id']], 'time_slots');
            if (!isset($_SESSION['branch_id'])) {
                redirect('admin/branch', 'refresh');
            } else {

                $this->load->view('admin/template', $this->data);
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function manage_time_slots()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = TABLES . 'manage-time-slots';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Manage Time Slots | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Manage Time Slots  | ' . $settings['app_name'];
            if (!isset($_SESSION['branch_id'])) {

                redirect('admin/branch', 'refresh');
            } else {

                $this->load->view('admin/template', $this->data);
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function add_time_slots()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->form_validation->set_rules('time_slots_type', 'Time Slot Type', 'trim|required|xss_clean');
            $this->form_validation->set_rules('time_slot_intervals', 'Time Slot Intervals', 'trim|required|xss_clean');

            if (!$this->form_validation->run()) {
                $response['error'] = true;
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                $response['messages'] = array('time_slots_type' => form_error('time_slots_type'), 'time_slot_intervals' => form_error('time_slot_intervals'));
                print_r(json_encode($response));
                return;
            }

            $_POST['branch_id'] = $_SESSION['branch_id'];
            $_POST['is_time_slot_enable'] = isset($_POST['is_time_slot_enable']) && $_POST['is_time_slot_enable'] == 'on' ? '1' : '0';
            // print_R($_POST);
            // die;
            $this->Time_slots_model->add_time_slots($_POST);
            $response["error"] = false;
            $response['csrfName'] = $this->security->get_csrf_token_name();
            $response['csrfHash'] = $this->security->get_csrf_hash();
            $response["message"] = "Time Slots updated successfully";
            print_r(json_encode($response));
            return;
        } else {
            redirect('admin/login', 'refresh');
        }
    }

}

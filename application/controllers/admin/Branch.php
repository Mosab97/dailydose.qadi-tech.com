<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Branch extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model('Branch_model');
        if (!has_permissions('read', 'branch')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        }
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'Branch';
            $settings = get_settings('system_settings', true);

            if (isset($_GET['edit_branch'])) {

                $this->data['title'] = 'Update Branch | ' . $settings['app_name'];
                $this->data['meta_description'] = 'Update Branch | ' . $settings['app_name'];
                $this->data['fetched_details'] = fetch_details(['id' => $_GET['edit_branch']], 'branch');
            } else {
                $this->data['title'] = 'Add Branch | ' . $settings['app_name'];
                $this->data['meta_description'] = 'Add Branch | ' . $settings['app_name'];
            }
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function manage_branch()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = TABLES . 'manage-branch';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Manage Branch | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Manage Branch  | ' . $settings['app_name'];
            if (!isset($_SESSION['branch_id'])) {

                redirect('admin/branch', 'refresh');
            } else {

                $this->load->view('admin/template', $this->data);
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function add_branch()
    {
        // print_R($_POST);
        // die;
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (isset($_POST['edit_branch'])) {

                if (print_msg(!has_permissions('update', 'branch'), PERMISSION_ERROR_MSG, 'branch')) {
                    return false;
                }
            } else {
                if (print_msg(!has_permissions('create', 'branch'), PERMISSION_ERROR_MSG, 'branch')) {
                    return false;
                }
            }
            // print_r($_POST['working_time']);
            // die;
            $this->form_validation->set_rules('branch_name', 'Branch Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('description', 'Description', 'trim|xss_clean');
            $this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean');
            $this->form_validation->set_rules('city', 'City', 'trim|required|xss_clean');
            $this->form_validation->set_rules('latitude', 'Latitude', 'trim|required|xss_clean');
            $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required|xss_clean');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
            $this->form_validation->set_rules('contact', 'Contact', 'trim|required|xss_clean');
            $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean');
            $this->form_validation->set_rules('branch_image', 'Branch Image', 'trim|required|xss_clean', array('required' => 'Branch image is required'));
            if (isset($_POST['edit_branch'])) {
                $this->form_validation->set_rules('working_time', 'Working Days', 'trim|xss_clean');
            } else {

                $this->form_validation->set_rules('working_time', 'Working Days', 'trim|xss_clean|required');
            }

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['messages'] = array(
                    'branch_name' => form_error('branch_name'),
                    'description' => form_error('description'),
                    'address' => form_error('address'),
                    'city' => form_error('city'),
                    'latitude' => form_error('latitude'),
                    'longitude' => form_error('longitude'),
                    'email' => form_error('email'),
                    'contact' => form_error('contact'),
                    'status' => form_error('status'),
                    'working_time' => form_error('working_time'),
                    'branch_image' => form_error('branch_image'),
                );
                print_r(json_encode($this->response));
            } else {
                if (isset($_POST['edit_branch'])) {

                    if (!isset($_POST['default_mode'])) {
                        $is_default = fetch_details(['id' => $_POST['edit_branch']], 'branch', 'default_branch');
                        if ($is_default[0]['default_branch'] == 1) {
                            $response["error"] = true;
                            $response['csrfName'] = $this->security->get_csrf_token_name();
                            $response['csrfHash'] = $this->security->get_csrf_hash();
                            $response["message"] = "Please designate one branch as the default.";
                            $response["data"] = array();
                            echo json_encode($response);
                            return false;
                        } else {
                        }
                    }

                    if (is_exist(['branch_name' => $this->input->post('branch_name', true)], 'branch', $this->input->post('edit_branch', true))) {
                        $response["error"] = true;
                        $response['csrfName'] = $this->security->get_csrf_token_name();
                        $response['csrfHash'] = $this->security->get_csrf_hash();
                        $response["message"] = "This Branch Already Exist.";
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }
                } else {
                    if (is_exist(['branch_name' => $this->input->post('branch_name', true)], 'branch')) {
                        $response["error"] = true;
                        $response['csrfName'] = $this->security->get_csrf_token_name();
                        $response['csrfHash'] = $this->security->get_csrf_hash();
                        $response["message"] = "This Branch Already Exist.";
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }
                }
                $self_pickup = isset($_POST['self_pickup']) && $_POST['self_pickup'] == 'on' ? 1 : 0;
                $deliver_orders = isset($_POST['deliver_orders']) && $_POST['deliver_orders'] == 'on' ? 1 : 0;
                $global_branch_time = isset($_POST['global_branch_time']) && $_POST['global_branch_time'] == 'on' ? 1 : 0;


                if ($self_pickup == 0 && $deliver_orders == 0) {
                    $response["error"] = true;
                    $response['csrfName'] = $this->security->get_csrf_token_name();
                    $response['csrfHash'] = $this->security->get_csrf_hash();
                    $response["message"] = "You have to enable at least one delivery option form self pickup and deliver order.";
                    $response["data"] = array();
                    echo json_encode($response);
                    return false;
                }

                $working_time = isset($_POST['working_time']) ? $_POST['working_time'] : "";
                $this->Branch_model->add_branch($_POST, $working_time);
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['location'] = base_url('admin/branch/manage_branch');
                $message = (isset($_POST['edit_branch'])) ? 'Branch Updated Successfully' : 'Branch Added Successfully';
                $this->response['message'] = $message;
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }



    public function branch_list()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            return $this->Branch_model->get_branch_list();
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function delete_branch()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                $this->response['error'] = true;
                $this->response['message'] = DEMO_VERSION_MSG;
                echo json_encode($this->response);
                return false;
                exit();
            }
            if (!has_permissions('delete', 'branch')) {
                $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'You are not authorized to oprate on this module!';
                print_r(json_encode($this->response));
                redirect('admin/home', 'refresh');
            }
            $branch_id = $this->input->get('id', true);
            $branch_detail = fetch_details(['id' => $branch_id], 'branch', 'default_branch');
            if ($_SESSION['branch_id'] == $branch_id) {
                $this->response['error'] = true;
                $this->response['message'] = 'The currently selected branch cannot be deleted. Please switch to a different branch before proceeding with deletion.';
            } elseif ($branch_detail[0]['default_branch'] == '1') {
                $this->response['error'] = true;
                $this->response['message'] = 'Default branch can not be deleted';
            } else {
                if (delete_details(['id' => $branch_id], 'branch') == TRUE) {
                    $this->response['error'] = false;
                    $this->response['message'] = 'Deleted Successfully';
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Something Went Wrong';
                }
            }
            print_r(json_encode($this->response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function save_branch($data = NULL)
    {

        session_start();

        if (isset($_POST['id'])) {
            $id = $_POST['id'];

            $_SESSION['branch_id'] = $id;

            echo "ID stored in session successfully.";
        } else {
            echo "Error: ID not received.";
        }
    }

    public function get_branch()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            $limit = (isset($_GET['limit'])) ? $this->input->get('limit', true) : 25;
            $offset = (isset($_GET['offset'])) ? $this->input->get('offset', true) : 0;
            $search = (isset($_GET['search'])) ? $_GET['search'] : null;
            $tags = $this->Branch_model->get_branch($search, $limit, $offset);
            $this->response['data'] = $tags;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            print_r(json_encode($this->response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function update_default_branch()
    {
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            $this->response['error'] = true;
            $this->response['message'] = DEMO_VERSION_MSG;
            echo json_encode($this->response);
            return false;
            exit();
        }
        $active_branch = fetch_details(['id' => $_GET['id']], 'branch', 'status');
        if ($active_branch[0]['status'] == 1) {

            if ($_GET['default_status'] == 0) {
                $fetch_default_branch = fetch_details(['default_branch' => 1], 'branch', 'id');
                if (!empty($fetch_default_branch)) {
                    if (update_details(['default_branch' => 0], ['id' => $fetch_default_branch[0]['id']], 'branch')) {
                        if (update_details(['default_branch' => 1], ['id' => $_GET['id']], 'branch')) {
                            $this->response['error'] = false;
                            $this->response['csrfName'] = $this->security->get_csrf_token_name();
                            $this->response['csrfHash'] = $this->security->get_csrf_hash();
                            $this->response['message'] = "Default branch updated successfully!";
                            print_r(json_encode($this->response));
                        }
                    }
                } else {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = "Something went wrong!";
                    print_r(json_encode($this->response));
                }
            } else {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = "Please designate one branch as the default.!";
                print_r(json_encode($this->response));
            }
        } else {
            $this->response['error'] = true;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = "deative branch can not be default!";
            print_r(json_encode($this->response));
        }
    }
}

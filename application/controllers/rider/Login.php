<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language']);
        $this->lang->load('auth');
    }

    public function index()
    {
        if (!$this->ion_auth->logged_in() && !$this->ion_auth->is_rider()) {
            $this->data['main_page'] = FORMS . 'login';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Rider Login Panel | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Rider Login Panel | ' . $settings['app_name'];
            $this->data['rider_logo'] = get_settings('rider_logo');
            $this->data['rider_cover_image'] = get_settings('rider_cover_image');
            $identity = $this->config->item('identity', 'ion_auth');
            if (empty($identity)) {
                $identity_column = 'text';
            } else {
                $identity_column = $identity;
            }
            $this->data['identity_column'] = $identity_column;
            $this->load->view('rider/login', $this->data);
        } else if ($this->ion_auth->logged_in() && $this->ion_auth->is_rider()) {
            redirect('rider/home', 'refresh');
        } else if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->ion_auth->logout();
            redirect('rider/home', 'refresh');
        } else if ($this->ion_auth->logged_in() && $this->ion_auth->is_partner()) {
            $this->ion_auth->logout();
            redirect('rider/home', 'refresh');
        }
    }
    public function update_user()
    {

        $identity_column = $this->config->item('identity', 'ion_auth');
        $identity = $this->session->userdata('identity');
        $user = $this->ion_auth->user()->row();

        if ($identity_column == 'email') {
            $this->form_validation->set_rules('email', 'Email', 'required|xss_clean|trim|valid_email|edit_unique[users.email.' . $user->id . ']');
        } else {
            $this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean|trim|numeric|edit_unique[users.mobile.' . $user->id . ']');
        }
        $this->form_validation->set_rules('username', 'Username', 'required|xss_clean|trim');
        $this->form_validation->set_rules('status', 'Status', 'required|xss_clean|trim');

        if (!empty($_POST['old']) || !empty($_POST['new']) || !empty($_POST['new_confirm'])) {
            $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
            $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        }


        $tables = $this->config->item('tables', 'ion_auth');
        if (!$this->form_validation->run()) {
            if (validation_errors()) {
                $response['error'] = true;
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                // $response['message'] = array(
                //     'email' => form_error('email'),
                //     'mobile' => form_error('mobile'),
                //     'username' => form_error('username'),
                //     'status' => form_error('status'),
                //     'old' => form_error('old'),
                //     'new' => form_error('new'),
                //     'new_confirm' => form_error('new_confirm'),
                // );
                $response['message'] = validation_errors();
                echo json_encode($response);
                return false;
                exit();
            }
            if ($this->session->flashdata('message')) {
                $response['error'] = false;
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                $response['message'] = $this->session->flashdata('message');
                echo json_encode($response);
                return false;
                exit();
            }
        } else {

            if (!empty($_POST['old']) || !empty($_POST['new']) || !empty($_POST['new_confirm'])) {
                if (!$this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'))) {
                    $response['error'] = true;
                    $response['csrfName'] = $this->security->get_csrf_token_name();
                    $response['csrfHash'] = $this->security->get_csrf_hash();
                    $response['message'] = $this->ion_auth->errors();
                    echo json_encode($response);
                    return;
                    exit();
                }
            }
            if (isset($_POST['status']) && !empty($_POST['status'])) {

                if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {


                    $response['error'] = true;
                    $response['message'] = DEMO_VERSION_MSG;
                    echo json_encode($response);
                    return false;
                } else {
                    if (isset($_POST['accept_orders']) && !empty($_POST['accept_orders'])) {
                        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                            $this->response['error'] = true;
                            $this->response['message'] = DEMO_VERSION_MSG;
                            echo json_encode($this->response);
                            return false;
                        } else {

                            $active_status = "confirmed,preparing,out_for_deliver";
                            $multiple_status = explode(',', $active_status);
                            $order_details = fetch_orders(false, false, $multiple_status, $user->id);
                            if (!empty($order_details['order_data'])) {
                                $response['error'] = true;
                                $response['message'] = 'You have some order assigned, If you still want to deactive the account then you can contact to the Administrator!';
                                $response['csrfName'] = $this->security->get_csrf_token_name();
                                $response['csrfHash'] = $this->security->get_csrf_hash();
                                echo json_encode($response);
                                return;
                            } else {
                                $set['accept_orders'] = $this->input->post('staus', true);
                            }
                        }
                    }
                }
            }
            $set = [
                'username' => $this->input->post('username'),
                'address' => $this->input->post('address'),
                'accept_orders' => $this->input->post('status'),
            ];
            $set = escape_array($set);
            $this->db->set($set)->where($identity_column, $identity)->update($tables['login_users']);
            $response['error'] = false;
            $response['csrfName'] = $this->security->get_csrf_token_name();
            $response['csrfHash'] = $this->security->get_csrf_hash();
            $response['message'] = 'Profile Update Successfully';
            echo json_encode($response);
            return;
        }
    }
    public function auth()
    {
        $identity_column = $this->config->item('identity', 'ion_auth');
        $identity = $this->input->post('identity', true);
        $this->form_validation->set_rules('identity', 'Email', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
        $res = $this->db->select('id')->where($identity_column, $identity)->get('users')->result_array();

        if ($this->form_validation->run()) {
            if (!empty($res)) {
                if ($this->ion_auth_model->in_group('rider', $res[0]['id'])) {
                    $remember = (bool)$this->input->post('remember');
                    if ($this->ion_auth->login($this->input->post('identity', true), $this->input->post('password', true), $remember)) {
                        $rider_status = $this->ion_auth->rider_status($this->session->userdata('user_id'));
                        $messages = array("0" => "Your acount is deactivated", "1" => "Logged in successfully", "2" => "Your account is not yet approved.", "7" => "Your account has been removed by the admin. Contact admin for more information.");
                        if ($rider_status == 0 || $rider_status == 2 || $rider_status == 7) {
                            $this->ion_auth->logout();
                            $response['error'] = true;
                            $response['csrfName'] = $this->security->get_csrf_token_name();
                            $response['csrfHash'] = $this->security->get_csrf_hash();
                            $response['message'] = $messages[$rider_status];
                            echo json_encode($response);
                            return;
                        } else {
                            $response['error'] = false;
                            $response['csrfName'] = $this->security->get_csrf_token_name();
                            $response['csrfHash'] = $this->security->get_csrf_hash();
                            $response['message'] = $messages[$rider_status];
                            echo json_encode($response);
                        }
                    } else {
                        // if the login was un-successful
                        $response['error'] = true;
                        $response['csrfName'] = $this->security->get_csrf_token_name();
                        $response['csrfHash'] = $this->security->get_csrf_hash();
                        $response['message'] = $this->ion_auth->errors();
                        echo json_encode($response);
                    }
                } else {
                    $response['error'] = true;
                    $response['csrfName'] = $this->security->get_csrf_token_name();
                    $response['csrfHash'] = $this->security->get_csrf_hash();
                    $response['message'] = ucfirst($identity_column) . ' field is not correct';
                    echo json_encode($response);
                }
            } else {
                $response['error'] = true;
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                $response['message'] = '' . ucfirst($identity_column) . ' field is not correct';
                echo json_encode($response);
            }
        } else {
            $response['error'] = true;
            $response['csrfName'] = $this->security->get_csrf_token_name();
            $response['csrfHash'] = $this->security->get_csrf_hash();
            $response['message'] = validation_errors();
            echo json_encode($response);
        }
    }

    public function forgot_password()
    {
        $this->data['main_page'] = FORMS . 'forgot-password';
        $this->data['title'] = 'Forget Password | Rider Panel';
        $this->data['meta_description'] = 'eRestro';
        $this->data['logo'] = get_settings('logo');
        $this->load->view('rider/login', $this->data);
    }
}

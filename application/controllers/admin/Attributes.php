<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Attributes extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model('attribute_model');
        if (!has_permissions('read', 'attribute')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        }
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'attribute';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Manage Attributes | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Manage Attributes | ' . $settings['app_name'];
            if (isset($_GET['edit_id'])) {
                $this->data['fetched_data'] = $this->db->select(' attr.* ,GROUP_CONCAT(av.value) as attribute_values')->join('attribute_values av', 'av.attribute_id = attr.id')
                    ->where(['attr.id' => $_GET['edit_id']])->group_by('attr.id')->get('attributes attr')->result_array();
                
                // Load attribute translations
                $this->data['attribute_translations'] = $this->attribute_model->get_attribute_translations($_GET['edit_id']);
                
                // Load attribute value translations
                $attribute_values = $this->db->where('attribute_id', $_GET['edit_id'])->get('attribute_values')->result_array();
                $this->data['attribute_value_translations'] = [];
                foreach ($attribute_values as $av) {
                    $this->data['attribute_value_translations'][$av['id']] = $this->attribute_model->get_attribute_value_translations($av['id']);
                }
            } else {
                // Initialize empty translations arrays for new attributes
                $this->data['attribute_translations'] = [];
                $this->data['attribute_value_translations'] = [];
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

    public function manage_attribute()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = TABLES . 'manage-attribute';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Attribute | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Attribute  | ' . $settings['app_name'];
            if (!isset($_SESSION['branch_id'])) {

                redirect('admin/branch', 'refresh');
            } else {

                $this->load->view('admin/template', $this->data);
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }
    public function add_attributes()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (isset($_POST['edit_attribute_id'])) {
                if (print_msg(!has_permissions('update', 'attribute'), PERMISSION_ERROR_MSG, 'attribute')) {
                    return false;
                }
            } else {
                if (print_msg(!has_permissions('create', 'attribute'), PERMISSION_ERROR_MSG, 'attribute')) {
                    return false;
                }
            }

        $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
        if (!isset($_POST['edit_attribute_id'])) {
            $this->form_validation->set_rules('attribute_values', 'Attribute Values', 'trim|required|xss_clean');
        }

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['messages'] = array(
                    'name' => form_error('name'),
                    'attribute_values' => form_error('attribute_values'),
                );
                print_r(json_encode($this->response));
            } else {
                // Collect and process attribute translations
                $attribute_translations = [];
                if (isset($_POST['attribute_translations']) && is_array($_POST['attribute_translations'])) {
                    $attribute_translations = $_POST['attribute_translations'];
                } elseif (isset($_POST['attribute_translations']) && is_string($_POST['attribute_translations']) && !empty($_POST['attribute_translations'])) {
                    // If it's a JSON string, parse it
                    $parsed = json_decode($_POST['attribute_translations'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                        $attribute_translations = $parsed;
                    }
                }
                
                // Always ensure English translation is included from main field
                if (!isset($attribute_translations['en'])) {
                    $attribute_translations['en'] = [];
                }
                if (empty($attribute_translations['en']['name']) && !empty($_POST['name'])) {
                    $attribute_translations['en']['name'] = $_POST['name'];
                }
                
                // Set the processed translations back to POST
                $_POST['attribute_translations'] = $attribute_translations;
                
                // Handle attribute value translations if provided
                if (isset($_POST['attribute_value_translations'])) {
                    if (is_string($_POST['attribute_value_translations']) && !empty($_POST['attribute_value_translations'])) {
                        // If it's a JSON string, parse it
                        $parsed = json_decode($_POST['attribute_value_translations'], true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                            $_POST['attribute_value_translations'] = $parsed;
                        } else {
                            // If JSON parsing fails, unset it to avoid errors
                            unset($_POST['attribute_value_translations']);
                        }
                    } elseif (!is_array($_POST['attribute_value_translations'])) {
                        // If it's neither string nor array, unset it
                        unset($_POST['attribute_value_translations']);
                    }
                }
                
                if (isset($_POST['edit_attribute_id'])) {
                    if (is_exist(['name' => $_POST['name']], 'attributes', $_POST['edit_attribute_id'])) {
                        $response["error"] = true;
                        $response['csrfName'] = $this->security->get_csrf_token_name();
                        $response['csrfHash'] = $this->security->get_csrf_hash();
                        $response["message"] = "This Attribute Already Exist.";
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }
                    if ($this->attribute_model->add_attributes($_POST)) {
                        $response["error"] = true;
                        $response["message"] = "This combination already exist ! Please provide a new combination";
                        $response['csrfName'] = $this->security->get_csrf_token_name();
                        $response['csrfHash'] = $this->security->get_csrf_hash();
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    } else {
                        $this->response['error'] = false;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['message'] = "Attribute Updated Successfully";
                        print_r(json_encode($this->response));
                        return false;
                    }
                } else {
                    if (is_exist(['name' => $_POST['name']], 'attributes')) {

                        $response["error"] = true;
                        $response['csrfName'] = $this->security->get_csrf_token_name();
                        $response['csrfHash'] = $this->security->get_csrf_hash();
                        $response["message"] = "This Attribute Already Exist.";
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }
                    $this->attribute_model->add_attributes($_POST);
                    $this->response['error'] = false;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'Attribute Added Successfully';
                    print_r(json_encode($this->response));

                }
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function attribute_list()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            return $this->attribute_model->get_attribute_list();
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function update_attribute_values_status()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                $this->response['error'] = true;
                $this->response['message'] = DEMO_VERSION_MSG;
                echo json_encode($this->response);
                return false;
                exit();
            }
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = $this->input->get("id", true);
                $attribute = fetch_details(['id' => $id], 'attribute_values', 'attribute_id');
                if ($this->db->select('id')->where(['attribute_id' => $attribute[0]['attribute_id']])->get('attribute_values')->num_rows() > 1) {
                    if (update_details(['status' => 0], ['id' => $id], "attribute_values")) {
                        $response['error'] = false;
                        $response['csrfName'] = $this->security->get_csrf_token_name();
                        $response['csrfHash'] = $this->security->get_csrf_hash();
                        $response['message'] = "Value Deleted Successfully.";
                        print_r(json_encode($response));
                        return false;
                    } else {
                        $response['error'] = true;
                        $response['csrfName'] = $this->security->get_csrf_token_name();
                        $response['csrfHash'] = $this->security->get_csrf_hash();
                        $response['message'] = "Something went wrong.Please try again later.";
                        print_r(json_encode($response));
                        return false;
                    }
                } else {
                    $response['error'] = true;
                    $response['csrfName'] = $this->security->get_csrf_token_name();
                    $response['csrfHash'] = $this->security->get_csrf_hash();
                    $response['message'] = "Atlest one value is compolsory with attribute";
                    print_r(json_encode($response));
                    return false;
                }


            } else {
                $response['error'] = true;
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                $response['message'] = "ID field is required.";
                print_r(json_encode($response));
                return false;
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function delete_attribute_combination()
    {
        if (!has_permissions('delete', 'attribute')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            $this->response['error'] = false;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = 'You are not authorized to oprate on this module!';
            print_r(json_encode($this->response));
            redirect('admin/home', 'refresh');
        }
        $attribute_values = fetch_details(['attribute_id' => $_GET['id']], 'attribute_values');
        $attribute_ids = array_column($attribute_values, 'id');

        // Fetch product varients
        $this->db->select('attribute_value_ids');
        $this->db->from('product_variants');
        $query = $this->db->get();
        $product_varients = $query->result_array();

        // Check for matches
        $matching_ids = [];
        foreach ($product_varients as $varient) {
            $varient_ids = isset($varient['attribute_value_ids']) ? explode(',', $varient['attribute_value_ids']) : [];
            foreach ($attribute_ids as $id) {
                if (in_array($id, $varient_ids)) {
                    $matching_ids[] = $id;
                }
            }
        }

        // Remove duplicate IDs
        $matching_ids = array_unique($matching_ids);

        if (!empty($matching_ids)) {
            // Do something with the matching IDs
            $response['error'] = false;
            $response['csrfName'] = $this->security->get_csrf_token_name();
            $response['csrfHash'] = $this->security->get_csrf_hash();
            $response['message'] = "Attribute can not be deleted, Product varients are containing this attributes!.";
            print_r(json_encode($response));
            return false;
        } else {
            if (delete_details(['id' => $_GET['id']], 'attributes')) {
                if (delete_details(['attribute_id' => $_GET['id']], 'attribute_values')) {
                    $response['error'] = true;
                    $response['csrfName'] = $this->security->get_csrf_token_name();
                    $response['csrfHash'] = $this->security->get_csrf_hash();
                    $response['message'] = "Attribute Deleted Successfully!.";
                    print_r(json_encode($response));
                    return false;
                }

            }
        }
    }

    public function get_attribute_translations()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $attribute_id = $this->input->get('attribute_id', true);
            
            if (empty($attribute_id)) {
                $this->response['error'] = true;
                $this->response['message'] = "Attribute ID is required";
                $this->response['data'] = [];
            } else {
                $translations = $this->attribute_model->get_attribute_translations($attribute_id);
                
                $this->response['error'] = false;
                $this->response['message'] = "Translations retrieved successfully";
                $this->response['data'] = $translations;
            }
            
            print_r(json_encode($this->response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function get_attribute_value_translations()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $attribute_value_id = $this->input->get('attribute_value_id', true);
            
            if (empty($attribute_value_id)) {
                $this->response['error'] = true;
                $this->response['message'] = "Attribute Value ID is required";
                $this->response['data'] = [];
            } else {
                $translations = $this->attribute_model->get_attribute_value_translations($attribute_value_id);
                
                $this->response['error'] = false;
                $this->response['message'] = "Translations retrieved successfully";
                $this->response['data'] = $translations;
            }
            
            print_r(json_encode($this->response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }
}

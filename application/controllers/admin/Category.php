<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Category extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation', 'upload']);
        $this->load->helper(['url', 'language', 'file']);
        $this->load->model(['category_model']);

        if (!has_permissions('read', 'categories')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        }
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            $this->data['main_page'] = TABLES . 'manage-category';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Category Management | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Category Management | ' . $settings['app_name'];
            $id = $this->input->get('id', true);
            if (isset($id) && !empty($id)) {
                $this->data['base_category_url'] = base_url() . 'admin/category/category_list?id=' . $id;
            } else {
                $this->data['base_category_url'] = base_url() . 'admin/category/category_list';
            }
            $this->data['category_result'] = $this->category_model->get_categories();
            if (!isset($_SESSION['branch_id'])) {

                redirect('admin/branch', 'refresh');
            } else {

                $this->load->view('admin/template', $this->data);
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function get_categories()
    {

            $ignore_status = isset($_GET['ignore_status']) && $_GET['ignore_status'] == 1 ? 1 : '';
            $search = (isset($_GET['search'])) ? $this->input->get('search') : null;
            $response['data'] = $this->data['category_result'] = $this->category_model->get_categories(NULL, '', '', 'row_order', 'ASC', 'true', '', $ignore_status, $search);
            echo json_encode($response);
            return;

 
    }

    public function create_category()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'category';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) ? 'Edit Category | ' . $settings['app_name'] : 'Add Category | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Add Category , Create Category | ' . $settings['app_name'];
            if (!has_permissions('read', 'branch')) {
                $this->data['permissions_message'] = PERMISSION_ERROR_MSG;
            }
            
            // Get current language dynamically from URL parameter, session, cookie, or default to 'en'
            $language_code = 'en'; // Default language
            
            // Priority: URL parameter > Session > Cookie (googtrans) > Default
            if (isset($_GET['lang']) && !empty($_GET['lang'])) {
                $language_code = $this->input->get('lang', true);
            } elseif (isset($_SESSION['admin_language']) && !empty($_SESSION['admin_language'])) {
                $language_code = $_SESSION['admin_language'];
            } elseif ($this->input->cookie('googtrans', true)) {
                // Parse googtrans cookie format: /en/ar (Arabic) or /en/en (English)
                $googtrans_value = $this->input->cookie('googtrans', true);
                $parts = explode('/', trim($googtrans_value, '/'));
                if (count($parts) >= 2) {
                    $language_code = end($parts); // Get the last part (target language)
                } elseif (count($parts) == 1 && !empty($parts[0])) {
                    $language_code = $parts[0]; // Fallback if format is different
                }
            }
            
            // Validate language code exists in database, fallback to 'en' if invalid
            $valid_languages = get_languages('', '', '', '');
            $valid_codes = array_column($valid_languages, 'code');
            if (!in_array($language_code, $valid_codes)) {
                $language_code = 'en'; // Fallback to English
            }
            
            if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {
                $edit_category = fetch_details(['id' => $_GET['edit_id']], 'categories');
                if ($edit_category[0]['branch_id'] == $_SESSION['branch_id']) {
                    $this->data['fetched_data'] = fetch_details(['id' => $_GET['edit_id']], 'categories');
                    // Load existing translations when editing
                    $this->data['category_translations'] = $this->category_model->get_category_translations($_GET['edit_id']);
                } else {
                    redirect('admin/category/', 'refresh');
                }
            } else {
                $this->data['category_translations'] = [];
            }
            $this->data['categories'] = $this->category_model->get_categories();
            $this->data['branch'] = fetch_details('', 'branch', 'id,branch_name');
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function category_order()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (!has_permissions('read', 'category_order')) {
                $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
                redirect('admin/home', 'refresh');
            }
            $this->data['main_page'] = TABLES . 'category-order';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Category Order | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Category Order | ' . $settings['app_name'];
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

    function delete_category()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (print_msg(!has_permissions('delete', 'categories'), PERMISSION_ERROR_MSG, 'categories')) {
                return false;
            }

            $category_products = fetch_details(['category_id' => $_GET['id']], 'products', 'id,category_id');
            $category_offer = fetch_details(['type' => 'categories', 'type_id' => $_GET['id']], 'offers', 'id');
            $category_slider = fetch_details(['type' => 'categories', 'type_id' => $_GET['id']], 'offers', 'id');
            if (!empty($category_products)) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'This category cannot be deleted because it is associated with product data.';
                print_r(json_encode($this->response));

            } elseif (!empty($category_offer)) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'This category cannot be deleted because it is associated with offers.';
                print_r(json_encode($this->response));
                return false;
            } elseif (!empty($category_slider)) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'This category cannot be deleted because it is associated slider.';
                print_r(json_encode($this->response));
                return false;
            }else {
                if ($this->category_model->delete_category($_GET['id']) == TRUE) {
                    $this->response['error'] = false;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'Deleted Successfully';
                    print_r(json_encode($this->response));
                }
            }

        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function category_list()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            return $this->category_model->get_category_list();
        } else {
            redirect('admin/login', 'refresh');
        }
    }



    public function add_category()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (isset($_POST['edit_category'])) {
                if (print_msg(!has_permissions('update', 'categories'), PERMISSION_ERROR_MSG, 'categories')) {
                    return false;
                }
            } else {
                if (print_msg(!has_permissions('create', 'categories'), PERMISSION_ERROR_MSG, 'categories')) {
                    return false;
                }
            }
            $this->form_validation->set_rules('category_input_name', 'Category Name', 'trim|required|xss_clean');

            if (isset($_POST['edit_category'])) {
                $this->form_validation->set_rules('category_input_image', 'Image', 'trim|required|xss_clean');
            } else {
                $this->form_validation->set_rules('category_input_image', 'Image', 'trim|required|xss_clean', array('required' => 'Category image is required'));
            }
            if (isset($_POST['edit_category'])) {
                // $this->form_validation->set_rules('branch[]', 'branch', 'trim|xss_clean');
            } else {
                $this->form_validation->set_rules('branch[]', 'branch', 'trim|required|xss_clean');
            }

            if (!$this->form_validation->run()) {

                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['messages'] = array(
                    'category_input_name' => form_error('category_input_name'),
                    'category_input_image' => form_error('category_input_image'),
                    'branch[]' => form_error('branch[]'),
                );
                print_r(json_encode($this->response));
            } else {

                if (!isset($_POST['edit_category'])) {
                    if (is_exist(['name' => $_POST['category_input_name'], 'branch_id' => $_POST['branch'][0], 'status' => 1], 'categories')) {

                        $this->response['error'] = true;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['message'] = "Category Already Exist! Provide another category name.";
                        $this->response['data'] = array();
                        echo json_encode($this->response);
                        return;
                    }
                }

                if (isset($_POST['edit_category'])) {
                    if (is_exist(['name' => $_POST['category_input_name']], 'categories')) {
                        $get_id = fetch_details(['name' => $_POST['category_input_name']], 'categories', 'id');
                        if ($get_id[0]['id'] !== $_POST['edit_category']) {
                            $response["error"] = true;
                            $response["message"] = "Category Already Exist! Provide another category name.";
                            $response['csrfName'] = $this->security->get_csrf_token_name();
                            $response['csrfHash'] = $this->security->get_csrf_hash();
                            $response["data"] = array();
                            echo json_encode($response);
                            return false;
                        }
                    }
                }

                $category_result = $this->category_model->add_category($_POST);
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                
                // Save category translations if provided
                if (isset($_POST['category_translations']) && !empty($_POST['category_translations'])) {
                    $translations = $this->input->post('category_translations', true);
                    
                    if (isset($_POST['edit_category']) && !empty($_POST['edit_category'])) {
                        // Update existing category - save translations for single category
                        $category_id = $_POST['edit_category'];
                        $this->category_model->save_category_translations($category_id, $translations);
                    } else {
                        // New category - save translations for all branch-specific categories
                        if (is_array($category_result) && !empty($category_result)) {
                            foreach ($category_result as $category_id) {
                                $this->category_model->save_category_translations($category_id, $translations);
                            }
                        }
                    }
                }
                
                $message = (isset($_POST['edit_category'])) ? 'Category Updated Successfully' : 'Category Added Successfully';
                $this->response['message'] = $message;
                $this->response['location'] = base_url('admin/category');
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function update_category_order()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('update', 'category_order'), PERMISSION_ERROR_MSG, 'category_order', false)) {
                return false;
            }
            $i = 0;
            $temp = array();
            foreach ($_GET['category_id'] as $row) {
                $temp[$row] = $i;
                $data = [
                    'row_order' => $i
                ];
                $data = escape_array($data);
                $this->db->where(['id' => $row])->update('categories', $data);
                $i++;
            }

            $response['error'] = false;
            $response['message'] = 'Category Order Saved !';

            print_r(json_encode($response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }


    public function bulk_upload()
    {

        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'category-bulk-upload';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Bulk Upload | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Bulk Upload | ' . $settings['app_name'];

            if (!isset($_SESSION['branch_id'])) {

                redirect('admin/branch', 'refresh');
            } else {

                $this->load->view('admin/template', $this->data);
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function process_bulk_upload()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('create', 'product'), PERMISSION_ERROR_MSG, 'product')) {
                return false;
            }
            $this->form_validation->set_rules('bulk_upload', '', 'xss_clean');
            $this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');
            if (empty($_FILES['upload_file']['name'])) {
                $this->form_validation->set_rules('upload_file', 'File', 'trim|required|xss_clean', array('required' => 'Please choose file'));
            }

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                $allowed_mime_type_arr = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv');
                $mime = get_mime_by_extension($_FILES['upload_file']['name']);
                if (!in_array($mime, $allowed_mime_type_arr)) {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'Invalid file format!';
                    print_r(json_encode($this->response));
                    return false;
                }
                $csv = $_FILES['upload_file']['tmp_name'];
                $temp = 0;
                $temp1 = 0;
                $handle = fopen($csv, "r");
                $this->response['message'] = '';
                $type = $_POST['type'];
                if ($type == 'upload') {
                    while (($row = fgetcsv($handle, 10000, ",")) != FALSE) //get row values
                    {
                        if ($temp != 0) {
                            if (empty($row[0])) {
                                $this->response['error'] = true;
                                $this->response['message'] = 'Name is empty at row ' . $temp;
                                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                                print_r(json_encode($this->response));
                                return false;
                            }
                            if (!empty($row[0])) {
                                if (is_exist(['name' => $row[0]], 'categories')) {
                                    $response["error"] = true;
                                    $response["message"] = "Category Already Exist! Provide another category name at row." . $temp;
                                    $response['csrfName'] = $this->security->get_csrf_token_name();
                                    $response['csrfHash'] = $this->security->get_csrf_hash();
                                    $response["data"] = array();
                                    echo json_encode($response);
                                    return false;
                                }
                            }
                            if (empty($row[1])) {
                                $this->response['error'] = true;
                                $this->response['message'] = 'Image is empty at row ' . $temp;
                                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                                print_r(json_encode($this->response));
                                return false;
                            }

                            if (empty($row[2])) {
                                $this->response['error'] = true;
                                $this->response['message'] = 'Branch Id is empty at row ' . $temp;
                                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                                print_r(json_encode($this->response));
                                return false;
                            }

                            if (!empty($row[2])) {
                                if (!is_exist(['id' => $row[2]], 'branch')) {
                                    $response["error"] = true;
                                    $response["message"] = "Branch is not exist Provide another branch id at row." . $temp;
                                    $response['csrfName'] = $this->security->get_csrf_token_name();
                                    $response['csrfHash'] = $this->security->get_csrf_hash();
                                    $response["data"] = array();
                                    echo json_encode($response);
                                    return false;
                                }
                            }
                        }
                        $temp++;
                    }

                    fclose($handle);
                    $handle = fopen($csv, "r");
                    while (($row = fgetcsv($handle, 10000, ",")) != FALSE) //get row vales
                    {
                        if ($temp1 != 0) {
                            $data['name'] = $row[0];
                            $data['slug'] = create_unique_slug($row[0], 'categories');
                            $data['image'] = $row[1];

                            $data['branch_id'] = $row[2];
                            $data['parent_id'] = 0;
                            $data['status'] = 1;
                            $this->db->insert('categories', $data);
                        }
                        $temp1++;
                    }
                    fclose($handle);
                    $this->response['error'] = false;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'Categories uploaded successfully!';
                    print_r(json_encode($this->response));
                    return false;
                } else { // bulk_update
                    while (($row = fgetcsv($handle, 10000, ",")) != FALSE) //get row vales
                    {
                        if ($temp != 0) {
                            if (empty($row[0])) {
                                $this->response['error'] = true;
                                $this->response['message'] = 'Category id is empty at row ' . $temp;
                                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                                print_r(json_encode($this->response));
                                return false;
                            }
                            if (!empty($row[0])) {
                                if (!is_exist(['id' => $row[0], 'branch_id' => $row[3], 'status' => 1], 'categories')) {
                                    $response["error"] = true;
                                    $response["message"] = "Category is not exist Provide another category id at row." . $temp;
                                    $response['csrfName'] = $this->security->get_csrf_token_name();
                                    $response['csrfHash'] = $this->security->get_csrf_hash();
                                    $response["data"] = array();
                                    echo json_encode($response);
                                    return false;
                                }
                            }
                            if (empty($row[1])) {
                                $this->response['error'] = true;
                                $this->response['message'] = 'Name is empty at row ' . $temp;
                                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                                print_r(json_encode($this->response));
                                return false;
                            }
                          
                            if (empty($row[2])) {
                                $this->response['error'] = true;
                                $this->response['message'] = 'Image is empty at row ' . $temp;
                                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                                print_r(json_encode($this->response));
                                return false;
                            }

                            if (empty($row[3])) {
                                $this->response['error'] = true;
                                $this->response['message'] = 'Branch Id is empty at row ' . $temp;
                                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                                print_r(json_encode($this->response));
                                return false;
                            }

                            if (!empty($row[3])) {
                                if (!is_exist(['id' => $row[3]], 'branch')) {
                                    $response["error"] = true;
                                    $response["message"] = "Branch is not exist Provide another branch id at row." . $temp;
                                    $response['csrfName'] = $this->security->get_csrf_token_name();
                                    $response['csrfHash'] = $this->security->get_csrf_hash();
                                    $response["data"] = array();
                                    echo json_encode($response);
                                    return false;
                                }
                            }
                        }
                        $temp++;
                    }

                    fclose($handle);
                    $handle = fopen($csv, "r");
                    while (($row = fgetcsv($handle, 10000, ",")) != FALSE) //get row values
                    {
                        if ($temp1 != 0) {
                            $category_id = $row[0];
                            $categories = fetch_details(['id' => $category_id], 'categories', '*');
                            if (isset($categories[0]) && !empty($categories[0])) {
                                if (!empty($row[1])) {
                                    $data['name'] = $row[1];
                                    $data['slug'] = create_unique_slug($row[1], 'categories');
                                } else {
                                    $data['name'] = $categories[0]['name'];
                                }
                                if (!empty($row[2])) {
                                    $data['image'] = $row[2];
                                } else {
                                    $data['image'] = $categories[0]['image'];
                                }

                                if (!empty($row[3])) {
                                    $data['branch_id'] = $row[3];
                                } else {
                                    $data['branch_id'] = $categories[0]['branch_id'];
                                }
                                $this->db->where('id', $row[0])->update('categories', $data);
                            }
                        }
                        $temp1++;
                    }
                    fclose($handle);
                    $this->response['error'] = false;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'Categories updated successfully!';
                    print_r(json_encode($this->response));
                    return false;
                }
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }
}

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

    }

    public function index()
    {
        
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
       
    }



    public function get_branch()
    {

            $limit = (isset($_GET['limit'])) ? $this->input->get('limit', true) : 25;
            $offset = (isset($_GET['offset'])) ? $this->input->get('offset', true) : 0;
            $search = (isset($_GET['search'])) ? $_GET['search'] : null;
            $tags = $this->Branch_model->get_branch($search, $limit, $offset);
            $this->response['data'] = $tags;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            print_r(json_encode($this->response));
        
    }

}

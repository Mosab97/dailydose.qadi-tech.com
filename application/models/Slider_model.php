<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Slider_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    function add_slider($data)
    {
        $data = escape_array($data);
       
        $slider_data = [
            'type' => $data['slider_type'],
            'image' => $data['image'],
        ];

        if (isset($data['slider_type']) && $data['slider_type'] == 'categories' && isset($data['category_id']) && !empty($data['category_id'])) {
            $slider_data['type_id'] = $data['category_id'];
        }

        if (isset($data['slider_type']) && $data['slider_type'] == 'products' && isset($data['product_id']) && !empty($data['product_id'])) {
            $slider_data['type_id'] = $data['product_id'];
        }

        if (isset($data['edit_slider'])) {
            if (empty($data['image'])) {
                unset($slider_data['image']);
            }

            $this->db->set($slider_data)->where('id', $data['edit_slider'])->update('sliders');
        } else {
            // $branch_ids = $data['branch'];
            $branch_ids = $_SESSION['branch_id'];
            $slider_ids = [];
            for ($i = 0; $i < count(array($branch_ids)); $i++) {
                $slider_data['branch_id'] = $branch_ids[$i];
                $this->db->insert('sliders', $slider_data);
                $slider_ids[] =  $this->db->insert_id();
            }
            return $slider_ids;
        }
    }

    function get_slider_list()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = '';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            
            $search = $_GET['search'];
            $multipleWhere = ['`id`' => $search, '`type`' => $search];
        }
        $branch_id = isset($_SESSION['branch_id'])   ? $_SESSION['branch_id'] : "";
        $where = array('sliders.branch_id' => trim($branch_id));
        $count_res = $this->db->select(' COUNT(id) as `total` ');
        $count_res->where($where);

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_Start();
            $count_res->or_where($multipleWhere);
            $count_res->group_end();
        }

        $slider_count = $count_res->get('sliders')->result_array();

        foreach ($slider_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' * ');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_Start();
            $search_res->or_like($multipleWhere);
            $search_res->group_end();
        }

        $search_res->where($where);


        $slider_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('sliders')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($slider_search_res as $row) {
            $row = output_escaping($row);

            $operate = ' <a href="' . base_url('admin/slider?edit_id=' . $row['id']) . '" class="btn btn-success btn-xs mr-1 mb-1"  title="Edit" data-id="' . $row['id'] . '" data-url="admin/slider/"><i class="fa fa-pen"></i></a>';
            $operate .= ' <a  href="javascript:void(0)" class="btn btn-danger btn-xs mr-1 mb-1"  title="Delete" id="delete-slider" data-id="' . $row['id'] . '"  ><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row['id'];
            $tempRow['type'] = $row['type'];
            $tempRow['type_id'] = $row['type_id'];
            if($row['type'] == 'products'){
                // print_r($row['type_id']);
                // die;
                $names = fetch_details(['id' => $row['type_id']], 'products', 'name');
                $tempRow['name'] = isset($names) && !empty($names) ? $names[0]['name'] : "";
            }else if($row['type'] == 'categories'){
                $names = fetch_details(['id' => $row['type_id']], 'categories', 'name');
                $tempRow['name'] = isset($names) && !empty($names) ? $names[0]['name'] : "";
            }else{
                $tempRow['name'] = "";
            }
            $branch_name =  fetch_details(['id' => $row['branch_id']], 'branch', 'branch_name');
            $tempRow['branch'] = stripslashes($branch_name[0]['branch_name']);

            if (empty($row['image']) || file_exists(FCPATH . $row['image']) == FALSE) {
                $row['image'] = base_url() . NO_IMAGE;
                $row['image_main'] = base_url() . NO_IMAGE;
            } else {
                $row['image_main'] = base_url($row['image']);
                $row['image'] = get_image_url($row['image'], 'thumb', 'sm');
            }
            $tempRow['image'] = "<div class='image-box-100'><a href='" . $row['image_main'] . "' data-toggle='lightbox' data-gallery='gallery'> <img src='" . $row['image'] . "' class='rounded' ></a></div>";
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    public function get_slider($branch_id = '', $limit = '', $offset = '', $sort = 'row_order', $order = 'ASC')
    {

        
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $multipleWhere = '';

        if (isset($_POST['offset'])) {
            $offset = $_POST['offset'];
        }
        if (isset($_POST['limit'])) {
            $limit = $_POST['limit'];
        }

        if (isset($_POST['sort'])) {
            if ($_POST['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_POST['sort'];
            }
        }
        if (isset($_POST['order'])) {
            $order = $_POST['order'];
        }

        if (isset($_POST['search']) and $_POST['search'] != '') {
            $search = $_POST['search'];
            $multipleWhere = ['`id`' => $search, '`type`' => $search];
        }


        $count_res = $this->db->select(' COUNT(id) as `total` ')->where('branch_id',$branch_id);

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->or_where($multipleWhere);
        }

        $slider_count = $count_res->get('sliders')->result_array();

        foreach ($slider_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' * ');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->or_like($multipleWhere);
        }

        $slider_search_res = $search_res->where('branch_id',$branch_id)->order_by($sort, $order)->limit($limit, $offset)->get('sliders')->result_array();

        foreach ($slider_search_res as &$row) {
            $row['relative_path'] = $row['image'];
            $row['image'] = get_image_url($row['image'], 'thumb', 'sm', true);
        }

        return json_decode(json_encode($slider_search_res), 1);
    }


}

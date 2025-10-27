<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Category_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    // public function get_categories($id = NULL, $limit = NULL, $offset = NULL, $sort = 'row_order', $order = 'ASC', $has_child_or_item = 'true', $slug = '', $ignore_status = '', $search = '')
    // {
    //     $level = 0;
    //     $session_branch_id = $this->session->userdata('branch_id');
    //     $branch_id = (isset($session_branch_id) && !empty($session_branch_id)) ? $session_branch_id : $_POST['branch_id'];
    //     $multipleWhere = [];
    //     if (isset($search) && !empty($search) && $search != "") {
    //         $multipleWhere = [
    //             '`c1.name`' => $search
    //         ];
    //     }

    //     if ($ignore_status == 1) {
    //         $where = (isset($id) && !empty($id)) ? ['c1.id' => $id] : ['c1.parent_id' => 0];
    //     } else {
    //         $where = (isset($id) && !empty($id)) ? ['c1.id' => $id, 'c1.status' => 1] : ['c1.parent_id' => 0];
    //     }

    //     $this->db->select('c1.*');
    //     if (isset($multipleWhere) && !empty($multipleWhere)) {
    //         $this->db->group_start();
    //         $this->db->or_like($multipleWhere);
    //         $this->db->group_end();
    //     }
    //     $this->db->where($where);
    //     $this->db->where("FIND_IN_SET('$branch_id', c1.branch_id) > 0");

    //     if (!empty($slug)) {
    //         $this->db->where('c1.slug', $slug);
    //     }
    //     if ($has_child_or_item == 'false') {
    //         $this->db->join('categories c2', 'c2.parent_id = c1.id', 'left');
    //         $this->db->join('products p', ' p.category_id = c1.id', 'left');
    //         $this->db->group_start();
    //         $this->db->or_where(['c1.id ' => ' p.category_id ', ' c2.parent_id ' => ' c1.id '], NULL, FALSE);
    //         $this->db->group_End();
    //         $this->db->group_by('c1.id');
    //     }

    //     if (!empty($limit) || !empty($offset)) {

    //         $this->db->offset($offset);
    //         $this->db->limit($limit);
    //     }

    //     $this->db->order_by($sort ?? '', $order ?? '');

    //     $parent = $this->db->where(['c1.status' => 1])->get('categories c1');

    //     $categories = $parent->result();
    //     $count_res = $this->db->where("FIND_IN_SET('$branch_id', c1.branch_id) > 0")->count_all_results('categories c1');

    //     $i = 0;
    //     print_r($this->db->last_query());

    //     foreach ($categories as $p_cat) {

    //         $categories[$i]->children = $this->sub_categories($p_cat->id, $level);
    //         $categories[$i]->text = output_escaping(str_replace('\r\n', '&#13;&#10;', $p_cat->name));
    //         $categories[$i]->name = output_escaping(str_replace('\r\n', '&#13;&#10;', $categories[$i]->name));
    //         $categories[$i]->state = ['opened' => true];
    //         $categories[$i]->icon = "jstree-folder";
    //         $categories[$i]->level = $level;
    //         $categories[$i]->relative_path = $categories[$i]->image;
    //         $categories[$i]->image = get_image_url($categories[$i]->image, 'thumb', 'sm');
    //         $i++;
    //     }
    //     if (isset($categories[0])) {
    //         $categories[0]->total = $count_res;

    //     }

    //     return json_decode(json_encode($categories), 1);
    // }

    public function get_categories($id = NULL, $limit = NULL, $offset = NULL, $sort = 'row_order', $order = 'ASC', $has_child_or_item = 'true', $slug = '', $ignore_status = '', $search = '', $from_app = 'false')
    {
        $level = 0;
        $session_branch_id = $this->session->userdata('branch_id');
        $branch_id = (isset($session_branch_id) && !empty($session_branch_id)) ? $session_branch_id : $_POST['branch_id'];
        $multipleWhere = [];
        if (isset($search) && !empty($search) && $search != "") {
            $multipleWhere = [
                '`c1.name`' => $search
            ];
        }

        if ($ignore_status == 1) {
            $where = (isset($id) && !empty($id)) ? ['c1.id' => $id] : ['c1.parent_id' => 0];
        } else {
            $where = (isset($id) && !empty($id)) ? ['c1.id' => $id, 'c1.status' => 1] : ['c1.parent_id' => 0, 'c1.status' => 1];
        }

        $this->db->select('c1.*');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_start();
            $this->db->or_like($multipleWhere);
            $this->db->group_end();
        }
        $this->db->where($where);
        $this->db->where("FIND_IN_SET('$branch_id', c1.branch_id) > 0");

        // Join with products table to ensure categories have active products
        if($from_app == 'true'){

            $this->db->join('products p', 'p.category_id = c1.id', 'inner');
            $this->db->where('p.status', 1);
        }

        if (!empty($slug)) {
            $this->db->where('c1.slug', $slug);
        }
        if ($has_child_or_item == 'false') {
            $this->db->join('categories c2', 'c2.parent_id = c1.id', 'left');
            $this->db->group_start();
            $this->db->or_where(['c1.id ' => ' p.category_id ', ' c2.parent_id ' => ' c1.id '], NULL, FALSE);
            $this->db->group_End();
            $this->db->group_by('c1.id');
        }

        if (!empty($limit) || !empty($offset)) {
            $this->db->offset($offset);
            $this->db->limit($limit);
        }

        $this->db->order_by($sort ?? '', $order ?? '');
        $this->db->group_by('c1.id'); // Ensure unique categories

        $parent = $this->db->get('categories c1');

        $categories = $parent->result();
        $count_res = $this->db->where("FIND_IN_SET('$branch_id', c1.branch_id) > 0")
            ->join('products p', 'p.category_id = c1.id', 'inner')
            ->where('p.status', 1)
            ->count_all_results('categories c1');

        $i = 0;
        foreach ($categories as $p_cat) {
            $categories[$i]->children = $this->sub_categories($p_cat->id, $level);
            $categories[$i]->text = output_escaping(str_replace('\r\n', "\n", $p_cat->name));
            $categories[$i]->name = output_escaping(str_replace('\r\n', "\n", $categories[$i]->name));
            $categories[$i]->state = ['opened' => true];
            $categories[$i]->icon = "jstree-folder";
            $categories[$i]->level = $level;
            $categories[$i]->relative_path = $categories[$i]->image;
            $categories[$i]->image = get_image_url($categories[$i]->image, 'thumb', 'sm');
            $i++;
        }
        if (isset($categories[0])) {
            $categories[0]->total = $count_res;
        }

        return json_decode(json_encode($categories), 1);
    }

    public function sub_categories($id, $level)
    {
        $level = $level + 1;
        $this->db->select('c1.*');
        $this->db->from('categories c1');
        $this->db->where(['c1.parent_id' => $id, 'c1.status' => 1]);
        $child = $this->db->get();
        $categories = $child->result();
        $i = 0;
        foreach ($categories as $p_cat) {

            $categories[$i]->children = $this->sub_categories($p_cat->id, $level);
            $categories[$i]->text = output_escaping($p_cat->name);
            $categories[$i]->state = ['opened' => true];
            $categories[$i]->level = $level;
            $categories[$i]->image = get_image_url($categories[$i]->image, 'thumb', 'md');
            $categories[$i]->banner = get_image_url($categories[$i]->banner, 'thumb', 'md');
            $i++;
        }
        return $categories;
    }


    public function delete_category($id)
    {
        $this->db->trans_start();
        $id = escape_array($id);
        $this->db->set('status', NULL)->where('id', $id)->update('categories');
        $this->db->trans_complete();
        $response = FALSE;
        if ($this->db->trans_status() === TRUE) {
            $response = TRUE;
            $this->db->set('category_id', '1')->where('category_id', $id)->update('products');
        }
        return $response;
    }


    public function get_category_list()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $multipleWhere = '';
        $where = ['status !=' => NULL];

        if (isset($_GET['id']))
            $where['parent_id'] = $_GET['id'];
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
            $multipleWhere = ['`id`' => $search, '`name`' => $search];
        }


        $count_res = $this->db->select('COUNT(id) as `total` ');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->or_like($multipleWhere);
        }
        $branch_id = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : "";
        if (isset($where) && !empty($where)) {
            $where['branch_id'] = $branch_id;
            $count_res->where($where);
        }
        $cat_count = $count_res->get('categories')->result_array();
        foreach ($cat_count as $row) {
            $total = $row['total'];
        }
        $search_res = $this->db->select(' * ');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->or_like($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }
        if (isset($where) && !empty($where)) {
            $where['branch_id'] = $branch_id;
            $search_res->where($where);
        }
        $cat_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('categories')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($cat_search_res as $row) {

            if (!$this->ion_auth->is_partner()) {
                $operate = '<a href="' . base_url('admin/category/create_category' . '?edit_id=' . $row['id']) . '" class=" btn btn-success btn-xs mr-1 mb-1" title="Edit" data-id="' . $row['id'] . '" data-url="admin/category/create_category"><i class="fa fa-pen"></i></a>';
                $operate .= '<a class="delete-categoty btn btn-danger btn-xs mr-1 mb-1" title="Delete" href="javascript:void(0)" data-id="' . $row['id'] . '" ><i class="fa fa-trash"></i></a>';
            }
            if ($row['status'] == '1') {
                $tempRow['status'] = '<a class="badge badge-success text-white" >Active</a>';
                if (!$this->ion_auth->is_partner()) {
                    $operate .= '<a class="btn btn-warning btn-xs update_active_status mr-1 mb-1" data-table="categories" title="Deactivate" href="javascript:void(0)" data-id="' . $row['id'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-eye-slash"></i></a>';
                }
            } else {
                $tempRow['status'] = '<a class="badge badge-danger text-white" >Inactive</a>';
                if (!$this->ion_auth->is_partner()) {
                    $operate .= '<a class="btn btn-primary mr-1 mb-1 btn-xs update_active_status" data-table="categories" href="javascript:void(0)" title="Active" data-id="' . $row['id'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-eye"></i></a>';
                }
            }

            // image size
            $bannerWidth = '200px';
            $bannerHeight = '100px';

            $tempRow['id'] = $row['id'];
            $tempRow['name'] = output_escaping($row['name']);
            $branch_name = fetch_details(['id' => $row['branch_id']], 'branch', 'branch_name');
            $tempRow['branch'] = stripslashes($branch_name[0]['branch_name']);


            if (empty($row['image']) || file_exists(FCPATH . $row['image']) == FALSE) {
                $row['image'] = base_url() . NO_IMAGE;
                $row['image_main'] = base_url() . NO_IMAGE;
            } else {
                $row['image_main'] = base_url($row['image']);
                $row['image'] = get_image_url($row['image'], 'thumb', 'sm');
            }
            $tempRow['image'] = "<div class='image-box-100' ><a href='" . $row['image_main'] . "' data-toggle='lightbox' data-gallery='gallery'> <img class='rounded' src='" . $row['image'] . "' ></a></div>";

            if (empty($row['banner']) || file_exists(FCPATH . $row['banner']) == FALSE) {
                $row['banner'] = base_url() . NO_IMAGE;
                $row['banner_main'] = base_url() . NO_IMAGE;
            } else {
                $row['banner_main'] = base_url($row['banner']);
                $row['banner'] = get_image_url($row['banner'], 'thumb', 'sm');
            }
            $tempRow['banner'] = "<div class='center-image image-box-100'><a href='" . $row['banner_main'] . "' data-toggle='lightbox' data-gallery='gallery'> <img src='" . $row['banner'] . "' class='img-fluid w-50' width='" . $bannerWidth . "' height='" . $bannerHeight . "'></a></div>";

            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    public function add_category($data)
    {
        $data = escape_array($data);
        // print_R($branch_ids);
        // die;
        
        $category_name = $data['category_input_name'];
        $category_image = $data['category_input_image'];
        $edit_category_id = isset($data['edit_category']) ? $data['edit_category'] : '';
        
        if (isset($edit_category_id) && !empty($edit_category_id)) {
            $branch_ids = $_SESSION['branch_id'];
            $catData = [
                'name' => $category_name,
                'slug' => create_unique_slug($category_name, 'categories'),
            ];
            
            if (!empty($category_image)) {
                $catData['image'] = $category_image;
            }
            
            $this->db->where('id', $edit_category_id)->update('categories', $catData);
        } else {
            
            $branch_ids = (isset($data['branch']) && !empty($data['branch'])) ? $data['branch'] : array();
            // Add new categories
            // die;
            $category_ids = [];
            foreach ($branch_ids as $branchId) {
                // print_R($branchId);

                $catData = [
                    'name' => $category_name,
                    'parent_id' => '0',
                    'slug' => create_unique_slug($category_name, 'categories'),
                    'status' => '1',
                    'branch_id' => $branchId,
                ];

                if (!empty($category_image)) {
                    $catData['image'] = $category_image;
                }

                $this->db->insert('categories', $catData);
                $category_ids[] = $this->db->insert_id();
                // return $this->db->insert_id();
            }
            // print_r($category_ids);
            return $category_ids;
        }
    }

    public function get_categories_branch_wise($id = NULL, $limit = '', $offset = '', $sort = 'row_order', $order = 'ASC', $has_child_or_item = 'true', $slug = '', $ignore_status = '')
    {
        $level = 0;
        $session_branch_id = $this->session->userdata('branch_id');
        $branch_id = (isset($session_branch_id) && !empty($session_branch_id)) ? $session_branch_id : $_POST['branch_id'];
        $api_branch_id = isset($_POST['branch_id']) ? $_POST['branch_id'] : '';


        if ($ignore_status == 1) {
            $where = (isset($id) && !empty($id)) ? ['c1.id' => $id] : ['c1.parent_id' => 0];
        } else {
            $where = (isset($id) && !empty($id)) ? ['c1.id' => $id, 'c1.status' => 1] : ['c1.parent_id' => 0];
        }

        $this->db->select('c1.*');
        $this->db->where($where);
        // $this->db->where("FIND_IN_SET('$branch_id', c1.branch_id) > 0");
        if (!empty($api_branch_id)) {
            $this->db->where("FIND_IN_SET('$api_branch_id', c1.branch_id) > 0");
        } else {
            $this->db->where("FIND_IN_SET('$branch_id', c1.branch_id) > 0");
        }

        if (!empty($slug)) {
            $this->db->where('c1.slug', $slug);
        }
        if ($has_child_or_item == 'false') {
            $this->db->join('categories c2', 'c2.parent_id = c1.id', 'left');
            $this->db->join('products p', ' p.category_id = c1.id', 'left');
            $this->db->group_start();
            $this->db->or_where(['c1.id ' => ' p.category_id ', ' c2.parent_id ' => ' c1.id '], NULL, FALSE);
            $this->db->group_End();
            $this->db->group_by('c1.id');
        }

        if (!empty($limit) || !empty($offset)) {
            $this->db->offset($offset);
            $this->db->limit($limit);
        }

        $this->db->order_by($sort ?? '', $order ?? '');

        $parent = $this->db->get('categories c1');

        $categories = $parent->result();
        // $count_res = $this->db->where("FIND_IN_SET('$branch_id', c1.branch_id) > 0")->count_all_results('categories c1');
        if (!empty($api_branch_id)) {
            $count_res = $this->db->where("FIND_IN_SET('$api_branch_id', c1.branch_id) > 0")->count_all_results('categories c1');
        } else {

            $count_res = $this->db->where("FIND_IN_SET('$branch_id', c1.branch_id) > 0")->count_all_results('categories c1');
        }
        $i = 0;


        foreach ($categories as $p_cat) {

            $categories[$i]->children = $this->sub_categories($p_cat->id, $level);
            $categories[$i]->text = output_escaping(str_replace('\r\n', '&#13;&#10;', $p_cat->name));
            $categories[$i]->name = output_escaping(str_replace('\r\n', '&#13;&#10;', $categories[$i]->name));
            $categories[$i]->state = ['opened' => true];
            $categories[$i]->icon = "jstree-folder";
            $categories[$i]->level = $level;
            $categories[$i]->image = get_image_url($categories[$i]->image, 'thumb', 'sm');
            $i++;
        }
        if (isset($categories[0])) {
            $categories[0]->total = $count_res;
        }

        return json_decode(json_encode($categories), 1);
    }
}

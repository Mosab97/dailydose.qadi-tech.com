<?php


defined('BASEPATH') or exit('No direct script access allowed');


class Product_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }
    public function add_product($data, $tags = [])
    {

        $data = escape_array($data);
        $tags = (!empty($tags)) ? escape_array($tags) : [];
        $pro_type = ($data['product_type'] == 'simple_product') ? 'simple_product' : 'variable_product';
        $short_description = $data['short_description'];
        $category_id = $data['product_category_id'];
        $branch_id = $data['branch_id'];
        $indicator = isset($data['indicator']) && $data['indicator'] !== '' ? $data['indicator'] : '0';
        $highlights = (!empty($data['highlights'])) ? $data['highlights'] : "";
        $slug = create_unique_slug($data['pro_input_name'], 'products');
        $main_image_name = $data['pro_input_image'];
        $start_time = $data['product_start_time'];
        $end_time = $data['product_end_time'];
        $total_allowed_quantity = (isset($data['total_allowed_quantity']) && !empty($data['total_allowed_quantity'])) ? $data['total_allowed_quantity'] : null;
        $minimum_order_quantity = (isset($data['minimum_order_quantity']) && !empty($data['minimum_order_quantity'])) ? $data['minimum_order_quantity'] : 1;
        $tax = (isset($data['pro_input_tax']) && $data['pro_input_tax'] != 0 && !empty($data['pro_input_tax'])) ? $data['pro_input_tax'] : 0;
        $calories = (isset($data['calories']) && !empty($data['calories'])) ? $data['calories'] : 0;

        $pro_data = [
            'name' => $data['pro_input_name'],
            'short_description' => $short_description,
            'slug' => $slug,
            'type' => $pro_type,
            'category_id' => $category_id,
            'indicator' => $indicator,
            'image' => $main_image_name,
            'total_allowed_quantity' => $total_allowed_quantity,
            'minimum_order_quantity' => $minimum_order_quantity,
            'highlights' => $highlights,
            'calories' => $calories,
            'start_time' => isset($start_time) && !empty($start_time) ? $start_time : '00:00:00',
            'end_time' => isset($end_time) && !empty($end_time) ? $end_time : '00:00:00',
            'tax' => $tax
        ];

        if ($data['product_type'] == 'simple_product') {

            if (isset($data['simple_product_stock_status']) && empty($data['simple_product_stock_status'])) {

                $pro_data['stock_type'] = NULL;
                $pro_data['stock'] = NULL;
                $pro_data['availability'] = NULL;
            }

            if (isset($data['simple_product_stock_status']) && in_array($data['simple_product_stock_status'], array('0', '1'))) {
                $pro_data['stock_type'] = '0';
            }

            if (isset($data['simple_product_stock_status']) && in_array($data['simple_product_stock_status'], array('0', '1'))) {
                if (!empty($data['product_sku'])) {
                    $pro_data['sku'] = $data['product_sku'];
                }
                $pro_data['stock'] = $data['product_total_stock'];
                $pro_data['availability'] = $data['simple_product_stock_status'];
            }
        }

        if ((isset($data['variant_stock_status']) || $data['variant_stock_status'] == '' || empty($data['variant_stock_status']) || $data['variant_stock_status'] == ' ') && $data['product_type'] == 'variable_product') {
            $pro_data['stock_type'] = NULL;
        }

        if (isset($data['variant_stock_level_type']) && !empty($data['variant_stock_level_type'])) {
            $pro_data['stock_type'] = ($data['variant_stock_level_type'] == 'product_level') ? 1 : 2;
        }

        if (isset($data['cod_allowed']) && $data['cod_allowed']) {
            $pro_data['cod_allowed'] = '1';
        } else {
            $pro_data['cod_allowed'] = '0';
        }
        if (isset($data['available_time']) && $data['available_time']) {
            $pro_data['available_time'] = '1';
        } else {
            $pro_data['available_time'] = '0';
        }
        if (isset($data['is_cancelable']) && $data['is_cancelable']) {
            $pro_data['is_cancelable'] = '1';
            $pro_data['cancelable_till'] = $data['cancelable_till'];
        }
        if (isset($data['is_spicy']) && $data['is_spicy']) {
            $pro_data['is_spicy'] = '1';
        }

        if (isset($data['is_prices_inclusive_tax']) && $data['is_prices_inclusive_tax']) {
            $pro_data['is_prices_inclusive_tax'] = '1';
        } else {
            $pro_data['is_prices_inclusive_tax'] = '0';
        }

        if (isset($data['edit_product_id'])) {
            if (empty($main_image_name)) {
                unset($pro_data['image']);
            }
            if (!isset($data['is_cancelable'])) {
                $pro_data['is_cancelable'] = '0';
                $pro_data['cancelable_till'] = '';
            }
            if (isset($data['cod_allowed']) && $data['cod_allowed']) {
                $pro_data['cod_allowed'] = '1';
            } else {
                $pro_data['cod_allowed'] = '0';
            }

            $this->db->set($pro_data)->where('id', $data['edit_product_id'])->update('products');
        } else {

            $branch_ids = isset($_POST['branch_id']) ? array($_POST['branch_id']) : array($_SESSION['branch_id']);



            $last_product_id = [];
            for ($i = 0; $i < count($branch_ids); $i++) {
                $pro_data['branch_id'] = $branch_ids[$i];
                $this->db->insert('products', $pro_data);
                $product_ids = $this->db->insert_id();
                array_push($last_product_id, $product_ids);
            }
        }
        //    print_R($product_ids);
        $p_id = (isset($data['edit_product_id'])) ? $data['edit_product_id'] : $this->db->insert_id();
        $pro_variance_data['product_id'] = $p_id;



        $tags = [];
        if (isset($data['tags']) && !empty($data['tags'])) {
            foreach ($data['tags'] as $row) {
                $tempRow = [];
                $tempRow['tag_id'] = $row;

                if (!isset($data['edit_product_id'])) {
                    // If it's a new product, associate each tag with the corresponding product ID
                    for ($x = 0; $x < count($last_product_id); $x++) {
                        $tempRow['product_id'] = $last_product_id[$x];
                        $tags[] = $tempRow;
                    }
                } else {
                    // If it's an edit operation, associate each tag with the edit product ID
                    $tempRow['product_id'] = $data['edit_product_id'];
                    $tags[] = $tempRow;
                }
            }
        }

        if (!empty($tags)) {
            if (isset($data['edit_product_id'])) {
                delete_details(['product_id' => $data['edit_product_id']], 'product_tags');
            }
            $this->db->insert_batch('product_tags', $tags);
        }

        $add_ons = [];
        if (isset($data['product_add_ons']) && !empty($data['product_add_ons'])) {
            foreach ($data['product_add_ons'] as $row) {
                $tempRow = [];
                $tempRow['title'] = $row['title'];
                $tempRow['description'] = $row['description'];
                $tempRow['price'] = $row['price'];
                $tempRow['calories'] = $row['calories'];
                $tempRow['status'] = $row['status'];

                if (!isset($data['edit_product_id'])) {
                    // If it's a new product, associate each add-on with the corresponding product ID
                    for ($x = 0; $x < count($last_product_id); $x++) {
                        $tempRow['product_id'] = $last_product_id[$x];
                        $add_ons[] = $tempRow;
                    }
                } else {
                    // If it's an edit operation, associate each add-on with the edit product ID
                    $tempRow['product_id'] = $data['edit_product_id'];
                    $add_ons[] = $tempRow;
                }
            }
        }

        if (!empty($add_ons)) {
            $this->db->insert_batch('product_add_ons', $add_ons);
        }

        $pro_attr_data = [
            'product_id' => $p_id,
            'attribute_value_ids' => strval($data['attribute_values']),
        ];

        if (isset($data['edit_product_id'])) {
            $this->db->where('product_id', $data['edit_product_id'])->update('product_attributes', $pro_attr_data);
        } else {
            $this->db->insert('product_attributes', $pro_attr_data);
        }
        if ($pro_type == 'simple_product') {
            $pro_variance_data = [
                // 'product_id' => $p_id,
                'price' => $data['simple_price'],
                'special_price' => (isset($data['simple_special_price']) && !empty($data['simple_special_price'])) ? $data['simple_special_price'] : '0'
            ];

            if (isset($data['edit_product_id'])) {

                if (isset($_POST['reset_settings']) && trim($_POST['reset_settings']) == '1') {
                    $this->db->insert('product_variants', $pro_variance_data);
                } else {
                    $this->db->where('product_id', $data['edit_product_id'])->update('product_variants', $pro_variance_data);
                }
            } else {

                for ($k = 0; $k < count($last_product_id); $k++) {
                    $pro_variance_data['product_id'] = $last_product_id[$k];
                    $this->db->insert('product_variants', $pro_variance_data);
                }
            }
        } else {
            $flag = " ";
            if (isset($data['variant_stock_status']) && $data['variant_stock_status'] == '0') {

                if ($data['variant_stock_level_type'] == "product_level") {
                    $flag = "product_level";
                    $pro_variance_data['stock'] = $data['total_stock_variant_type'];
                    $pro_variance_data['availability'] = $data['variant_status'];
                    $variant_price = $data['variant_price'];
                    $variant_special_price = (isset($data['variant_special_price']) && !empty($data['variant_special_price'])) ? $data['variant_special_price'] : '0';
                }
            } else {
                $variant_price = $data['variant_price'];
                $variant_special_price = (isset($data['variant_special_price']) && !empty($data['variant_special_price'])) ? $data['variant_special_price'] : '0';
            }

            if (!empty($data['variants_ids'])) {
                $variants_ids = $data['variants_ids'];
                if (isset($data['edit_variant_id']) && !empty($data['edit_variant_id'])) {
                    $this->db->set('status', 7)->where('product_id', $data['edit_product_id'])->where('status !=', 0)->where_not_in('id', $data['edit_variant_id'])->update('product_variants');
                }

                if (!isset($data['edit_variant_id']) && isset($data['edit_product_id'])) {
                    $this->db->set('status', 7)->where('product_id', $data['edit_product_id'])->where('status !=', 0)->update('product_variants');
                }
                for ($i = 0; $i < count($variants_ids); $i++) {
                    $value = str_replace(' ', ',', trim($variants_ids[$i]));
                    $pro_variance_data['price'] = $variant_price[$i];
                    $pro_variance_data['special_price'] = (isset($variant_special_price[$i]) && !empty($variant_special_price[$i])) ? $variant_special_price[$i] : '0';
                    $pro_variance_data['attribute_value_ids'] = $value;
                    if (isset($data['edit_variant_id'][$i]) && !empty($data['edit_variant_id'][$i])) {
                        $this->db->where('id', $data['edit_variant_id'][$i])->update('product_variants', $pro_variance_data);
                    } else {
                        for ($j = 0; $j < count($last_product_id); $j++) {
                            $pro_variance_data['product_id'] = $last_product_id[$j];
                            $this->db->insert('product_variants', $pro_variance_data);
                        }
                    }
                }
            }
        }
        if (isset($product_ids)) {
            return $product_ids;
        }
    }

    public function get_product_details($flag = NULL, $p_status = NULL, $indicator = NULL, $product_type = NULL)
    {

        $settings = get_settings('system_settings', true);
        $low_stock_limit = isset($settings['low_stock_limit']) ? $settings['low_stock_limit'] : 5;
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
                $sort = "pid";  // Sort by product ID since we're grouping by product
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = trim($_GET['search']);
            $multipleWhere = ['p.`name`' => $search, 'p.`id`' => $search];
        }

        if (isset($_GET['category_id']) || isset($_GET['search'])) {
            if (isset($_GET['search']) and $_GET['search'] != '') {
                $multipleWhere['p.`category_id`'] = $search;
            }

            if (isset($_GET['category_id']) and $_GET['category_id'] != '') {
                $category_id = $_GET['category_id'];
            }
        }

        $count_res = $this->db->select(' COUNT(DISTINCT(p.id)) as `total` ')->where('p.branch_id', $_SESSION['branch_id'])->join(" categories c", "p.category_id=c.id ")->join('product_variants', 'product_variants.product_id = p.id');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_Start();
            $count_res->or_like($multipleWhere);
            $count_res->group_End();
        }

        if (isset($where) && !empty($where)) {

            $count_res->where($where);
        }
        if ($flag == 'low') {
            $count_res->group_Start();
            $where = "p.stock_type is  NOT NULL";
            $where = ['p.branch_id' => $_SESSION['branch_id']];
            $count_res->where($where);
            $count_res->where('p.stock <=', $low_stock_limit);
            $count_res->where('p.availability  =', '1');
            $count_res->or_where('product_variants.stock <=', $low_stock_limit);
            $count_res->where('product_variants.availability  =', '1');
            $count_res->group_End();
        }

        if (isset($indicator) && $indicator != "") {
            $count_res->where("p.indicator", $indicator);
        }
        if (isset($product_type) && $product_type != "") {
            $count_res->where("p.type", $product_type);
        }
        if (isset($p_status) && $p_status != "") {
            $count_res->where("p.status", $p_status);
        }

        if ($flag == 'sold') {
            $count_res->group_Start();
            $where = "p.stock_type is  NOT NULL";
            $where = ['p.branch_id' => $_SESSION['branch_id']];
            $count_res->where($where);
            $count_res->where('p.stock ', '0');
            $count_res->where('p.availability ', '0');
            $count_res->or_where('product_variants.stock ', '0');
            $count_res->where('product_variants.availability ', '0');
            $count_res->group_End();
        }

        if (isset($category_id) && !empty($category_id)) {
            $count_res->group_Start();
            $count_res->or_where('p.category_id', $category_id);
            $count_res->or_where('c.parent_id', $category_id);
            $count_res->group_End();
        }

        $product_count = $count_res->get('products p')->result_array();
        foreach ($product_count as $row) {
            $total = $row['total'];
        }
        $search_res = $this->db->select('MIN(product_variants.id) AS id, p.id as pid ,p.rating,p.no_of_ratings,p.name,p.branch_id as branch_id ,p.type, p.image, p.status, MIN(product_variants.price) as price, MIN(product_variants.special_price) as special_price, SUM(product_variants.stock) as stock, c.name as category ')
            ->join(" categories c", "p.category_id=c.id ")
            ->join('product_variants', 'product_variants.product_id = p.id');
        $search_res->where('p.branch_id', $_SESSION['branch_id']);
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_Start();
            $search_res->or_like($multipleWhere);
            $search_res->group_End();
        }

        if (isset($where) && !empty($where)) {

            $search_res->where($where);
        }

        if ($flag != null && $flag == 'low') {

            $search_res->group_Start();
            $where = "p.stock_type is  NOT NULL";
            $where = ['p.branch_id' => $_SESSION['branch_id']];
            $search_res->where($where);
            $search_res->where('p.stock <=', $low_stock_limit);
            $search_res->where('p.availability  =', '1');
            $search_res->or_where('product_variants.stock <=', $low_stock_limit);
            $search_res->where('product_variants.availability  =', '1');
            $search_res->group_End();
        }
        if ($flag != null && $flag == 'sold') {
            $search_res->group_Start();
            $where = "p.stock_type is  NOT NULL";
            $where = ['p.branch_id' => $_SESSION['branch_id']];
            $search_res->where($where);
            $search_res->where('p.stock ', '0');
            $search_res->where('p.availability ', '0');
            $search_res->or_where('product_variants.stock ', '0');
            $search_res->where('product_variants.availability ', '0');
            $search_res->group_End();
        }

        if (isset($category_id) && !empty($category_id)) {
            $search_res->group_Start();
            $search_res->or_where('p.category_id', $category_id);
            $search_res->or_where('c.parent_id', $category_id);
            $search_res->group_End();
        }
        if (isset($partner_id) && $partner_id != "") {
            $search_res->where("p.partner_id", $partner_id);
        }

        if (isset($p_status) && $p_status != NULL) {
            $search_res->where("p.status", $p_status);
        }


        if (isset($indicator) && $indicator != "") {
            $search_res->where("p.indicator", $indicator);
        }
        if (isset($product_type) && $product_type != "") {
            $search_res->where("p.type", $product_type);
        }

        $pro_search_res = $search_res->group_by('pid')->order_by($sort, $order)->limit($limit, $offset)->get('products p')->result_array();
        // print_r($this->db->last_query());
        $currency = get_settings('currency');
        $bulkData = array();
        $bulkData['total'] = $total;

        $rows = array();
        $tempRow = array();

        foreach ($pro_search_res as $row) {

            $row = output_escaping($row);

            $operate = "<a href='view-product?edit_id=" . $row['pid'] . "'  class='btn btn-primary btn-xs mr-1 mb-1' title='View'><i class='fa fa-eye'></i></a>";
            $operate .= " <a href='create-product?edit_id=" . $row['pid'] . "' data-id=" . $row['pid'] . " class='btn btn-success btn-xs mr-1 mb-1' title='Edit' ><i class='fa fa-pen'></i></a>";
            if ($row['status'] == '1') {
                $tempRow['status'] = '<a class="badge badge-success text-white" >Active</a>';
                $operate .= '<a class="btn btn-warning btn-xs update_active_status mr-1 mb-1" data-table="products" title="Deactivate" href="javascript:void(0)" data-id="' . $row['pid'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-toggle-on"></i></a>';
            } else if ($row['status'] == '0') {
                $tempRow['status'] = '<a class="badge badge-danger text-white" >Inactive</a>';
                $operate .= '<a class="btn btn-secondary mr-1 mb-1 btn-xs update_active_status" data-table="products" href="javascript:void(0)" title="Active" data-id="' . $row['pid'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-toggle-off"></i></a>';
            }
            $operate .= ' <a href="javascript:void(0)" id="delete-product" data-id=' . $row['pid'] . ' class="btn btn-danger mr-1 mb-1 btn-xs"><i class="fa fa-trash"></i></a>';
            $operate .= " <a href='javascript:void(0)' data-id=" . $row['pid'] . " data-toggle='modal' data-target='#product-rating-modal' class='btn btn-success btn-xs mr-1 mb-1' title='View Ratings' ><i class='fa fa-star'></i></a>";

            $attr_values = get_variants_values_by_pid($row['pid']);
            $tempRow['id'] = $row['pid'];
            $branch_name = fetch_details(['id' => $row['branch_id']], 'branch', 'branch_name');

            $tempRow['branch'] = stripslashes($branch_name[0]['branch_name']);
            $tempRow['varaint_id'] = $row['id'];
            $tempRow['name'] = stripslashes($row['name']);
            // $tempRow['name'] = stripslashes($row['name']) . '<br><small>' . ucwords(str_replace('_', ' ', $row['type'])) . '</small>';
            $tempRow['category'] = $row['category'];
            $tempRow['type'] = $row['type'];
            $tempRow['price'] = ($row['special_price'] == null || $row['special_price'] == '0') ? $currency . $row['price'] : $currency . $row['special_price'];
            $tempRow['stock'] = $row['stock'];
            $variations = '';
            foreach ($attr_values as $variants) {
                if (isset($attr_values[0]['attr_name'])) {

                    if (!empty($variations)) {
                        $variations .= '---------------------<br>';
                    }
                    $attr_name = explode(',', $variants['attr_name']);
                    $varaint_values = explode(',', $variants['variant_values']);
                    for ($i = 0; $i < count($attr_name); $i++) {
                        if (!empty($attr_name[$i])) {

                            $variations .= '<b>' . $attr_name[$i] . '</b> : ' . $varaint_values[$i] . '<br>';
                        }
                    }
                }
            }

            $tempRow['variations'] = (!empty($variations)) ? $variations : '-';
            $row['image'] = get_image_url($row['image'], 'thumb', 'sm');
            $tempRow['image'] = '<div class="mx-auto product-image image-box-100"><a href=' . $row['image'] . ' data-toggle="lightbox" data-gallery="gallery">
            <img src=' . $row['image'] . ' class="img-fluid rounded"></a></div>';

            $tempRow['rating'] = '<input type="text" class="kv-fa rating-loading" value="' . $row['rating'] . '" data-size="xs" title="" readonly> <span> (' . $row['rating'] . '/' . $row['no_of_ratings'] . ') </span>';

            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }


    public function get_product_add_ons($product_id = "", $from_api = false)
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = '';
        $where = [];

        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        if (isset($_GET['sort'])) {
            if ($_GET['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_GET['sort'];
            }
        }
        if (isset($_GET['order'])) {
            $order = $_GET['order'];
        }

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                'id' => $search,
                'title' => $search,
                'description' => $search,
                'price' => $search,
                'calories' => $search
            ];
        }

        if (isset($product_id) && !empty($product_id)) {
            $where = ['product_id' => $product_id];
        }

        $count_res = $this->db->select('COUNT(id) as `total`');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_start();
            $count_res->or_like($multipleWhere);
            $count_res->group_end();
        }
        if (isset($where) && !empty($where)) {
            $count_res->group_start();
            $count_res->where($where);
            $count_res->group_end();
        }

        $cat_count = $count_res->get('product_add_ons')->result_array();

        foreach ($cat_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select('*');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_start();
            $search_res->or_like($multipleWhere);
            $search_res->group_end();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $cat_search_res = $search_res->order_by($sort, "desc")->limit($limit, $offset)->get('product_add_ons')->result_array();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($cat_search_res as $row) {
            $row = output_escaping($row);
            $operate = "";
            if ($row['status'] == '1') {
                $operate .= '<a class="btn btn-success btn-xs update_active_status mr-1" data-table="product_add_ons" title="Deactivate" href="javascript:void(0)" data-id="' . $row['id'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-toggle-on"></i></a>';
            } else {
                $operate .= '<a class="btn btn-secondary mr-1 btn-xs update_active_status" data-table="product_add_ons" href="javascript:void(0)" title="Active" data-id="' . $row['id'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-toggle-off"></i></a>';
            }
            $operate .= ' <a href="javascript:void(0)" id="delete-add-ons" data-id=' . $row['id'] . ' class="btn btn-danger mr-1 mb-1 btn-xs"><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row['id'];
            $tempRow['product_id'] = $row['product_id'];
            $tempRow['title'] = $row['title'];
            $tempRow['description'] = $row['description'];
            $tempRow['price'] = $row['price'];
            $tempRow['calories'] = $row['calories'];
            if ($from_api == true) {
                $tempRow['status'] = $row['status'];
                // $tempRow['allow_multiple_qty'] = $row['allow_multiple_qty'];
            } else {
                $tempRow['status'] = ($row['status'] == '1') ? '<a class="badge badge-success text-white" >Active</a>' : '<a class="badge badge-danger text-white" >Inactive</a>';
                // $tempRow['allow_multiple_qty'] = ($row['allow_multiple_qty'] == '1') ? '<a class="badge badge-success text-white" >Yes</a>' : '<a class="badge badge-danger text-white" >No</a>';
                $tempRow['actions'] = $operate;
            }
            $rows[] = $tempRow;
        }
        if ($from_api == true) {
            $bulkData['error'] = (empty($cat_search_res)) ? true : false;
            $bulkData['message'] = (empty($cat_search_res)) ? 'Add-on(s) does not exist' : 'Add-on(s) retrieved successfully';
            $bulkData['total'] = (empty($cat_search_res)) ? 0 : $total;
            $bulkData['data'] = (isset($rows) && !empty($rows)) ? $rows : [];
        } else {
            $bulkData['rows'] = $rows;
        }

        print_r(json_encode($bulkData));
    }

    public function get_top_foods()
    {

        $this->db->select('p.id, p.name, p.image, SUM(oi.quantity) as total_sales, SUM(oi.price * oi.quantity) as total_amount');
        $this->db->from('order_items oi');
        $this->db->join('product_variants pv', 'oi.product_variant_id = pv.id');
        $this->db->join('products p', 'pv.product_id = p.id');
        $this->db->join('orders o', 'oi.order_id = o.id'); // Join with orders table
        $this->db->where('oi.branch_id', $_SESSION['branch_id']);
        $this->db->where('o.active_status', 'delivered'); // Filter by active_status
        $this->db->group_by('oi.product_variant_id');
        $this->db->order_by('total_sales', 'DESC');
        $this->db->limit(3);
        $result = $this->db->get()->result_array();

        return $result;
    }
}

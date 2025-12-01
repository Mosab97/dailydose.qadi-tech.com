<?php
defined('BASEPATH') or exit('No direct script access allowed');


/*
---------------------------------------------------------------------------
Defined Methods:-
---------------------------------------------------------------------------
1. login
---------------------------------------------------------------------------
*/
class Api extends CI_Controller
{
    /**
     *   @var array $excluded_routes is an array of uri strings which we want to exclude from jwt verification.
     */
    protected $excluded_routes =
        [
            "admin/app/v1/api/index",
            "admin/app/v1/api",
            "admin/app/v1/api/login",
            "admin/app/v1/api/manage_tags",
            "admin/app/v1/api/delete_tag",
            "admin/app/v1/api/get_tags",

            "admin/app/v1/api/manage_category",
            "admin/app/v1/api/get_categories",
            "admin/app/v1/api/delete_category",
            "admin/app/v1/api/update_category_status",

            "admin/app/v1/api/get_products",
            "admin/app/v1/api/add_products",
            "admin/app/v1/api/update_products",
            "admin/app/v1/api/delete_product",
            
            "admin/app/v1/api/get_addons",

            "admin/app/v1/api/manage_tax",
            "admin/app/v1/api/get_taxes",
            "admin/app/v1/api/delete_tax",
            "admin/app/v1/api/update_tax_status",

            "admin/app/v1/api/get_transactions",
            "admin/app/v1/api/get_settings",
            "admin/app/v1/api/get_orders",

            "admin/app/v1/api/manage_branch",
            "admin/app/v1/api/get_branches",
            "admin/app/v1/api/delete_branch",

            "admin/app/v1/api/get_media",
            "admin/app/v1/api/upload_media",
            "admin/app/v1/api/delete_media",

            "admin/app/v1/api/get_attribute_values",
            "admin/app/v1/api/get_attributes",
            "admin/app/v1/api/add_attributes",
            "admin/app/v1/api/edit_attributes",
            "admin/app/v1/api/delete_attribute",


            "admin/app/v1/api/get_customers",

            "admin/app/v1/api/get_faqs",
            "admin/app/v1/api/manage_faqs",
            "admin/app/v1/api/delete_faq",

            "admin/app/v1/api/manage_rider",
            "admin/app/v1/api/get_riders",
            "admin/app/v1/api/delete_rider",
            "admin/app/v1/api/manage_rider_cash_collection",
            "admin/app/v1/api/get_rider_cash_collection",

            "admin/app/v1/api/get_sliders",
            "admin/app/v1/api/manage_slider",
            "admin/app/v1/api/delete_slider",
            
            "admin/app/v1/api/manage_offer",
            "admin/app/v1/api/delete_offer",
            "admin/app/v1/api/get_offer_images",

            "admin/app/v1/api/manage_promocode",
            "admin/app/v1/api/get_promo_codes",
            "admin/app/v1/api/delete_promocode",

            "admin/app/v1/api/get_sections",
            "admin/app/v1/api/delete_section",
            "admin/app/v1/api/manage_sections",

            "admin/app/v1/api/delete_order",
            "admin/app/v1/api/get_statistics",

            "admin/app/v1/api/manage_ticket_types",
            "admin/app/v1/api/delete_ticket_type",
            "admin/app/v1/api/get_ticket_types",
            "admin/app/v1/api/get_tickets",
            "admin/app/v1/api/send_message",
            "admin/app/v1/api/get_messages",


            "admin/app/v1/api/update_order_status",
            "admin/app/v1/api/get_cities",
            "admin/app/v1/api/update_customer_wallet",
            "admin/app/v1/api/update_fcm",
            "admin/app/v1/api/update_user_status",
        ];

    private $user_details = [];
    private $allowed_settings = ["general_settings", "terms_conditions", "privacy_policy", "about_us", 'payment_gateways_settings'];
    private $user_data = [
        'id',
        'username',
        'mobile',
        'email',
        'fcm_id',
        'image',
        'latitude',
        'longitude',
        'friends_code',
        'referral_code',
        'city',
        'serviceable_city',
        'country_code',
        'cash_received',
        'commission',
        'commission_method',
        'active',
        'no_of_ratings',
        'rating',
        'balance'
    ];

    public function __construct()
    {

        parent::__construct();
        header("Content-Type: application/json");
        header("Expires: 0");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $this->load->library(['upload', 'jwt', 'ion_auth', 'form_validation']);
        $this->load->model(['Order_model', 'Branch_model', 'category_model', 'transaction_model', 'Home_model', 'customer_model', 'ticket_model', 'Offer_model', 'faq_model', 'Slider_model', 'Rider_model', 'Area_model', 'Attribute_model', 'Product_model', 'media_model', 'Tag_model','Promo_code_model','Tax_model','Featured_section_model']);
        $this->load->helper([]);
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
        $response = $temp = $bulkdata = array();
        $this->identity_column = $this->config->item('identity', 'ion_auth');
        // initialize db tables data
        $this->tables = $this->config->item('tables', 'ion_auth');

        $current_uri = uri_string();
        if (!in_array($current_uri, $this->excluded_routes)) {
            $token = verify_app_request();
            if ($token['error']) {
                header('Content-Type: application/json');
                http_response_code($token['status']);
                print_r(json_encode($token));
                die();
            }
            $this->user_details = $token['data'];
        }
    }


    public function index()
    {
        $this->load->helper('file');
        $this->output->set_content_type(get_mime_by_extension(base_url('partner-api-doc.txt')));
        $this->output->set_output(file_get_contents(base_url('partner-api-doc.txt')));
    }


    public function login()
    {
        /* Parameters to be passed
            mobile: 9876543210
            password: 12345678
            fcm_id: FCM_ID //{ optional }
        */

        $identity_column = $this->config->item('identity', 'ion_auth');
        if ($identity_column == 'mobile') {
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim|numeric|required|xss_clean');
        } elseif ($identity_column == 'email') {
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');
        } else {
            $this->form_validation->set_rules('identity', 'Identity', 'trim|required|xss_clean');
        }
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
        $this->form_validation->set_rules('fcm_id', 'FCM ID', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        }

        $login = $this->ion_auth->login($this->input->post('mobile'), $this->input->post('password'), false);


        if ($login) {
            $data = fetch_details(['mobile' => $this->input->post('mobile', true)], 'users');
          
            if ($this->ion_auth->in_group('admin', $data[0]['id'])) {

                if (isset($_POST['fcm_id']) && !empty($_POST['fcm_id'])) {
                    update_details(['fcm_id' => $_POST['fcm_id']], ['mobile' => $_POST['mobile']], 'users');
                }

                /** generate token  */
                $token = generate_tokens($this->input->post('mobile'));
                update_details(['apikey' => $token], ['mobile' => $this->input->post('mobile')], "users");

                $data = fetch_details(['mobile' => $this->input->post('mobile', true)], 'users');
                
                unset($data[0]['password']);
                unset($data[0]['apikey']);



                if (empty($data[0]['image']) || !file_exists(FCPATH . USER_IMG_PATH . $data[0]['image']) == FALSE) {

                    $data[0]['image'] = base_url() . NO_PROFILE_IMAGE;
                } else {

                    $data[0]['image'] = base_url() . $data[0]['image'];
                }
                $data = array_map(function ($value) {
                    return $value === NULL ? "" : $value;
                }, $data[0]);

                //if the login is successful
                $response['error'] = false;
                $response['token'] = $token;
                $response['message'] = strip_tags($this->ion_auth->messages());
                $response['data'] = $data;
                echo json_encode($response);
                return false;
            } else {
                if (!is_exist(['mobile' => $_POST['mobile']], 'users')) {
                    $response['error'] = true;
                    $response['message'] = 'User does not exists !';
                    echo json_encode($response);
                    return false;
                }

                // if the login was un-successful
                $response['error'] = true;
                $response['message'] = strip_tags($this->ion_auth->errors());
                echo json_encode($response);
                return false;
            }
        } else {
            // if the login was un-successful
            $response['error'] = true;
            $response['message'] = strip_tags($this->ion_auth->errors());
            echo json_encode($response);
            return false;
        }
    }

    public function reset_password()
    {
        /* Parameters to be passed
            mobile_no:7894561235            
            new: pass@123
        */


        $this->form_validation->set_rules('mobile_no', 'Mobile No', 'trim|numeric|required|xss_clean|min_length[10]');
        $this->form_validation->set_rules('new', 'New Password', 'trim|required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        }

        $identity_column = $this->config->item('identity', 'ion_auth');
        $res = fetch_details(['mobile' => $_POST['mobile_no']], 'users');
        if (!empty($res) && $this->ion_auth->in_group('admin', $res[0]['id'])) {
            $identity = ($identity_column == 'email') ? $res[0]['email'] : $res[0]['mobile'];
            if (!$this->ion_auth->reset_password($identity, $_POST['new'])) {
                $response['error'] = true;
                $response['message'] = strip_tags($this->ion_auth->messages());
                echo json_encode($response);
                return false;
            } else {
                $response['error'] = false;
                $response['message'] = 'Reset Password Successfully';
                echo json_encode($response);
                return false;
            }
        } else {
            $response['error'] = false;
            $response['message'] = 'User does not exists !';
            echo json_encode($response);
            return false;
        }
    }

    public function manage_tags()
    {
        /*
        id: 1 {required only for update tags}
        title: tag_title {required}
         */

        $this->form_validation->set_rules('id', 'Tag Id', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                $old_title = fetch_details(['id' => $_POST['id']], 'tags');
                if($old_title[0]['title'] == $_POST['title']) {
                    
                }else{

                    if (is_exist(['title' => $_POST['title']], 'tags')) {
                        $this->response['error'] = true;
                        $this->response['message'] = 'Tag alredy exist !';
                        print_r(json_encode($this->response));
                        return false;
                    }
                }

                $this->db->set(['title' => $_POST['title']])->where('id', $_POST['id'])->update('tags');
                $tag_detail = fetch_details(['id' => $_POST['id']], 'tags');


                $this->response['error'] = false;
                $this->response['message'] = 'Tag Updated successfully';
                $this->response['data'] = $tag_detail;
                print_r(json_encode($this->response));
            } else {
                if (isset($_POST['title']) && !empty($_POST['title'])) {
                    if (is_exist(['title' => $_POST['title']], 'tags')) {
                        $this->response['error'] = true;
                        $this->response['message'] = 'Tag alredy exist !';
                        print_r(json_encode($this->response));
                        return false;
                    }
                    $data = [
                        'title' => $_POST['title'],

                    ];
                    $this->db->insert('tags', $data);
                    $tag_id = $this->db->insert_id();

                    $tag_detail = fetch_details(['id' => $tag_id], 'tags');

                    $this->response['error'] = false;
                    $this->response['message'] = 'Tag Adedd successfully';
                    $this->response['data'] = $tag_detail;
                    print_r(json_encode($this->response));
                }
            }
        }
    }

    public function delete_tag()
    {
        /*
       tag_id: 1 {required}
       
        */

        $this->form_validation->set_rules('tag_id', 'Tag Id', 'required|trim|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $tag_details = fetch_details(['id' => $_POST['tag_id']], 'tags');
            if (!empty($tag_details)) {
                if (delete_details(['id' => $_POST['tag_id']], 'tags')) {
                    $this->response['error'] = false;
                    $this->response['message'] = 'Tag Deleted successfully!';
                    print_r(json_encode($this->response));
                    return false;
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Something went wrong!';
                    print_r(json_encode($this->response));
                    return false;
                }
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Tag does not exist!';
                print_r(json_encode($this->response));
                return false;
            }
        }
    }
    public function get_tags()
    {


        /*
           limit:10 {optional}
           offset:0 {optional}
           search:tag_title {optional}
       
        */

        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('search', 'search', 'trim|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {

            $search = isset($_POST['search']) && !empty($_POST['search']) ? $_POST['search'] : "";
            $limit = isset($_POST['limit']) && !empty($_POST['limit']) ? $_POST['limit'] : 25;
            $offset = isset($_POST['offset']) && !empty($_POST['offset']) ? $_POST['offset'] : 0;

            $tags_res = $this->Tag_model->get_tags($search, $limit, $offset);

            $this->response['error'] = (empty($tags_res)) ? true : false;
            $this->response['total'] = !empty($tags_res['data']) ? count($tags_res['data']) : 0;
            $this->response['message'] = (empty($tags_res)) ? 'Tag does not exist' : 'Tags retrieved successfully';
            $this->response['data'] = $tags_res['data'];

            print_r(json_encode($this->response));
        }
    }

    public function manage_category()
    {

        /*
            id:10 {required when update category}
            name:category_name {required}
            branch_id:7,8 {required} { not required when update }
            image:text type(relative_path url - from get_media api)
         */

        $this->form_validation->set_rules('id', 'Category Id', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
        if (!isset($_POST['id'])) {
            $this->form_validation->set_rules('branch_id', 'Branch Id', 'trim|required|xss_clean');
        }
        $this->form_validation->set_rules('image', 'Category Image', 'trim|required|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            
            $data = [
                'category_input_name' => isset($_POST['name']) ? $_POST['name'] : "",
                'branch' => isset($_POST['branch_id']) ? explode(",", $_POST['branch_id']) : [],
                'category_input_image' => isset($_POST['image']) ? $_POST['image'] : "",
                'edit_category' => isset($_POST['id']) ? $_POST['id'] : "",
            ];
            $category = $this->category_model->add_category($data);
            
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                $data = fetch_details(['id' => $_POST['id']], 'categories');
                $data[0]['relative_path'] = $data[0]['image'];
                $data[0]['image'] = base_url($data[0]['image']);
                $branch_detail = fetch_details(['id' => $data[0]['branch_id']], 'branch');
                $data[0]['branch_details'] = $branch_detail[0];
            } else {
                $data = [];
                foreach ($category as $key => $value) {
                    $key1 =  fetch_details(['id' => $value], 'categories');
                    $key1[0]['relative_path'] = $key1[0]['image'];
                    $key1[0]['image'] = base_url($key1[0]['image']);
                    $branch_detail = fetch_details(['id' => $key1[0]['branch_id']], 'branch');
                    $key1[0]['branch_details'] = $branch_detail[0];
                    $data[] = $key1[0];
                }
            }
            
            $this->response['error'] = false;
            $this->response['message'] = (empty($_POST['id'])) ? 'Category added successfully' : 'Category updated successfully';
            $this->response['data'] = $data;
            print_r(json_encode($this->response));
        }
    }
    public function get_categories()
    {
        
        /*
            branch_id : 1
            id:15               // optional
            limit:25            // { default - 25 } optional
            offset:0            // { default - 0 } optional
            sort:               id / name // { default -row_id } optional
            order:DESC/ASC      // { default - ASC } optional
            has_child_or_item:false { default - true}  optional
            search:category_name {optional}
        */


        $this->form_validation->set_rules('branch_id', 'Branch Id', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('id', 'Category Id', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');
        $this->form_validation->set_rules('has_child_or_item', 'Child or Item', 'trim|xss_clean');
        $this->form_validation->set_rules('search', 'search', 'trim|xss_clean');


        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        }
        $limit = (isset($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
        $offset = (isset($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
        $sort = (isset($_POST['sort(array)']) && !empty(trim($_POST['sort']))) ? $this->input->post('sort', true) : 'row_order';
        $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $this->input->post('order', true) : 'ASC';
        $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : null;

        $has_child_or_item = (isset($_POST['has_child_or_item']) && !empty(trim($_POST['has_child_or_item']))) ? $this->input->post('has_child_or_item', true) : 'true';

        $id = (!empty($_POST['id']) && isset($_POST['id'])) ? $_POST['id'] : '';
        $cat_res = $this->category_model->get_categories($id, $limit, $offset, $sort, $order, strval(trim($has_child_or_item)),"","", $search);
        foreach ($cat_res as &$key) {
            $key['branch_details'] = fetch_details(['id' => $key['branch_id']], 'branch')[0];
        }
        $this->response['error'] = (empty($cat_res)) ? true : false;
        $this->response['total'] = !empty($cat_res) ? $cat_res[0]['total'] : 0;
        $this->response['message'] = (empty($cat_res)) ? 'Category does not exist' : 'Category retrieved successfully';
        $this->response['data'] = $cat_res;


        print_r(json_encode($this->response));
    }

    public function delete_category(){

        /** category_id : 1 {required} */

        $this->form_validation->set_rules('category_id', 'Category Id', 'trim|numeric|required|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        }
        if(is_exist(['id' => $_POST['category_id']],'categories')){

            if($this->category_model->delete_category($_POST['category_id']) == TRUE){
                    $this->response['error'] = false;
                    $this->response['message'] = "Category deleted successfully!";
                    $this->response['data'] = array();
            }else{
                    $this->response['error'] = true;
                    $this->response['message'] = "Something went wrong!";
                    $this->response['data'] = array();
            }
        }else{
                    $this->response['error'] = true;
                    $this->response['message'] = "This Category Does not exist!";
                    $this->response['data'] = array();
        }
         print_r(json_encode($this->response));
         return;

    }

    public function update_category_status(){
        /** 
         * category_id : 1 {required}
         * status : 0/1 {0: deactive, 1: active} {required}
         */

         $this->form_validation->set_rules('category_id', 'Category Id', 'trim|numeric|required|xss_clean');
         $this->form_validation->set_rules('status', 'Status', 'trim|numeric|required|xss_clean');
         if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        }

        if(update_details(['status' => $_POST['status']],['id' => $_POST['category_id']],'categories')){
                $this->response['error'] = false;
                $this->response['message'] = "Status updated successfully !";
                $this->response['data'] = array();
        }else{
                $this->response['error'] = true;
                $this->response['message'] = "something went wrong !";
                $this->response['data'] = array();
        }
            print_r(json_encode($this->response));
    }


    public function get_products()
    {

        // branch_id:1 
        // id:101              // optional
        // category_id:29      // optional
        // user_id:15          // optional
        // search:keyword      // optional   // search by product name and highlights
        // tags:multiword tag1, tag2, another tag      // optional {search by restro and product tags}
        // highlights:multiword tag1, tag2, another tag      // optional
        // attribute_value_ids : 34,23,12 // { Use only for filteration } optional
        // limit:25            // { default - 25 } optional
        // offset:0            // { default - 0 } optional
        // sort:p.id / p.date_added / pv.price
        //                     { default - p.id } optional
        // order:DESC/ASC      // { default - DESC } optional
        // top_rated_foods: 1 // { default - 0 } optional
        // discount: 5             // optional
        // min_price:10000          // optional
        // max_price:50000          // optional
        // product_ids: 19,20             // optional
        // product_variant_ids: 44,45,40             // optional
        // vegetarian:1|2|3             //{optional -> 1 - veg | 2 - non-veg | 3 - Both}
        // filter_by:p.id|sd.user_id       // {p.id = product list}
        //                      { default - sd.user_id } optional
        // latitude:123                 // {optional}
        // longitude:123                // {optional}
        // city_id:1                    // {optional}


        $this->form_validation->set_rules('branch_id', 'Branch ID', 'trim|required|numeric|xss_clean');
        $this->form_validation->set_rules('id', 'Product ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('vegetarian', 'vegetarian', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('search', 'Search', 'trim|xss_clean');
        $this->form_validation->set_rules('category_id', 'Category id', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('attribute_value_ids', 'Attr Ids', 'trim|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean|alpha');
        $this->form_validation->set_rules('top_rated_foods', ' Top Rated Foods ', 'trim|xss_clean|numeric');
        $this->form_validation->set_rules('min_price', ' Min Price ', 'trim|xss_clean|numeric|less_than_equal_to[' . $this->input->post('max_price') . ']');
        $this->form_validation->set_rules('max_price', ' Max Price ', 'trim|xss_clean|numeric|greater_than_equal_to[' . $this->input->post('min_price') . ']');
        $this->form_validation->set_rules('discount', ' Discount ', 'trim|xss_clean|numeric');
        $this->form_validation->set_rules('filter_by', ' filter_by ', 'trim|xss_clean');
        $this->form_validation->set_rules('latitude', 'latitude', 'trim|xss_clean');
        $this->form_validation->set_rules('longitude', 'longitude', 'trim|xss_clean');
        $this->form_validation->set_rules('city_id', 'city_id', 'trim|xss_clean');
        $this->form_validation->set_rules('slug', 'slug', 'trim|xss_clean');

        if (!$this->form_validation->run()) {

            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            if (isset($_POST['latitude']) && !empty($_POST['latitude']) && empty($_POST['longitude'])) {
                $this->response['error'] = true;
                $this->response['message'] = "The Longitude Field is Required";
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return;
            }
            if (isset($_POST['longitude']) && !empty($_POST['longitude']) && empty($_POST['latitude'])) {
                $this->response['error'] = true;
                $this->response['message'] = "The Latitude Field is Required";
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return;
            }
            $branch_id = (isset($_POST['branch_id']) && !empty($_POST['branch_id'])) ? $this->input->post("branch_id", true) : 0;
            $filters['longitude'] = (isset($_POST['longitude']) && !empty($_POST['longitude'])) ? $this->input->post("longitude", true) : 0;
            $filters['latitude'] = (isset($_POST['latitude']) && !empty($_POST['latitude'])) ? $this->input->post("latitude", true) : 0;
            $filters['city_id'] = (isset($_POST['city_id']) && !empty($_POST['city_id'])) ? $this->input->post("city_id", true) : 0;
            $filters['branch_id'] = (isset($_POST['branch_id']) && !empty($_POST['branch_id'])) ? $this->input->post("branch_id", true) : 0;
            $filters['category_slug'] = (isset($_POST['category_slug']) && !empty($_POST['category_slug'])) ? $this->input->post("category_slug", true) : "";
            $limit = (isset($_POST['limit'])) ? $this->input->post('limit', true) : 40;
            $offset = (isset($_POST['offset'])) ? $this->input->post('offset', true) : 0;
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'ASC';
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'p.row_order';
            $filters['search'] = (isset($_POST['search'])) ? $_POST['search'] : null;
            $filters['tags'] = (isset($_POST['tags'])) ? $_POST['tags'] : "";
            $filters['highlights'] = (isset($_POST['highlights'])) ? $_POST['highlights'] : "";
            $filters['attribute_value_ids'] = (isset($_POST['attribute_value_ids'])) ? $_POST['attribute_value_ids'] : null;
            $filters['is_similar_products'] = (isset($_POST['is_similar_products'])) ? $_POST['is_similar_products'] : null;
            $filters['vegetarian'] = (isset($_POST['vegetarian'])) ? $this->input->post("vegetarian", true) : null;
            $filters['discount'] = (isset($_POST['discount'])) ? $_POST['discount'] : 0;
            $filters['product_type'] = (isset($_POST['top_rated_foods']) && $_POST['top_rated_foods'] == 1) ? 'top_rated_foods_including_all_foods' : null;
            $filters['min_price'] = (isset($_POST['min_price']) && !empty($_POST['min_price'])) ? $this->input->post("min_price", true) : 0;
            $filters['max_price'] = (isset($_POST['max_price']) && !empty($_POST['max_price'])) ? $this->input->post("max_price", true) : 0;
            $filter_by = (isset($_POST['filter_by']) && !empty($_POST['filter_by'])) ? $this->input->post("filter_by", true) : 'sd.user_id';


            $category_id = (isset($_POST['category_id'])) ? $_POST['category_id'] : null;
            $product_id = (isset($_POST['id'])) ? $_POST['id'] : null;
            $product_ids = (isset($_POST['product_ids'])) ? $_POST['product_ids'] : null;
            $product_variant_ids = (isset($_POST['product_variant_ids']) && !empty($_POST['product_variant_ids'])) ? $this->input->post("product_variant_ids", true) : null;
            if ($product_ids != null) {
                $product_id = isset($product_ids) ? explode(",", $product_ids) : "";
            }
            if ($product_variant_ids != null) {
                $filters['product_variant_ids'] = isset($product_variant_ids) ? explode(",", $product_variant_ids) : "";
            }
            $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';

            $products = fetch_product($branch_id, $user_id, (isset($filters)) ? $filters : null, $product_id, $category_id, $limit, $offset, $sort, $order, null, null, $filter_by);

            $final_total = "0";
            if (isset($filters['discount']) && !empty($filters['discount'])) {

                $final_total = (isset($products['product'][0]['total']) && !empty($products['product'][0]['total'])) ? $products['product'][0]['total'] : "";
            } else {

                $final_total = (isset($products['total'])) ? strval($products['total']) : '';
            }

            if ($products['total'] > 0) {

                $this->response['error'] = false;
                $this->response['message'] = "Products retrieved successfully !";
                $this->response['min_price'] = (isset($products['min_price']) && !empty($products['min_price'])) ? strval($products['min_price']) : 0;
                $this->response['max_price'] = (isset($products['max_price']) && !empty($products['max_price'])) ? strval($products['max_price']) : 0;
                $this->response['search'] = (isset($_POST['search'])) ? $this->input->post("search", true) : "";
                $this->response['filters'] = (isset($products['filters']) && !empty($products['filters'])) ? $products['filters'] : [];
                $this->response['categories'] = (isset($products['categories']) && !empty($products['categories'])) ? $products['categories'] : [];
                $this->response['product_tags'] = (isset($products['product_tags']) && !empty($products['product_tags'])) ? $products['product_tags'] : [];
                $this->response['total'] = $final_total;
                $this->response['offset'] = (isset($_POST['offset']) && !empty($_POST['offset'])) ? $this->input->post("offset", true) : '0';
                $this->response['data'] = $products['product'];
            } else {
                $this->response['error'] = true;
                $this->response['message'] = "Products Not Found !";
                $this->response['data'] = array();
            }
        }
        print_r(json_encode($this->response));
    }

   public function add_products()
    {
        /*
            pro_input_name: product name
            branch_id:7
            product_category_id:99
            short_description: description
            product_add_ons:  [{"title":"add_on1","description":"descritpion","price":"40","calories":"123","status":1},{"title":"add_on2","description":"description2","price":"43","calories":"1234","status":1}]
            tags:1,2,3                               //{pass Tag Ids comma saprated}
            pro_input_tax:12
            cod_allowed:1                            //{ 1:allowed | 0:not-allowed }{default:1}
            available_time : 1                       //{ 1:need-time | 0:no-time needed }{default:0}
            product_start_time : 16:00:00
            product_end_time : 17:00:00
            is_cancelable:1                          //{optional}{1:cancelable | 0:not-cancelable}{default:0}
            cancelable_till:pending                  //{pending,confirmed,preparing,out_for_delivery}{required if "is_cancelable" is 1}
            pro_input_image:file  
            indicator:1                              //{ 0 - none | 1 - veg | 2 - non-veg }
            highlights:new,fresh                     //{optional}
            calories:123                             //{optional}
            total_allowed_quantity:100               //{optional}
            minimum_order_quantity:12
            attribute_values:1,2,3,4,5               //{comma saprated attributes values ids if set}
            --------------------------------------------------------------------------------
            till above same params
            --------------------------------------------------------------------------------
            --------------------------------------------------------------------------------
            common param for simple and variable product
            --------------------------------------------------------------------------------          
            product_type:simple_product | variable_product  
            variant_stock_level_type:product_level
            
            if(product_type == variable_product):
                variants_ids:3 5,4 5,1 2
                variant_price:100,200
                variant_special_price:90,190

                total_stock_variant_type:100     //{if (variant_stock_level_type == product_level)}
                variant_status:1                 //{if (variant_stock_level_type == product_level)}

            if(product_type == simple_product):
                simple_product_stock_status:null|0|1   {1=in stock | 0=out stock}
                simple_price:100
                simple_special_price:90
                product_total_stock:100             {optional}
                variant_stock_status: 0             {optional}//{0 =>'Simple_Product_Stock_Active' 1 => "Product_Level"	}
                variant_status:1
       */
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            $this->response['error'] = true;
            $this->response['message'] = DEMO_VERSION_MSG;
            echo json_encode($this->response);
            return false;
            exit();
        }
        if (!verify_tokens()) {
            return false;
        }

        $this->form_validation->set_rules('branch_id', 'Branch Id', 'trim|required|xss_clean');
        $this->form_validation->set_rules('pro_input_name', 'Product Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('indicator', 'Product Indicator', 'trim|required|xss_clean');
        $this->form_validation->set_rules('product_category_id', 'Product Category', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('short_description', 'Short Description', 'trim|required|xss_clean');
        $this->form_validation->set_rules('pro_input_image', 'Product Image', 'trim|xss_clean', array('required' => 'Image is required'));
        $this->form_validation->set_rules('tags', 'Food Tags', 'trim|xss_clean'); // tag ids->1,2,3
        $this->form_validation->set_rules('attribute_values', 'Attribute Values', 'trim|xss_clean'); // tag ids->1,2,3
        $this->form_validation->set_rules('product_type', 'Product type', 'trim|required|xss_clean');
        $this->form_validation->set_rules('total_allowed_quantity', 'Total Allowed Quantity', 'trim|xss_clean');
        $this->form_validation->set_rules('calories', 'calories', 'trim|xss_clean|numeric');
        $this->form_validation->set_rules('minimum_order_quantity', 'Minimum Order Quantity', 'trim|xss_clean');
        $this->form_validation->set_rules('product_type', 'Product Type', 'trim|required|xss_clean|in_list[simple_product,variable_product]');
        $this->form_validation->set_rules('variant_stock_level_type', 'Product Lavel', 'trim|xss_clean|in_list[product_level]');

        $_POST['variant_price'] = (isset($_POST['variant_price']) && !empty($_POST['variant_price'])) ?  explode(",", $this->input->post('variant_price', true)) : NULL;
        $_POST['variant_special_price'] = (isset($_POST['variant_special_price']) && !empty($_POST['variant_special_price'])) ?  explode(",", $this->input->post('variant_special_price', true)) : NULL;
        $_POST['variants_ids'] = (isset($_POST['variants_ids']) && !empty($_POST['variants_ids'])) ?  explode(",", $this->input->post('variants_ids', true)) : NULL;
        $_POST['variant_total_stock'] = (isset($_POST['variant_total_stock']) && !empty($_POST['variant_total_stock'])) ?  explode(",", $this->input->post('variant_total_stock', true)) : NULL;
        $_POST['variant_level_stock_status'] = (isset($_POST['variant_level_stock_status']) && !empty($_POST['variant_level_stock_status'])) ?  explode(",", $this->input->post('variant_level_stock_status', true)) : NULL;
        $_POST['branch_id'] = (isset($_POST['branch_id']) && !empty($_POST['branch_id'])) ?  $this->input->post('branch_id', true) : NULL;
        if (isset($_POST['is_cancelable']) && $_POST['is_cancelable'] == '1') {
            $this->form_validation->set_rules('cancelable_till', 'Till which status', 'trim|required|xss_clean|in_list[pending,confirmed,preparing,out_for_delivery]');
        }

        if (isset($_POST['cod_allowed'])) {
            $this->form_validation->set_rules('cod_allowed', 'COD allowed', 'trim|xss_clean');
        }
        if (isset($_POST['is_prices_inclusive_tax'])) {
            $this->form_validation->set_rules('is_prices_inclusive_tax', 'Tax included in prices', 'trim|xss_clean');
        }

        // If product type is simple			
        if (isset($_POST['product_type']) && $_POST['product_type'] == 'simple_product') {
            $this->form_validation->set_rules('simple_price', 'Price', 'trim|required|numeric|greater_than_equal_to[' . $this->input->post('simple_special_price') . ']|xss_clean');
            $this->form_validation->set_rules('simple_special_price', 'Special Price', 'trim|numeric|less_than_equal_to[' . $this->input->post('simple_price') . ']|xss_clean');

            if (isset($_POST['simple_product_stock_status']) && in_array($_POST['simple_product_stock_status'], array('0', '1'))) {
                $this->form_validation->set_rules('product_total_stock', 'Total Stock', 'trim|required|numeric|xss_clean');
                $this->form_validation->set_rules('simple_product_stock_status', 'Stock Status', 'trim|required|numeric|xss_clean');
            }
        } elseif (isset($_POST['product_type']) && $_POST['product_type'] == 'variable_product') { //If product type is variant	
            if (isset($_POST['variant_stock_status']) && $_POST['variant_stock_status'] == '0') {
                if ($_POST['variant_stock_level_type'] == "product_level") {
                    $this->form_validation->set_rules('total_stock_variant_type', 'Total Stock', 'trim|required|xss_clean');
                    $this->form_validation->set_rules('variant_stock_status', 'Stock Status', 'trim|required|xss_clean');
                    if (isset($_POST['variant_price']) && isset($_POST['variant_special_price'])) {
                        foreach ($_POST['variant_price'] as $key => $value) {
                            $this->form_validation->set_rules('variant_price[' . $key . ']', 'Price', 'trim|required|numeric|xss_clean|greater_than_equal_to[' . $this->input->post('variant_special_price[' . $key . ']') . ']');
                            $this->form_validation->set_rules('variant_special_price[' . $key . ']', 'Special Price', 'trim|numeric|xss_clean|less_than_equal_to[' . $this->input->post('variant_price[' . $key . ']') . ']');
                        }
                    } else {
                        $this->form_validation->set_rules('variant_price', 'Price', 'trim|required|numeric|xss_clean|greater_than_equal_to[' . $this->input->post('variant_special_price') . ']');
                        $this->form_validation->set_rules('variant_special_price', 'Special Price', 'trim|numeric|xss_clean|less_than_equal_to[' . $this->input->post('variant_price') . ']');
                    }
                }
            } else {
                if (isset($_POST['variant_price']) && isset($_POST['variant_special_price'])) {
                    foreach ($_POST['variant_price'] as $key => $value) {
                        $this->form_validation->set_rules('variant_price[' . $key . ']', 'Price', 'trim|required|numeric|xss_clean|greater_than_equal_to[' . $this->input->post('variant_special_price[' . $key . ']') . ']');
                        $this->form_validation->set_rules('variant_special_price[' . $key . ']', 'Special Price', 'trim|numeric|xss_clean|less_than_equal_to[' . $this->input->post('variant_price[' . $key . ']') . ']');
                    }
                } else {
                    $this->form_validation->set_rules('variant_price', 'Price', 'trim|required|numeric|xss_clean|greater_than_equal_to[' . $this->input->post('variant_special_price') . ']');
                    $this->form_validation->set_rules('variant_special_price', 'Special Price', 'trim|numeric|xss_clean|less_than_equal_to[' . $this->input->post('variant_price') . ']');
                }
            }
        }

    
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
        } else {
            if (isset($_POST['product_add_ons']) && $_POST['product_add_ons'] != '') {
                $_POST['product_add_ons'] = json_decode($_POST['product_add_ons'], 1);
            }
            if (isset($_POST['tags']) && $_POST['tags'] != '') {
                $_POST['tags'] = explode(",", $_POST['tags']);
            }
            $product_id = $this->Product_model->add_product($_POST);
            // print_r($product_id);
            $data = fetch_product(NULL, NULL, NULL, $product_id);
            // print_r($data);
            $this->response['error'] = false;
            $this->response['message'] = 'Product Added Successfully';
            $this->response['data'] = $data['product'];
            print_r(json_encode($this->response));
        }
    }

    public function update_product()
    {
        /*
            edit_product_id:74
            edit_variant_id:104,105
            variants_ids: new created with new attributes added
            pro_input_name: product name
            branch_id:7
            product_category_id:99
            short_description: description
            product_add_ons:  [{"title":"add_on1","description":"descritpion","price":"40","calories":"123","status":1},{"title":"add_on2","description":"description2","price":"43","calories":"1234","status":1}]
            tags:1,2,3                               //{pass Tag Ids comma saprated}
            is_prices_inclusive_tax:0                //{1: inclusive | 0: exclusive}
            cod_allowed:1                            //{ 1:allowed | 0:not-allowed }{default:1}
            available_time : 1                       //{ 1:need-time | 0:no-time needed }{default:0}
            product_start_time : 16:00:00
            product_end_time : 17:00:00
            is_cancelable:1                          //{optional}{1:cancelable | 0:not-cancelable}{default:0}
            cancelable_till:pending                  //{pending,confirmed,preparing,out_for_delivery}{required if "is_cancelable" is 1}
            pro_input_image:text type(relative_path url - from get_media api)  
            indicator:1                              //{ 0 - none | 1 - veg | 2 - non-veg }
            highlights:new,fresh                     //{optional}
            calories:123                             //{optional}
            total_allowed_quantity:100               //{optional}
            minimum_order_quantity:12
            attribute_values:1,2,3,4,5               //{comma saprated attributes values ids if set}
            --------------------------------------------------------------------------------
            till above same params
            --------------------------------------------------------------------------------
            --------------------------------------------------------------------------------
            common param for simple and variable product
            --------------------------------------------------------------------------------          
            product_type:simple_product | variable_product  
            variant_stock_level_type:product_level
            
            if(product_type == variable_product):
                variants_ids:3 5,4 5,1 2
                variant_price:100,200
                variant_special_price:90,190
                variant_images:files              //{optional}

                total_stock_variant_type:100     //{if (variant_stock_level_type == product_level)}
                variant_status:1                 //{if (variant_stock_level_type == product_level)}

            if(product_type == simple_product):
                simple_product_stock_status:null|0|1   {1=in stock | 0=out stock}
                simple_price:100
                simple_special_price:90
                product_total_stock:100             {optional}
                variant_stock_status: 0             {optional}//{0 =>'Simple_Product_Stock_Active' 1 => "Product_Level"	}
                variant_status:1
       */
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            $this->response['error'] = true;
            $this->response['message'] = DEMO_VERSION_MSG;
            echo json_encode($this->response);
            return false;
            exit();
        }
        if (!verify_tokens()) {
            return false;
        }

        $this->form_validation->set_rules('branch_id', 'Branch Id', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('edit_product_id', 'edit_product_id', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('indicator', 'Product Indicator', 'trim|required|xss_clean');
        $this->form_validation->set_rules('edit_variant_id', 'edit_variant_id', 'trim|xss_clean');
        $this->form_validation->set_rules('pro_input_name', 'Product Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('product_category_id', 'Product Category', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('short_description', 'Short Description', 'trim|required|xss_clean');
        $this->form_validation->set_rules('pro_input_image', 'Product Image', 'trim|xss_clean', array('required' => 'Image is required'));
        $this->form_validation->set_rules('tags', 'Food Tags', 'trim|xss_clean');
        $this->form_validation->set_rules('attribute_values', 'Attribute Values', 'trim|xss_clean');
        $this->form_validation->set_rules('product_type', 'Product type', 'trim|required|xss_clean');
        $this->form_validation->set_rules('total_allowed_quantity', 'Total Allowed Quantity', 'trim|xss_clean');
        $this->form_validation->set_rules('calories', 'calories', 'trim|xss_clean|numeric');
        $this->form_validation->set_rules('minimum_order_quantity', 'Minimum Order Quantity', 'trim|xss_clean');
        $this->form_validation->set_rules('product_type', 'Product Type', 'trim|required|xss_clean|in_list[simple_product,variable_product]');
        $this->form_validation->set_rules('variant_stock_level_type', 'Product Lavel', 'trim|required|xss_clean|in_list[product_level]');



        $_POST['variant_price'] = (isset($_POST['variant_price']) && !empty($_POST['variant_price'])) ?  explode(",", $this->input->post('variant_price', true)) : NULL;
        $_POST['variant_special_price'] = (isset($_POST['variant_special_price']) && !empty($_POST['variant_special_price'])) ?  explode(",", $this->input->post('variant_special_price', true)) : NULL;
        $_POST['variants_ids'] = (isset($_POST['variants_ids']) && !empty($_POST['variants_ids'])) ?  explode(",", $this->input->post('variants_ids', true)) : NULL;
        $_POST['variant_total_stock'] = (isset($_POST['variant_total_stock']) && !empty($_POST['variant_total_stock'])) ?  explode(",", $this->input->post('variant_total_stock', true)) : NULL;
        $_POST['variant_level_stock_status'] = (isset($_POST['variant_level_stock_status']) && !empty($_POST['variant_level_stock_status'])) ?  explode(",", $this->input->post('variant_level_stock_status', true)) : NULL;
        $_POST['edit_variant_id'] = (isset($_POST['edit_variant_id']) && !empty($_POST['edit_variant_id'])) ? explode(",", $this->input->post('edit_variant_id', true)) : [];

        if (isset($_POST['is_cancelable']) && $_POST['is_cancelable'] == '1') {
            $this->form_validation->set_rules('cancelable_till', 'Till which status', 'trim|required|xss_clean|in_list[pending,confirmed,preparing,out_for_delivery]');
        }
        if (isset($_POST['cod_allowed'])) {
            $this->form_validation->set_rules('cod_allowed', 'COD allowed', 'trim|xss_clean');
        }
        if (isset($_POST['is_prices_inclusive_tax'])) {
            $this->form_validation->set_rules('is_prices_inclusive_tax', 'Tax included in prices', 'trim|xss_clean');
        }

        // If product type is simple			
        if (isset($_POST['product_type']) && $_POST['product_type'] == 'simple_product') {
            $this->form_validation->set_rules('simple_price', 'Price', 'trim|required|numeric|greater_than_equal_to[' . $this->input->post('simple_special_price') . ']|xss_clean');
            $this->form_validation->set_rules('simple_special_price', 'Special Price', 'trim|numeric|less_than_equal_to[' . $this->input->post('simple_price') . ']|xss_clean');

            if (isset($_POST['simple_product_stock_status']) && in_array($_POST['simple_product_stock_status'], array('0', '1'))) {
                $this->form_validation->set_rules('product_total_stock', 'Total Stock', 'trim|required|numeric|xss_clean');
                $this->form_validation->set_rules('simple_product_stock_status', 'Stock Status', 'trim|required|numeric|xss_clean');
            }
        } elseif (isset($_POST['product_type']) && $_POST['product_type'] == 'variable_product') { //If product type is variant	
            if (isset($_POST['variant_stock_status']) && $_POST['variant_stock_status'] == '0') {
                if ($_POST['variant_stock_level_type'] == "product_level") {
                    $this->form_validation->set_rules('total_stock_variant_type', 'Total Stock', 'trim|required|xss_clean');
                    $this->form_validation->set_rules('variant_stock_status', 'Stock Status', 'trim|required|xss_clean');
                    if (isset($_POST['variant_price']) && isset($_POST['variant_special_price'])) {
                        foreach ($_POST['variant_price'] as $key => $value) {
                            $this->form_validation->set_rules('variant_price[' . $key . ']', 'Price', 'trim|required|numeric|xss_clean|greater_than_equal_to[' . $this->input->post('variant_special_price[' . $key . ']') . ']');
                            $this->form_validation->set_rules('variant_special_price[' . $key . ']', 'Special Price', 'trim|numeric|xss_clean|less_than_equal_to[' . $this->input->post('variant_price[' . $key . ']') . ']');
                        }
                    } else {
                        $this->form_validation->set_rules('variant_price', 'Price', 'trim|required|numeric|xss_clean|greater_than_equal_to[' . $this->input->post('variant_special_price') . ']');
                        $this->form_validation->set_rules('variant_special_price', 'Special Price', 'trim|numeric|xss_clean|less_than_equal_to[' . $this->input->post('variant_price') . ']');
                    }
                }
            } else {
                if (isset($_POST['variant_price']) && isset($_POST['variant_special_price'])) {
                    foreach ($_POST['variant_price'] as $key => $value) {
                        $this->form_validation->set_rules('variant_price[' . $key . ']', 'Price', 'trim|required|numeric|xss_clean|greater_than_equal_to[' . $this->input->post('variant_special_price[' . $key . ']') . ']');
                        $this->form_validation->set_rules('variant_special_price[' . $key . ']', 'Special Price', 'trim|numeric|xss_clean|less_than_equal_to[' . $this->input->post('variant_price[' . $key . ']') . ']');
                    }
                } else {
                    $this->form_validation->set_rules('variant_price', 'Price', 'trim|required|numeric|xss_clean|greater_than_equal_to[' . $this->input->post('variant_special_price') . ']');
                    $this->form_validation->set_rules('variant_special_price', 'Special Price', 'trim|numeric|xss_clean|less_than_equal_to[' . $this->input->post('variant_price') . ']');
                }
            }
        }

        $varient_eidt_id  = $_POST['edit_variant_id'];
        if (!$this->form_validation->run()) {
            $response['error'] = true;
            $response['message'] = strip_tags(validation_errors());
            $response['data'] = array();
            echo json_encode($response);
        } else {
            if (isset($_POST['tags']) && $_POST['tags'] != '') {
                $_POST['tags'] = explode(",", $_POST['tags']);
            }
          
            $_POST['edit_variant_id'] = $varient_eidt_id;
           
            $this->Product_model->add_product($_POST);
            $data = fetch_product(NULL, NULL, NULL, $_POST['edit_product_id']);
            $this->response['error'] = false;
            $this->response['message'] = 'Product Updated Successfully';
            $this->response['data'] = $data['product'];

            print_r(json_encode($this->response));
        }
    }


    public function delete_product(){
        /*
            product_id: 1001                // {required}
         
        */
        $this->form_validation->set_rules('product_id', 'Product ID', 'trim|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            $product_id = $this->input->post('product_id');
            // $this->Product_model->delete_product($product_id);
            
                if (delete_details(['product_id' => $product_id], 'product_variants')) {
                    delete_details(['id' => $product_id], 'products');
                    delete_details(['product_id' => $product_id], 'product_attributes');
                    delete_details(['product_id' => $product_id], 'product_tags');
                    delete_details(['product_id' => $product_id], 'product_rating');
                    delete_details(['product_id' => $product_id], 'product_add_ons');
                }
           
            $this->response['error'] = false;
            $this->response['message'] = 'Product Deleted Successfully';
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        }

    }


    public function get_transactions()
    {
        
        /*
            user_id:73              // { optional}
            id: 1001                // { optional}
            transaction_type:transaction / wallet // { default - transaction } optional
            type : COD / stripe / razorpay / paypal / paystack / flutterwave - for transaction | credit / debit - for wallet // { optional }
            search : Search keyword // { optional }
            limit:25                // { default - 25 } optional
            offset:0                // { default - 0 } optional
            sort: id / date_created // { default - id } optional
            order:DESC/ASC          // { default - DESC } optional
        */

        $this->form_validation->set_rules('user_id', 'User ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('transaction_type', 'Transaction Type', 'trim|xss_clean');
        $this->form_validation->set_rules('type', 'Type', 'trim|xss_clean');
        $this->form_validation->set_rules('search', 'Search keyword', 'trim|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            $user_id = (isset($_POST['user_id']) && is_numeric($_POST['user_id']) && !empty(trim($_POST['user_id']))) ? $this->input->post('user_id', true) : "";
            $id = (isset($_POST['id']) && is_numeric($_POST['id']) && !empty(trim($_POST['id']))) ? $this->input->post('id', true) : "";
            $transaction_type = (isset($_POST['transaction_type']) && !empty(trim($_POST['transaction_type']))) ? $this->input->post('transaction_type', true) : "transaction";
            $type = (isset($_POST['type']) && !empty(trim($_POST['type']))) ? $this->input->post('type', true) : "";
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'DESC';
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'id';
            $res = $this->transaction_model->get_transactions($id, $user_id, $transaction_type, $type, $search, $offset, $limit, $sort, $order);
            $this->response['error'] = !empty($res['data']) ? false : true;
            $this->response['message'] = !empty($res['data']) ? 'Transactions Retrieved Successfully' : 'Transactions does not exists';
            $this->response['total'] = !empty($res['data']) ? $res['total'] : 0;
            $this->response['data'] = !empty($res['data']) ? $res['data'] : [];
        }

        print_r(json_encode($this->response));
    }
    /**
     * API to get settings
     * 
     * @param string $type type of setting to be retrieved, either 'payment_method' or 'all' (default: all)
     * @param int $user_id user id (optional)
     * 
     * @return array response array containing settings
     */
    public function get_settings()
    {
        /*
            type : payment_method | all // { default : all  } optional
            user_id : 15 { optional }
        */
        $type = (isset($_POST['type']) && $_POST['type'] == 'payment_method') ? 'payment_method' : 'all';
        $this->form_validation->set_rules('type', 'Setting Type', 'trim|xss_clean');
        $this->form_validation->set_rules('user_id', 'User id', 'trim|numeric|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        } else {
            $tags = $city = $general_settings = array();
            $ALLOW_MODIFICATION = ALLOW_MODIFICATION;
            if ($type == 'all' || $type == 'payment_method') {

                $settings = [
                    'logo' => 0,
                    'privacy_policy' => 0,
                    'terms_conditions' => 0,
                    'fcm_server_key' => 0,
                    'contact_us' => 0,
                    'payment_method' => 1,
                    'about_us' => 0,
                    'currency' => 0,
                    'user_data' => 0,
                    'system_settings' => 1,
                    'web_settings' => 1,
                    'firebase_settings' => 1,
                ];

                if ($type == 'payment_method') {

                    $settings_res['payment_method'] = get_settings($type, $settings[$_POST['type']]);
                    unset($settings_res['payment_method']['phonepe_salt_key']);
                    unset($settings_res['payment_method']['phonepe_salt_index']);
                    unset($settings_res['payment_method']['phonepe_marchant_id']);
                    unset($settings_res['payment_method']['phonepe_appid']);

                    if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
                        $cart_total_response = get_cart_total($_POST['user_id'], false, 0);
                        $cod_allowed = isset($cart_total_response[0]['is_cod_allowed']) ? $cart_total_response[0]['is_cod_allowed'] : 1;
                        $settings_res['is_cod_allowed'] = $cod_allowed;
                    } else {
                        $settings_res['is_cod_allowed'] = 1;
                    }

                    $general_settings = $settings_res;
                } else {

                    foreach ($settings as $type => $isjson) {
                        if ($type == 'payment_method') {
                            continue;
                        }
                        $general_settings[$type] = [];
                        $settings_res = get_settings($type, $isjson);

                        if ($type == 'logo') {
                            $settings_res = base_url() . $settings_res;
                        }
                        if ($type == 'user_data') {
                            if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
                                $cart_total_response = get_cart_total($_POST['user_id'], false, 0);
                                $settings_res = fetch_users($_POST['user_id']);
                                $settings_res['cart_total_items'] = (isset($cart_total_response[0]['cart_count']) && $cart_total_response[0]['cart_count'] > 0) ? $cart_total_response[0]['cart_count'] : '0';
                                $settings_res = $settings_res;
                            } else {
                                $settings_res = "";
                            }
                        }
                        if ($type == 'system_settings') {
                            unset($settings_res['google_map_api_key']);
                            unset($settings_res['google_map_javascript_api_key']);
                            if(isset($settings_res['default_branch'])){
                                $branch_name = fetch_details(['id' => $settings_res['default_branch']],'branch','*');
                                if(isset($branch_name) && !empty($branch_name)){
                                    $settings_res['branch_name'] = $branch_name[0]['branch_name'];
                                    $settings_res['branch_image'] = base_url($branch_name[0]['image']);
                                }

                            }
                        }
                        array_push($general_settings[$type], $settings_res);
                    }
                }
                $general_settings['web_settings'][0]['logo'] = base_url() . $general_settings['web_settings'][0]['logo'];
                $general_settings['web_settings'][0]['favicon'] = base_url() . $general_settings['web_settings'][0]['favicon'];
                $general_settings['web_settings'][0]['light_logo'] = base_url() . $general_settings['web_settings'][0]['light_logo'];

                $this->response['error'] = false;
                $this->response['allow_modification'] = $ALLOW_MODIFICATION;
                $this->response['message'] = 'Settings retrieved successfully';
                $this->response['data'] = $general_settings;
                $this->response['data']['tags'] = $tags;
            } else {
                $this->response['error'] = true;
                $this->response['allow_modification'] = $ALLOW_MODIFICATION;
                $this->response['message'] = 'Settings Not Found';
                $this->response['data'] = array();
            }
            print_r(json_encode($this->response));
        }
    }

    public function get_orders()
    {
        // id:101              // {optional}
        // active_status: confirmed  {pending,confirmed,preparing,out_for_delivery,delivered,cancelled}     // optional
        // limit:25            // { default - 25 } optional
        // offset:0            // { default - 0 } optional
        // sort: o.id / date_added // { default - o.id } optional
        // order:DESC/ASC      // { default - DESC } optional
        // download_invoice:0 // { default - 0 } optional
        // branch_id : 7 
        // start_date : 2020-09-07 or 2020/09/07 { optional }
        // end_date : 2021-03-15 or 2021/03/15 { optional }

        $this->form_validation->set_rules('id', 'Id', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('branch_id', 'Branch Id', 'trim|required|numeric|xss_clean');
        $this->form_validation->set_rules('active_status', 'status', 'trim|xss_clean');
        $this->form_validation->set_rules('download_invoice', 'Invoice', 'trim|numeric|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        } else {
            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $this->input->post('sort', true) : 'o.id';
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $this->input->post('order', true) : 'DESC';
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : '';

            $this->form_validation->set_rules('user_id', 'User Id', 'trim|numeric|xss_clean');
            $this->form_validation->set_rules('active_status', 'status', 'trim|xss_clean');


            if (isset($_POST['active_status']) && !empty($_POST['active_status'])) {
                $where['active_status'] = $_POST['active_status'];
            }
            $id = (isset($_POST['id']) && !empty($_POST['id'])) ? $_POST['id'] : false;
            $user_id = (isset($_POST['user_id']) && !empty($_POST['user_id'])) ? $_POST['user_id'] : false;
            $start_date = (isset($_POST['start_date']) && !empty($_POST['start_date'])) ? $_POST['start_date'] : false;
            $end_date = (isset($_POST['end_date']) && !empty($_POST['end_date'])) ? $_POST['end_date'] : false;
            $multiple_status = (isset($_POST['active_status']) && !empty($_POST['active_status'])) ? explode(',', $_POST['active_status']) : false;
            $download_invoice = (isset($_POST['download_invoice']) && !empty($_POST['download_invoice'])) ? $_POST['download_invoice'] : 1;
            $city_id = (isset($_POST['city_id']) && !empty($_POST['city_id'])) ? $_POST['city_id'] : null;
            $area_id = (isset($_POST['area_id']) && !empty($_POST['area_id'])) ? $_POST['area_id'] : null;
            $branch_id = (isset($_POST['branch_id']) && $_POST['branch_id'] != null) ? $this->input->post('branch_id', true) : 0;

            $order_details = fetch_orders($id, $user_id, $multiple_status, '', $limit, $offset, $sort, $order, $download_invoice, '', $start_date, $end_date, $search, $city_id, $area_id, $branch_id, '', '', '');
            if (!empty($order_details['order_data'])) {
                $this->response['error'] = false;
                $this->response['message'] = 'Data retrieved successfully';
                $this->response['total'] = $order_details['total'];

                $this->response['pending'] = isset($_POST['branch_id']) ? orders_count("pending", $_POST['branch_id']) : "";
                $this->response['confirmed'] = isset($_POST['branch_id']) ? orders_count("confirmed", $_POST['branch_id']) : "";
                $this->response['preparing'] = isset($_POST['branch_id']) ? orders_count("preparing", $_POST['branch_id']) : "";
                $this->response['out_for_delivery'] = isset($_POST['branch_id']) ? orders_count("out_for_delivery", $_POST['branch_id']) : "";
                $this->response['delivered'] = isset($_POST['branch_id']) ? orders_count("delivered", $_POST['branch_id']) : "";
                $this->response['cancelled'] = isset($_POST['branch_id']) ? orders_count("cancelled", $_POST['branch_id']) : "";
                $this->response['data'] = $order_details['order_data'];
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Data Does Not Exists';
                $this->response['total'] = "0";
                $this->response['awaiting'] = "0";
                $this->response['received'] = "0";
                $this->response['processed'] = "0";
                $this->response['shipped'] = "0";
                $this->response['delivered'] = "0";
                $this->response['cancelled'] = "0";
                $this->response['returned'] = "0";
                $this->response['data'] = array();
            }
            print_r(json_encode($this->response));
        }
    }

    public function manage_branch(){
        /**
         * id : 1{required only while updating the branch}
         * latitude : 23.241653499709386
         * longitude : 69.66664668584443
         * branch_name : bhuj
         * description : branch description
         * address : branch adress
         * email : bhuj@gmail.com
         * contact : 9898989898
         * status : 0/1 {1: active , 0 :  deactive}
         * city_id : 1
         * set_default_branch : 0/1 
         * self_pickup : 0/1
         * deliver_orders : 0/1
         * image : text type(relative_path url - from get_media api)
         * working_time:[{"day":"Sunday","opening_time":"11:02:00","closing_time":"22:04:00","is_open":1},{"day":"Tuesday","opening_time":"19:20","closing_time":"18:21","is_open":1}]
         **/

        $this->form_validation->set_rules('id', 'Id', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('latitude', 'Latitude', 'trim|xss_clean');
        $this->form_validation->set_rules('longitude', 'Longitude', 'trim|xss_clean');
        $this->form_validation->set_rules('branch_name', 'Branch Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('description', 'Description', 'trim|required|xss_clean');
        $this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', 'Mail', 'trim|required|xss_clean');
        $this->form_validation->set_rules('contact', 'Contact', 'trim|required|xss_clean|min_length[5]');
        $this->form_validation->set_rules('status', 'Status', 'trim|xss_clean');
        $this->form_validation->set_rules('city_id', 'City Id', 'trim|xss_clean');
        $this->form_validation->set_rules('set_default_branch', 'Default Branch', 'trim|xss_clean');
        $this->form_validation->set_rules('self_pickup', 'Self Pickup', 'trim|xss_clean');
        $this->form_validation->set_rules('delivery_orders', 'Delivery Orders', 'trim|xss_clean');
        $this->form_validation->set_rules('working_time', 'Working Days', 'trim|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        } else {
            $work_time = [];
            if (isset($_POST['working_time']) && !empty($_POST['working_time'])) {
                $working_time = $this->input->post('working_time', true);
                $work_time = json_decode($working_time, true);
            }
            if (is_exist(['branch_name' => $_POST['branch_name']], 'branch')) {
                        $response["error"]   = true;
                        $response["message"] = "This Branch Already Exist.";
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
            }
            $branch_details = [
                'edit_branch' => isset($_POST['id']) ? $_POST['id'] : "",
                'latitude' => isset($_POST['latitude']) ? $_POST['latitude'] : "",
                'longitude' => isset($_POST['longitude']) ? $_POST['longitude'] : "",
                'branch_name' => isset($_POST['branch_name']) ? $_POST['branch_name'] : "",
                'description' => isset($_POST['description']) ? $_POST['description'] : "",
                'address' => isset($_POST['address']) ? $_POST['address'] : "",
                'email' => isset($_POST['email']) ? $_POST['email'] : "",
                'contact' => isset($_POST['contact']) ? $_POST['contact'] : "",
                'status' => isset($_POST['status']) ? $_POST['status'] : "",
                'city' => isset($_POST['city_id']) ? $_POST['city_id'] : "",
                'set_default_branch' => isset($_POST['set_default_branch']) && $_POST['set_default_branch'] == "1" ? "on" : "off",
                'self_pickup' => isset($_POST['self_pickup']) && $_POST['self_pickup'] == "1" ? "on": "off",
                'delivery_orders' => isset($_POST['delivery_orders']) && $_POST['delivery_orders'] == "1" ? "on": "off",
                'branch_image' => isset($_POST['image']) ? $_POST['image'] : "",
            ];

            $branch_data = $this->Branch_model->add_branch($branch_details, $working_time);
          

            if(isset($branch_data) && !empty($branch_data)){
            $branch = fetch_details(['id' => $branch_data],'branch');
            $working_time = fetch_details(["branch_id" => $branch_data], "branch_timings");
            foreach ($working_time as &$time) {
                unset($time['date_created']);
            }
            $branch[0]['branch_working_time'] = $working_time;
            $this->response['error'] = false;
            $this->response['message'] = !empty($_POST['id']) ? "Branch Updated successfully" : "Branch Added successfully";
            $this->response['data'] = $branch;
             }else{
            $this->response['error'] = true;
            $this->response['message'] = "Something went wrong";
            $this->response['data'] = array();
             }

        print_r(json_encode($this->response));
        }
    }

    public function delete_branch(){
        /** 
         * branch_id : 1 {required}
         */
        $this->form_validation->set_rules('branch_id', 'Branch Id', 'trim|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        }

        $branch_data = fetch_details(['id' => $_POST['branch_id']],'branch','*');
    
        if(empty($branch_data)){
            $this->response['error'] = true;
            $this->response['message'] = "Branch does not exist";
            $this->response['data'] = array();
        }else{

            if (delete_details(['id' => $_POST['branch_id']], 'branch') == TRUE) {
                if(delete_details(['branch_id' => $_POST['branch_id']], 'branch_timings') == TRUE){
                    $this->response['error'] = false;
                    $this->response['message'] = 'Branch Deleted Successfully';
                }
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Something Went Wrong';
            }

        }
         print_r(json_encode($this->response));

    }

    public function get_branches()
    {

        /*
            id:15               // optional
            limit:25            // { default - 25 } optional
            offset:0            // { default - 0 } optional
            sort:               id / name
                                // { default -row_id } optional
            order:DESC/ASC      // { default - ASC } optional
            search:value        // { optional }
        */

        $this->form_validation->set_rules('id', 'Id', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');
        $this->form_validation->set_rules('search', 'search', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        }
        $limit = $this->input->post('limit', true) ?? 25;
        $offset = $this->input->post('offset', true) ?? 0;
        $sort = $this->input->post('sort', true) ?? 'b.id';
        $order = $this->input->post('order', true) ?? 'ASC';
        $search = $this->input->post('search', true) ?? null;
        $city_id = $this->input->post('city_id', true);
        $latitude = $this->input->post('latitude', true);
        $longitude = $this->input->post('longitude', true);
        $id = $this->input->post('id', true) ?? '';

        $branch_res = $this->Branch_model->get_branches($search, $limit, $offset, $sort, $order, $id);
     
        foreach ($branch_res['data'] as &$branch) {
            $working_time = fetch_details(["branch_id" => $branch['branch_id']], "branch_timings");
            foreach ($working_time as &$time) {
                unset($time['date_created']);
            }
            $branch['branch_working_time'] = $working_time;
            $branch['range_wise_charges'] = isset($branch['range_wise_charges']) && !empty($branch['range_wise_charges']) ? json_decode($branch['range_wise_charges'], true) : [];
            $branch['boundary_points'] = json_decode($branch['boundary_points'], true);
        }

        $response['error'] = (empty($branch_res)) ? true : false;
        $response['total'] = $branch_res['total'];
        $response['message'] = (empty($branch_res['data'])) ? 'Branch does not exist' : 'Branch retrieved successfully';
        $response['data'] = $branch_res['data'];
        print_r(json_encode($response));
    }

    public function get_media()
    {
        /* 
        limit:25            // { default - 25 } optional
        offset:0            // { default - 0 } optional
        sort:               // { id } optional
        order:DESC/ASC      // { default - DESC } optional
        search:value        // {optional} 
        type:image          // {documents,spreadsheet,archive,video,audio,image}
        */


        $limit = isset($_POST['limit']) && is_numeric($_POST['limit']) ? $_POST['limit'] : 25;
        $offset = isset($_POST['offset']) && is_numeric($_POST['offset']) ? $_POST['offset'] : 0;
        $sort = isset($_POST['sort']) ? $_POST['sort'] : 'id';
        $order = isset($_POST['order']) ? $_POST['order'] : 'DESC';
        $search = isset($_POST['search']) ? $_POST['search'] : '';
        $type = isset($_POST['type']) ? $_POST['type'] : '';


        $this->form_validation->set_rules('user_id', 'User id', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            $this->media_model->get_media($limit, $offset, $sort, $order, $search, $type);
        }
    }


    public function upload_media()
    {

        /* 
            documents[]:FILES          
        */

        if (empty($_FILES['documents']['name'][0])) {
            $this->response['error'] = true;
            $this->response['message'] = "Upload at least one media file !";
            print_r(json_encode($this->response));
            return;
        }
        $year = date('Y');
        $target_path = FCPATH . MEDIA_PATH . $year . '/';
        $sub_directory = MEDIA_PATH . $year . '/';

        if (!file_exists($target_path)) {
            mkdir($target_path, 0777, true);
        }

        $temp_array = $media_ids = $other_images_new_name = array();
        $files = $_FILES;
        $other_image_info_error = "";
        $allowed_media_types = implode('|', allowed_media_types());
        $config['upload_path'] = $target_path;
        $config['allowed_types'] = $allowed_media_types;
        $other_image_cnt = count($_FILES['documents']['name']);
        $other_img = $this->upload;
        $other_img->initialize($config);
        $image_ids = array();
        for ($i = 0; $i < $other_image_cnt; $i++) {
            if (!empty($_FILES['documents']['name'][$i])) {
                $_FILES['temp_image']['name'] = $files['documents']['name'][$i];
                $_FILES['temp_image']['type'] = $files['documents']['type'][$i];
                $_FILES['temp_image']['tmp_name'] = $files['documents']['tmp_name'][$i];
                $_FILES['temp_image']['error'] = $files['documents']['error'][$i];
                $_FILES['temp_image']['size'] = $files['documents']['size'][$i];
                if (!$other_img->do_upload('temp_image')) {
                    $other_image_info_error = $other_image_info_error . ' ' . $other_img->display_errors();
                } else {
                    $temp_array = $other_img->data();
                    $temp_array['sub_directory'] = $sub_directory;
                    $media_ids[] = $media_id = $this->media_model->set_media($temp_array); 
                    /* set media in database */
                    resize_image($temp_array, $target_path, $media_id);
                    $other_images_new_name[$i] = $temp_array['file_name'];
                }
            } else {

                $_FILES['temp_image']['name'] = $files['documents']['name'][$i];
                $_FILES['temp_image']['type'] = $files['documents']['type'][$i];
                $_FILES['temp_image']['tmp_name'] = $files['documents']['tmp_name'][$i];
                $_FILES['temp_image']['error'] = $files['documents']['error'][$i];
                $_FILES['temp_image']['size'] = $files['documents']['size'][$i];
                if (!$other_img->do_upload('temp_image')) {
                    $other_image_info_error = $other_img->display_errors();
                }
            }
            $image_ids = $media_ids;
        }
        // Deleting Uploaded Images if any overall error occured
        if ($other_image_info_error != NULL) {
            if (isset($other_images_new_name) && !empty($other_images_new_name)) {
                foreach ($other_images_new_name as $key => $status) {
                    unlink($target_path . $other_images_new_name[$key]);
                }
            }
        }

        if (empty($_FILES) || $other_image_info_error != NULL) {
            $this->response['error'] = true;
            $this->response['message'] = (empty($_FILES)) ? "Files not Uploaded Successfully..!" : $other_image_info_error;
            $this->response['data'] = [];
            print_r(json_encode($this->response));
        } else {
            $data = array();
            foreach ($image_ids as $key) {
                $media = fetch_details(['id' => $key], 'media');
                foreach ($media as $row) {
                    $tempRow['id'] = $row['id'];
                    $tempRow['name'] = $row['name'];
                    if (file_exists(FCPATH . $row['sub_directory'] . $row['name'])) {
                        $row['image'] = get_image_url($row['sub_directory'] . $row['name'], 'thumb', 'sm', trim(strtolower($row['type'])));
                    } else {
                        $row['image'] = base_url() . NO_IMAGE;
                    }
                    $tempRow['image'] =  base_url() . $row['sub_directory'] . $row['name'];
                    $tempRow['relative_path'] =  $row['sub_directory'] . $row['name'];
                    $tempRow['extension'] = $row['extension'];
                    $tempRow['sub_directory'] = $row['sub_directory'];
                    $tempRow['size'] = ($row['size'] > 1) ? formatBytes($row['size']) : $row['size'];
                    $rows[] = $tempRow;
                    // $i++;
                }
                $data[] = $rows[0];
            }
            $this->response['error'] = false;
            $this->response['message'] = "Files Uploaded Successfully..!";
            $this->response['data'] = $data;
            print_r(json_encode($this->response));
        }
    }

    public function delete_media(){
        /** 
         * media_id : 1 {required}
         */
        $this->form_validation->set_rules('media_id', 'Media Id', 'trim|numeric|required|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        } else {
            $media_details = fetch_details(['id' => $_POST['media_id']] ,'media');
            if(empty($media_details)){         
            $this->response['error'] = true;
            $this->response['message'] = "Media does not exist";
            }else{            
           
            $id = $_POST['media_id'];
        /* check if id is not empty or invalid */
        if (!is_numeric($id) && $id == '') {
            $this->response['error'] = true;
            $this->response['message'] = "Something went wrong! Try again!";
            print_r(json_encode($this->response));
            return false;
        }
        $media = $this->media_model->get_media_by_id($id);
        /* check if media actually exists */
        if (empty($media)) {
            $this->response['error'] = true;
            $this->response['message'] = "Media does not exist!";
            print_r(json_encode($this->response));
            return false;
        }
        $path = FCPATH . $media[0]['sub_directory'] . $media[0]['name'];
        $where = array('id' => $id);

        if (delete_details($where, 'media')) {

            delete_images($media[0]['sub_directory'], $media[0]['name']);
            $this->response['error'] = false;
            $this->response['message'] = "Media deleted successfully!";
            print_r(json_encode($this->response));
            return false;
        } else {
            $this->response['error'] = true;
            $this->response['message'] = "Media could not be deleted!";
            print_r(json_encode($this->response));
            return false;
        }
            }
            print_r(json_encode($this->response));


        }

    }


    public function get_customers()
    {

        /*
            id: 1001                // { optional}
            search : Search keyword // { optional }
            limit:25                // { default - 25 } optional
            offset:0                // { default - 0 } optional
            sort: id/username/email/mobile/area_name/city_name/date_created // { default - id } optional
            order:DESC/ASC          // { default - DESC } optional
        */

        $this->form_validation->set_rules('id', 'ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('search', 'Search keyword', 'trim|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        } else {
            $id = (isset($_POST['id']) && is_numeric($_POST['id']) && !empty(trim($_POST['id']))) ? $this->input->post('id', true) : "";
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'DESC';
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'id';
            $this->customer_model->get_customers($id, $search, $offset, $limit, $sort, $order);
        }
    }
    public function get_faqs()
    {

        /*
                id:2    // {optional}
                search : Search keyword // { optional }
                limit:25                // { default - 10 } optional
                offset:0                // { default - 0 } optional
                sort: id                // { default - id } optional
                order:DESC/ASC          // { default - DESC } optional
        */

        $this->form_validation->set_rules('id', 'FAQs ID', 'trim|numeric');
        $this->form_validation->set_rules('search', 'Search keyword', 'trim');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric');
        $this->form_validation->set_rules('sort', 'sort', 'trim');
        $this->form_validation->set_rules('order', 'order', 'trim|in_list[DESC,ASC]');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        } else {
            $id = $this->input->post('id');
            $search = $this->input->post('search');
            $limit = $this->input->post('limit', true) ?? 10;
            $offset = $this->input->post('offset', true) ?? 0;
            $order = $this->input->post('order') ?? 'DESC';
            $sort = $this->input->post('sort') ?? 'id';

            $result = $this->faq_model->get_faq_list(
                $id,
                $search,
                $offset,
                $limit,
                $sort,
                $order
            );
        }

        print_r(json_encode($result));
    }

    public function manage_faqs(){

        /*
            id : 1 {required when update faq}
            question : question {required}
            answer : answer {required}
        */
        $this->form_validation->set_rules('id', 'ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('question', 'Question', 'trim|xss_clean|required');
        $this->form_validation->set_rules('answer', 'Answer', 'trim|xss_clean|required');
         if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        } else {
            if (isset($_POST['id'])) {
                    if (is_exist(['question' => $_POST['question'], 'status' => '1'], 'faqs', $_POST['id'])) {
                        $response["error"]   = true;
                        $response["message"] = "Question Already Exist !";
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }
                } else {
                    if (is_exist(['question' => $_POST['question'], 'status' => '1'], 'faqs')) {
                        $response["error"]   = true;
                        $response["message"] = "Question Already Exist !";
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }
                }

                $faq = [
                    'question' => $_POST['question'],
                    'answer' => $_POST['answer'],
                    'edit_faq' => isset($_POST['id']) ? $_POST['id'] : "",
                ];

                $faq_detail = $this->faq_model->add_faq($faq);
                $faq['id'] = strval($faq_detail);
                unset($faq['edit_faq']);
                if(!empty($faq_detail)){
                $response["error"]   = false;
                $response["message"] = isset($_POST['id']) ? "FAQ Updated Successfully !" : "FAQ Added Successfully !";
                $response["data"] = [$faq];
                echo json_encode($response);
                return false;
                }

        }


    }

     public function delete_faq()
    {
            /*
             faq_id: 1 {required}
            */

        $this->form_validation->set_rules('faq_id', 'Faq Id', 'required|trim|numeric|xss_clean');
        if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['message'] = strip_tags(validation_errors());
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return;
        } else {
        $faq_details = fetch_details(['id' => $_POST['faq_id']], 'faqs');
        if (!empty($faq_details)) {
            if (delete_details(['id' => $_POST['faq_id']], 'faqs')) {
                $this->response['error'] = false;
                $this->response['message'] = 'Faq Deleted successfully!';
                print_r(json_encode($this->response));
                return false;
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Something went wrong!';
                print_r(json_encode($this->response));
                return false;
            }
        } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Faq does not exist!';
                print_r(json_encode($this->response));
                return false;
        }
            }
    }

    public function get_riders()
    {

        /*
            branch_id : 7
            id: 1001                // { optional}
            search : Search keyword // { optional }
            limit:25                // { default - 25 } optional
            offset:0                // { default - 0 } optional
            sort: id/username/email/mobile/area_name/city_name/date_created // { default - id } optional
            order:DESC/ASC          // { default - DESC } optional
        */


        $this->form_validation->set_rules('branch_id', 'Branch ID', 'trim|required|numeric|xss_clean');
        $this->form_validation->set_rules('id', 'ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('search', 'Search keyword', 'trim|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        } else {
            $id = (isset($_POST['id']) && is_numeric($_POST['id']) && !empty(trim($_POST['id']))) ? $this->input->post('id', true) : "";
            $branch_id = (isset($_POST['branch_id']) && is_numeric($_POST['branch_id']) && !empty(trim($_POST['branch_id']))) ? $this->input->post('branch_id', true) : "";
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'DESC';
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'id';
            $this->Rider_model->get_riders($id, $search, $offset, $limit, $sort, $order, $branch_id);
        }
    }

    public function manage_rider(){

        /*  name:API rider4
            email:apirider7@gmail.com
            mobile:9696969647
            password:Apirider@1234
            confirm_password:Apirider@1234
            address:test address
            serviceable_city:1,2,58
            active:1
            commission_method:{required} (percentage_on_delivery_charges / fixed_commission_per_order)
            percentage:2 { only required when commission_method is "percentage_on_delivery_charges"}
            commission:2 { only required when commission_method is "fixed_commission_per_order"}
            rider_cancel_order:1
            branch_id:7
            rider_id:418 {only required while updating the rider}
        */
    
            $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('email', 'Mail', 'trim|required|xss_clean');
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|xss_clean|min_length[5]');
            $this->form_validation->set_rules('profile', 'Rider Profile', 'trim|xss_clean');
            if (!isset($_POST['rider_id'])) {
                $this->form_validation->set_rules('profile', 'Rider Profile', 'trim|xss_clean');
                $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
                $this->form_validation->set_rules('confirm_password', 'Confirm password', 'trim|required|matches[password]|xss_clean');
            }
            $this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean');
            $this->form_validation->set_rules('serviceable_city[]', 'Serviceable city', 'trim|required|xss_clean');
            $this->form_validation->set_rules('active', 'Status', 'trim|xss_clean');
            $this->form_validation->set_rules('commission_method', 'Commission Method', 'trim|required|xss_clean');
            if (isset($_POST['commission_method']) && !empty($_POST['commission_method']) && $_POST['commission_method'] == "percentage_on_delivery_charges") {
                $this->form_validation->set_rules('percentage', 'Percentage', 'trim|xss_clean|required');
            }
            if (isset($_POST['commission_method']) && !empty($_POST['commission_method']) && $_POST['commission_method'] == "fixed_commission_per_order") {
                $this->form_validation->set_rules('commission', 'Commission', 'trim|xss_clean|required');
            }
            if (!$this->form_validation->run()) {

                $this->response['error'] = true;
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                // if(isset($_POST['rider_id'])){

                    $_POST['edit_rider'] = $_POST['rider_id'];
                // }
               $_POST['serviceable_city'] = array($_POST['serviceable_city']);
               $serviceable_city = isset($_POST['serviceable_city']) && !empty($_POST['serviceable_city']) ? implode("," , $_POST['serviceable_city']) : "";
              
                if (isset($_POST['commission']) && !empty($_POST['commission'])) {
                } else {

                    if (isset($_POST['percentage']) && !empty($_POST['percentage'])) {

                        if ($_POST['percentage'] <= 0 || $_POST['percentage'] > 100) {
                            $response["error"]   = true;
                            $response["message"] = "Percentage on Delivery Charges is not valid";
                            $response["data"] = array();
                            echo json_encode($response);
                            return false;
                        }
                    }
                }

                $temp_array_logo = $profile_doc = array();
                $logo_files = $_FILES;
                $profile_error = "";
                $config = [
                    'upload_path' =>  FCPATH . USER_IMG_PATH,
                    'allowed_types' => 'jpg|png|jpeg|gif',
                    'max_size' => 8000,
                ];
                if (isset($logo_files['profile']) && !empty($logo_files['profile']['name']) && isset($logo_files['profile']['name'])) {
                    $other_img = $this->upload;
                   
                    $other_img->initialize($config);

                    if (isset($_POST['edit_rider']) && !empty($_POST['edit_rider']) && isset($_POST['profile']) && !empty($_POST['profile'])) {
                        $old_logo = explode('/', $this->input->post('profile', true));
                        delete_images(USER_IMG_PATH, $old_logo[2]);
                    }

                    if (!empty($logo_files['profile']['name'])) {

                        $_FILES['temp_image']['name'] = $logo_files['profile']['name'];
                        $_FILES['temp_image']['type'] = $logo_files['profile']['type'];
                        $_FILES['temp_image']['tmp_name'] = $logo_files['profile']['tmp_name'];
                        $_FILES['temp_image']['error'] = $logo_files['profile']['error'];
                        $_FILES['temp_image']['size'] = $logo_files['profile']['size'];
                        if (!$other_img->do_upload('temp_image')) {
                            $profile_error = 'Images :' . $profile_error . ' ' . $other_img->display_errors();
                        } else {
                            $temp_array_logo = $other_img->data();
                            resize_review_images($temp_array_logo, FCPATH . USER_IMG_PATH);
                            $profile_doc  = USER_IMG_PATH . $temp_array_logo['file_name'];
                        }
                    } else {
                        $_FILES['temp_image']['name'] = $logo_files['profile']['name'];
                        $_FILES['temp_image']['type'] = $logo_files['profile']['type'];
                        $_FILES['temp_image']['tmp_name'] = $logo_files['profile']['tmp_name'];
                        $_FILES['temp_image']['error'] = $logo_files['profile']['error'];
                        $_FILES['temp_image']['size'] = $logo_files['profile']['size'];
                        if (!$other_img->do_upload('temp_image')) {
                            $profile_error = $other_img->display_errors();
                        }
                    }
                    //Deleting Uploaded Images if any overall error occured
                }

                if ($profile_error != NULL) {
                    $this->response['error'] = true;
                    $this->response['message'] =  $profile_error;
                    print_r(json_encode($this->response));
                    return;
                }
                /* process commission params */
                $commission_method = $this->input->post("commission_method", true);
                $commission = 0;
                if (isset($commission_method) && !empty($commission_method) && $commission_method == "percentage_on_delivery_charges") {
                    $commission = $this->input->post("percentage");
                }
                if (isset($commission_method) && !empty($commission_method) && $commission_method == "fixed_commission_per_order") {
                    $commission = $this->input->post("commission");
                }

                $_POST['commission'] = $commission;
                $_POST['percentage'] = $this->input->post("percentage", true);

                if (isset($_POST['edit_rider'])) {
                   
                    if ($_POST['commission_method'] == 'percentage_on_delivery_charges') {
                        if (isset($_POST['percentage']) && !empty($_POST['percentage'])) {

                            if ($_POST['percentage'] <= 0 || $_POST['percentage'] > 100) {
                                $response["error"]   = true;
                                $response["message"] = "Percentage on Delivery Charges is not valid";
                                $response["data"] = array();
                                echo json_encode($response);
                                return false;
                            }
                        }
                    }
                   
                    
                    
                    $_POST['active'] = $this->input->post("active", true);
                    $_POST['rider_cancel_order'] = isset($_POST['rider_cancel_order']) && $_POST['rider_cancel_order'] == 'on' ? 1 : 0;
                    $image = USER_IMG_PATH . $_FILES['profile']['name'];
                    $riders_data = fetch_details(['id' => $_POST['rider_id']],'users','*');
                    $this->Rider_model->update_rider($_POST, $image);
                    
                } else {
                   
                    if (!$this->form_validation->is_unique($_POST['mobile'], 'users.mobile') || !$this->form_validation->is_unique($_POST['email'], 'users.email')) {
                        $response["error"]   = true;
                        $response["message"] = "Email or mobile already exists !";
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }

                    $identity_column = $this->config->item('identity', 'ion_auth');
                    $email = strtolower($this->input->post('email'));
                    $mobile = $this->input->post('mobile');
                    $identity = ($identity_column == 'mobile') ? $mobile : $email;
                    $password = $this->input->post('password');
                    $branch_id = isset($_POST['branch_id']) ? $_POST['branch_id'] : "";

                    if (validatePassword($password)) {
                        $additional_data = [
                            'username' => $this->input->post('name'),
                            'address' => $this->input->post('address'),
                            'serviceable_city' => $serviceable_city,
                            'commission_method' => $commission_method,
                            'commission' => $commission,
                            'branch_id' => $branch_id,
                            'image' => (!empty($profile_doc)) ? $profile_doc : $this->input->post('profile', true),
                            'rider_cancel_order' => isset($_POST['rider_cancel_order']) && $_POST['rider_cancel_order'] == 'on' ? 1 : 0,
                        ];
                        $ridresss = $this->ion_auth->register($identity, $password, $email, $additional_data, ['3']);
                        $riders_data = fetch_details(['id' => $ridresss],'users','*');
                        update_details(['active' => 1], [$identity_column => $identity], 'users');
                    } else {
                        $response["error"]   = true;
                        $response["message"] = "Password Should be atleast 8 character, one upparcase letter, one lowercase letter and one number!";
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }
                }

                $this->response['error'] = false;
                $this->response['message'] = isset($_POST['rider_id']) ? "Rider updated sucessfully" : "Rider added sucessfully";
                $this->response['data'] = $riders_data;
                print_r(json_encode($this->response));
            }
       

    }

    public function delete_rider()
    {
        /* 
            rider_id:2
        */        
            if (delete_details(['id' => $_POST['rider_id']], 'users') == TRUE) {
                if (delete_details(['user_id' => $_POST['rider_id']], 'users_groups') == TRUE) {
                    $this->response['error'] = false;
                    $this->response['message'] = 'Rider Deleted successfully';
                    print_r(json_encode($this->response));
                }
            }
            else {
                $this->response['error'] = true;
                $this->response['message'] = 'Something Went Wrong';
                print_r(json_encode($this->response));
            }
       
    }

    public function delete_order()
    {

        /*
            order_id:1
        */

        $this->form_validation->set_rules('order_id', 'Order ID', 'trim|required|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';
            if (!is_exist(["id" => $order_id], 'orders')) {
                $this->response['error'] = true;
                $this->response['message'] = 'Order ID does not exist';
                $this->response['data'] = array();
            } else {
                delete_details(['id' => $order_id], 'orders');
                delete_details(['order_id' => $order_id], 'order_items');
                $this->response['error'] = false;
                $this->response['message'] = 'Order deleted successfully';
                $this->response['data'] = array();
            }
        }
        print_r(json_encode($this->response));
    }

    public function get_statistics()
    {
        $this->form_validation->set_rules('branch_id', 'Branch ID', 'trim|required|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        } else {
            $branch_id = (isset($_POST['branch_id']) && is_numeric($_POST['branch_id']) && !empty(trim($_POST['branch_id']))) ? $this->input->post('branch_id', true) : "";
            $overall_sale = $this->db
                ->select("SUM(final_total) as overall_sale")
                ->where('branch_id', $branch_id)
                ->get('orders')
                ->row_array();

            $overall_sale = !empty($overall_sale['overall_sale']) ? intval($overall_sale['overall_sale']) : 0;
            $earning_row['overall_sale'] = $overall_sale;


            $day_res = $this->db->select("DAY(date_added) as date, SUM(final_total) as total_sale")
                ->where('date_added >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)')->where('branch_id', $branch_id)
                ->group_by('day(date_added)')->get('`orders`')->result_array();
            $day_wise_sales['total_sale'] = array_map('intval', array_column($day_res, 'total_sale'));
            $day_wise_sales['day'] = array_column($day_res, 'date');
            $earning_row['daily_earnings'] = $day_wise_sales;


            $day = strtotime("today");
            $start_week = strtotime("last sunday midnight", $day);
            $end_week = strtotime("next saturday", $day);
            $start = date("Y-m-d", $start_week);
            $end = date("Y-m-d", $end_week);
            $week_res = $this->db->select("DATE_FORMAT(date_added, '%d-%b') as date, SUM(final_total) as total_sale")
                ->where("date(date_added) >='$start' and date(date_added) <= '$end' ")->where('branch_id', $branch_id)
                ->group_by('day(date_added)')->get('`orders`')->result_array();

            $week_wise_sales['total_sale'] = array_map('intval', array_column($week_res, 'total_sale'));
            $week_wise_sales['week'] = array_column($week_res, 'date');
            $earning_row['weekly_earnings'] = $week_wise_sales;

            $month_res = $this->db->select('SUM(final_total) AS total_sale,DATE_FORMAT(date_added,"%b") AS month_name ')
                ->where('branch_id', $branch_id)
                ->group_by('year(CURDATE()),MONTH(date_added)')
                ->order_by('year(CURDATE()),MONTH(date_added)')
                ->get('`orders`')->result_array();

            $month_wise_sales['total_sale'] = array_map('intval', array_column($month_res, 'total_sale'));
            $month_wise_sales['month_name'] = array_column($month_res, 'month_name');
            $earning_row['monthly_earnings'] = $month_wise_sales;
            $earning_rows[] = $earning_row;

            $count_products_low_status = $this->Home_model->count_products_stock_low_status($branch_id);
            $count_products_sold_out_status = $this->Home_model->count_products_availability_status($branch_id);
            $counter_row['order_counter'] = $this->Home_model->count_new_orders($branch_id, '1');
            $counter_row['delivered_orders_counter'] = $this->Home_model->count_orders_by_status('delivered', $branch_id);
            $counter_row['cancelled_orders_counter'] = $this->Home_model->count_orders_by_status('cancelled', $branch_id);
            $counter_row['awaiting_orders_counter'] = $this->Home_model->count_orders_by_status('awaiting', $branch_id);
            $counter_row['pending_orders_counter'] = $this->Home_model->count_orders_by_status('pending', $branch_id);
            $counter_row['preparing_orders_counter'] = $this->Home_model->count_orders_by_status('preparing', $branch_id);
            $counter_row['ready_for_pickup_orders_counter'] = $this->Home_model->count_orders_by_status('ready_for_pickup', $branch_id);
            $counter_row['out_for_delivery_orders_counter'] = $this->Home_model->count_orders_by_status('out_for_delivery', $branch_id);
            $counter_row['confirmed_orders_counter'] = $this->Home_model->count_orders_by_status('confirmed', $branch_id);
            $counter_row['draft_orders_counter'] = $this->Home_model->count_orders_by_status('draft', $branch_id);
           
            $counter_row['user_counter'] = $this->Home_model->count_new_users();
            $counter_row['rider_counter'] = $this->Home_model->count_riders($branch_id);
            $counter_row['product_counter'] = $this->Home_model->count_products($branch_id);
            $counter_row['branch_counter'] = $this->Home_model->count_branch();
            $counter_row['count_products_low_status'] = "$count_products_low_status";
            $counter_row['count_products_sold_out_status'] = "$count_products_sold_out_status";
            $counter_rows[] = $counter_row;
            $bulkData['error'] = false;
            $bulkData['message'] = 'Data retrived Successfully';
            $bulkData['earnings'] = $earning_rows;
            $bulkData['counts'] = $counter_rows;
            print_r(json_encode($bulkData));

        }
    }

    public function manage_ticket_types(){

        /*
            title : ticket type title
            id : 2{only for update}
        */

        $this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');
        $this->form_validation->set_rules('id', 'Id', 'trim|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        } else {
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                // print_r("hello");
                $ticket_type = fetch_details(['id' => $_POST['id']], 'ticket_types');
                if(empty($ticket_type)) {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Ticket type not found';
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }
                $id = $_POST['id'];
                $data = [
                    'title' => $_POST['title']
                ];
                $this->db->where('id', $id);
                $this->db->update('ticket_types', $data);
                $data = fetch_details(['id' => $id], 'ticket_types', '*');

                $this->response['error'] = false;
                $this->response['message'] = 'Ticket type updated successfully';
                $this->response['data'] = $data;
            } else {
                $data = [
                    'title' => $_POST['title']
                ];
                $this->db->insert('ticket_types', $data);
                $last_id = $this->db->insert_id();
                $data = fetch_details(['id' => $last_id], 'ticket_types','*');
                $this->response['error'] = false;
                $this->response['message'] = 'Ticket type added successfully';
                $this->response['data'] = $data;
            }
            print_r(json_encode($this->response));
        }
    }

    public function get_ticket_types()
    {
        $this->db->select('*');
        $types = $this->db->get('ticket_types')->result_array();

        if (!empty($types)) {
            foreach ($types as &$type) {
                unset($type['date_created']);
                $type = output_escaping($type);
            }
            unset($type); // unset the reference
        }

        $this->response['error'] = false;
        $this->response['message'] = 'Ticket types fetched successfully';
        $this->response['data'] = $types;
        print_r(json_encode($this->response));
    }

    public function delete_ticket_type(){

        /*
            id : 2 {required}
        */

        $this->form_validation->set_rules('id', 'Id', 'trim|numeric|required|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        } else {
            $id = $_POST['id'];
            $ticket_type = fetch_details(['id' => $id], 'ticket_types');
            if (empty($ticket_type)) {
                $this->response['error'] = true;
                $this->response['message'] = 'Ticket type not found';
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
            $this->db->where('id', $id);
            $this->db->delete('ticket_types');
            $this->response['error'] = false;
            $this->response['message'] = 'Ticket type deleted successfully';
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        }
    }


    public function get_tickets()
    {

        /*
       
            ticket_id: 1001                // { optional}
            ticket_type_id: 1001                // { optional}
            user_id: 1001                // { optional}
            status:   [1 -> pending, 2 -> opened, 3 -> resolved, 4 -> closed, 5 -> reopened]// { optional}
            search : Search keyword // { optional }
            limit:25                // { default - 25 } optional
            offset:0                // { default - 0 } optional
            sort: id | date_created | last_updated                // { default - id } optional
            order:DESC/ASC          // { default - DESC } optional
        */

        $this->form_validation->set_rules('ticket_id', 'Ticket ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('ticket_type_id', 'Ticket Type ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('user_id', 'User ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('status', 'User ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('search', 'Search keyword', 'trim|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        } else {
            $ticket_id = $_POST['ticket_id'] ?? $this->input->post('ticket_id', true) ?? "";
            $ticket_type_id = $_POST['ticket_type_id'] ?? $this->input->post('ticket_type_id', true) ?? "";
            $user_id = $_POST['user_id'] ?? $this->input->post('user_id', true) ?? "";
            $status = $_POST['status'] ?? $this->input->post('status', true) ?? "";
            $search = $_POST['search'] ?? $this->input->post('search', true) ?? "";
            $limit = $_POST['limit'] ?? $this->input->post('limit', true) ?? 10;
            $offset = $_POST['offset'] ?? $this->input->post('offset', true) ?? 0;
            $order = $_POST['order'] ?? $this->input->post('order', true) ?? 'DESC';
            $sort = $_POST['sort'] ?? $this->input->post('sort', true) ?? 'id';
            $result = $this->ticket_model->get_tickets($ticket_id, $ticket_type_id, $user_id, $status, $search, $offset, $limit, $sort, $order);
            print_r(json_encode($result));
        }
    }


    public function edit_ticket()
    {

        /*
            ticket_id:1
            status:1 or 2 or 3 or 4 or 5  [1 -> pending, 2 -> opened, 3 -> resolved, 4 -> closed, 5 -> reopened]
        */

        $this->form_validation->set_rules('ticket_id', 'Ticket Id', 'trim|required|xss_clean');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean');


        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            $status = $this->input->post('status', true);
            $ticket_id = $this->input->post('ticket_id', true);
            $res = fetch_details('tickets', 'id=' . $ticket_id, '*');
            if (empty($res)) {
                $this->response['error'] = true;
                $this->response['message'] = "User id is changed you can not udpate the ticket.";
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
            if ($status == PENDING && $res[0]['status'] == OPENED) {
                $this->response['error'] = true;
                $this->response['message'] = "Current status is opened.";
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
            if ($status == OPENED && ($res[0]['status'] == RESOLVED || $res[0]['status'] == CLOSED)) {
                $this->response['error'] = true;
                $this->response['message'] = "Can't be OPEN but you can REOPEN the ticket.";
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
            if ($status == RESOLVED && $res[0]['status'] == CLOSED) {
                $this->response['error'] = true;
                $this->response['message'] = "Current status is closed.";
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
            if ($status == REOPEN && ($res[0]['status'] == PENDING || $res[0]['status'] == OPENED)) {
                $this->response['error'] = true;
                $this->response['message'] = "Current status is pending or opened.";
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }

            $data = array(
                'status' => $status,
                'edit_ticket_status' => $ticket_id
            );

            $system_settings = get_settings('system_settings', true);
            if (!$this->ticket_model->add_ticket($data)) {
                $result = $this->ticket_model->get_tickets($ticket_id);
                if (!empty($result)) {
                    /* Send custom notification message */
                    $ticket_res = fetch_details('ticket_messages', ['user_type' => 'user', 'ticket_id' => $ticket_id], 'user_id');

                    $user_res = fetch_details("users", ['id' => $ticket_res[0]['user_id']], 'fcm_id', '', '', '', '');
                    $fcm_ids[0][] = $user_res[0]['fcm_id'];

                    $custom_notification = fetch_details('custom_notifications', ['type' => "ticket_status"], '');

                    $hashtag_application_name = '< application_name >';

                    $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                    $hashtag = html_entity_decode($string);

                    $data = str_replace($hashtag_application_name, $system_settings['app_name'], $hashtag);
                    $message = output_escaping(trim($data, '"'));

                    $fcm_admin_subject = (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Your Ticket status has been changed";
                    $fcm_admin_msg = (!empty($custom_notification)) ? $message : "Ticket Message";

                    if (!empty($fcm_ids)) {
                        $fcmMsg = array(
                            'title' => $fcm_admin_subject,
                            'body' => $fcm_admin_msg,
                            'type' => "ticket_status",
                            'type_id' => $ticket_id
                        );
                        send_notification($fcmMsg, $fcm_ids,$fcmMsg,$fcm_admin_subject,$fcm_admin_msg,"ticket_status");
                    }
                }
                $this->response['error'] = false;
                $this->response['message'] = 'Ticket updated Successfully';
                $this->response['data'] = $result['data'];
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Ticket Not Added';
                $this->response['data'] = (!empty($this->response['data'])) ? $this->response['data'] : [];
            }
        }
        print_r(json_encode($this->response));
    }

    public function get_messages()
    {

        /*
        ticket_id: 1001            
        user_type: 1001             // { optional}
        user_id: 1001                // { optional}
        search : Search keyword // { optional }
        limit:25                // { default - 25 } optional
        offset:0                // { default - 0 } optional
        sort: id | date_created | last_updated                // { default - id } optional
        order:DESC/ASC          // { default - DESC } optional
        */


        $this->form_validation->set_rules('ticket_id', 'Ticket ID', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('user_id', 'User ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('status', 'User ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('search', 'Search keyword', 'trim|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        } else {
            $ticket_id = isset($_POST['ticket_id']) && is_numeric($_POST['ticket_id']) && !empty(trim($_POST['ticket_id'])) ? $this->input->post('ticket_id', true) : "";
            $user_id = isset($_POST['user_id']) && is_numeric($_POST['user_id']) && !empty(trim($_POST['user_id'])) ? $this->input->post('user_id', true) : "";
            $search = isset($_POST['search']) && !empty(trim($_POST['search'])) ? $this->input->post('search', true) : "";
            $limit = $_POST['limit'] ?? (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 10;
            $offset = $_POST['offset'] ?? (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $order = $_POST['order'] ?? (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'DESC';
            $sort = $_POST['sort'] ?? (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'id';

            $data = $this->config->item('type');
            $result = $this->ticket_model->get_messages($ticket_id, $user_id, $search, $offset, $limit, $sort, $order, $data, "");
            print_r(json_encode($result));
        }
    }

    public function update_order_status()
    {
        $this->form_validation->set_rules('order_id', 'Order Id', 'numeric|trim|required|xss_clean');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean');
        $this->form_validation->set_rules('deliver_by', 'Deliver By', 'numeric|trim|xss_clean');
        if (isset($_POST['status']) && !empty($_POST['status']) && $_POST['status'] == 'cancelled') {
            $this->form_validation->set_rules('reason', 'reason', 'trim|required|xss_clean');
        }
        if (isset($_POST['is_self_pick_up']) && !empty($_POST['is_self_pick_up']) && $_POST['status'] != 'cancelled') {
            $this->form_validation->set_rules('owner_note', 'owner_note', 'trim|xss_clean');
            $this->form_validation->set_rules('self_pickup_time', 'self_pickup_time', 'trim|required|xss_clean');
        }

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;

            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
        } else {
            $msg = '';
            $order_id = $this->input->post('order_id', true);
            $deliver_by = (isset($_POST['deliver_by']) && !empty($_POST['deliver_by'])) ? $this->input->post('deliver_by', true) : "";
            $settings = get_settings('system_settings', true);
            $app_name = isset($settings['app_name']) && !empty($settings['app_name']) ? $settings['app_name'] : '';
            $reason = (isset($_POST['reason']) && !empty($_POST['reason'])) ? $this->input->post('reason', true) : "";
            $owner_note = (isset($_POST['owner_note']) && !empty($_POST['owner_note'])) ? $this->input->post('owner_note', true) : "";
            $self_pickup_time = (isset($_POST['self_pickup_time']) && !empty($_POST['self_pickup_time'])) ? $this->input->post('self_pickup_time', true) : "";
            $status = $this->input->post('status', true);

            if (isset($deliver_by) && !empty($deliver_by)) {

            }
            $res = validate_order_status($order_id, $status, 'orders');
            if ($res['error']) {
                $this->response['error'] = true;
                $this->response['message'] = $msg . $res['message'];

                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }

            if (isset($deliver_by) && !empty($deliver_by) && isset($order_id) && !empty($order_id)) {
                if ($status == "pending") {
                    $this->response['error'] = true;
                    $this->response['message'] = "First confirm the order by restaurant then you can assign rider for this order.";

                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }
                $result = update_rider($deliver_by, $order_id, $status);
                if ($result['error']) {
                    $this->response['error'] = true;
                    $this->response['message'] = $result['message'];

                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                } else {
                    $msg = $result['message'];
                }
            }
            $priority_status = array();
            if ($status == 'out_for_delivery') {
                $priority_status = [
                    'pending' => 0,
                    'confirmed' => 1,
                    'preparing' => 2,
                    'ready_for_pickup' => 4,
                    'out_for_delivery' => 5,
                    'delivered' => 6,
                    'cancelled' => 7,
                ];
            } else {
                $priority_status = [
                    'pending' => 0,
                    'confirmed' => 1,
                    'preparing' => 2,
                    'ready_for_pickup' => 3,
                    'delivered' => 4,
                    'cancelled' => 5,
                ];
            }

            $update_status = 1;
            $error = TRUE;
            $message = '';

            $where_id = "id = " . $order_id . " and (active_status != 'cancelled'  ) ";

            if (isset($order_id) && isset($status)) {
                if ($update_status == 1) {

                    $current_orders_status = fetch_details($where_id, 'orders', 'user_id,active_status');
                    $user_id = $current_orders_status[0]['user_id'];
                    $current_orders_status = $current_orders_status[0]['active_status'];
                    if ($priority_status[$status] > $priority_status[$current_orders_status]) {
                        $set = [
                            'status' => $status, // status => 'proceesed'
                            "reason" => $reason,
                            "owner_note" => $owner_note,
                            "self_pickup_time" => $self_pickup_time,
                            "cancel_by" => $this->session->userdata('user_id')
                        ];
                        if ($this->Order_model->update_order($set, $where_id, true)) {
                            if ($this->Order_model->update_order(['active_status' => $status], $where_id)) {
                                $error = false;
                            }
                        }

                        if ($status == "cancelled") {
                            if (is_exist(['order_id' => $order_id], "pending_orders")) {
                                delete_details(['order_id' => $order_id], "pending_orders");
                            }
                        }


                        if ($error == false) {
                            /* Send notification */

                            // custome notification

                            if ($status == 'pending') {
                                $type = ['type' => "customer_order_pending"];
                            } elseif ($status == 'confirmed') {
                                $type = ['type' => "customer_order_confirm"];
                            } elseif ($status == 'preparing') {
                                $type = ['type' => "customer_order_preparing"];
                            } elseif ($status == 'delivered') {
                                $type = ['type' => "customer_order_delivered"];
                            } elseif ($status == 'cancelled') {
                                $type = ['type' => "customer_order_cancel"];
                            } elseif ($status == 'out_for_delivery') {
                                $type = ['type' => "customer_order_out_for_delivery"];
                            } elseif ($status == 'ready_for_pickup') {
                                $type = ['type' => "customer_order_ready_for_pickup"];
                            }

                            $custom_notification = fetch_details($type, 'custom_notifications', '*');

                            $hashtag_order_id = '< order_item_id >';
                            $hashtag_application_name = '< application_name >';
                            $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                            $hashtag = html_entity_decode($string);
                            $data = str_replace(array($hashtag_order_id, $hashtag_application_name), array($order_id, $app_name), $hashtag);
                            $message = output_escaping(trim($data, '"'));

                            $title = (!empty($custom_notification)) ? $custom_notification[0]['title'] : 'Order status updated';
                            $body = (!empty($custom_notification)) ? $message : 'Order status updated to ' . $status . ' for your order ID #' . $order_id . ' please take note of it! Thank you for ordering with us.';
                            send_notifications($user_id, "user", $title, $body, "order", $order_id);

                            /* Process refer and earn bonus */
                            process_refund($order_id, $status, 'orders');
                            if (trim($status == 'cancelled')) {
                                $data = fetch_details(['order_id' => $order_id], 'order_items', 'product_variant_id,quantity');
                                $product_variant_ids = $qtns = [];
                                foreach ($data as $d) {
                                    array_push($product_variant_ids, $d['product_variant_id']);
                                    array_push($qtns, $d['quantity']);
                                }
                                update_stock($product_variant_ids, $qtns, 'plus');
                            }
                            $response = process_referral_bonus($user_id, $order_id, $status);
                            $order_data = fetch_orders($order_id);
                            $message = 'Status Updated Successfully';
                        }
                    }
                }
                if ($error == true) {
                    $message = $msg . 'Status Updation Failed';
                }
            }
            // print_r($order_data);
            $response['error'] = $error;
            $response['message'] = $message;
            $response['data'] = isset($order_data) && !empty($order_data) ? $order_data['order_data'] : array();

            print_r(json_encode($response));
        }
    }
    public function get_cities()
    {

        /*
           sort:               // { c.name / c.id } optional
           order:DESC/ASC      // { default - ASC } optional
           search:value        // {optional}
           limit:10            // {pass default limit for city list}{default : 25}
           offset:0            // {optional default :0}
       */
        $this->form_validation->set_rules('sort', 'sort', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');
        $this->form_validation->set_rules('search', 'search', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $this->input->post('sort', true) : 'c.name';
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $this->input->post('order', true) : 'ASC';
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $limit = (isset($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
            $offset = (isset($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;

            $result = $this->Area_model->get_cities($sort, $order, $search, $limit, $offset);
            print_r(json_encode($result));
        }
    }

    public function manage_rider_cash_collection()
    {

        /*
            rider_id:57
            amount:123
            transaction_date: 2021-12-08T16:13  // {optional}
            message:test  //{optional}
        */

        $this->form_validation->set_rules('rider_id', 'Rider', 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|xss_clean|numeric|greater_than[0]');
        $this->form_validation->set_rules('message', 'Message', 'trim|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            echo json_encode($this->response);
            return false;
        } else {
            $rider_id = $this->input->post('rider_id', true);
            if (!is_exist(['id' => $rider_id], 'users')) {
                $this->response['error'] = true;
                $this->response['message'] = 'Rider is not exist in your database';
                print_r(json_encode($this->response));
                return false;
            }
            $res = fetch_details(['id' => $rider_id], 'users', 'cash_received');
            $amount = $this->input->post('amount', true);
            $date = (isset($_POST['transaction_date']) && !empty($_POST['transaction_date'])) ? $this->input->post('transaction_date', true) : date("Y-m-d H:i:s");
            $message = (isset($_POST['message']) && !empty($_POST['message'])) ? $this->input->post('message', true) : "Rider cash collection by admin";
            if ($res[0]['cash_received'] < $amount) {
                $this->response['error'] = true;
                $this->response['message'] = 'Amount must be not be greater than cash';
                echo json_encode($this->response);
                return false;
            }
            if ($res[0]['cash_received'] > 0 && $res[0]['cash_received'] != null) {
                update_cash_received($amount, $rider_id, "deduct");
                $this->load->model("transaction_model");
                $transaction_data = [
                    'transaction_type' => "transaction",
                    'user_id' => $rider_id,
                    'order_id' => "",
                    'type' => "rider_cash_collection",
                    'txn_id' => "",
                    'amount' => $amount,
                    'status' => "1",
                    'message' => $message,
                    'transaction_date' => $date,
                ];
                $this->transaction_model->add_transaction($transaction_data);
                $this->response['error'] = false;
                $this->response['message'] = 'Amount Successfully Collected';
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Cash should be greater than 0';
            }
            echo json_encode($this->response);
            return false;
        }
    }
    public function get_rider_cash_collection()
    {

        /* 
        rider_id:15  // {optional}
        status:             // {rider_cash (rider collected) | rider_cash_collection (admin collected)}
        limit:25            // { default - 25 } optional
        offset:0            // { default - 0 } optional
        sort:               // { id } optional
        order:DESC/ASC      // { default - DESC } optional
        search:value        // {optional} 
        */


        $this->form_validation->set_rules('rider_id', 'Rider', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');
        $this->form_validation->set_rules('search', 'search', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        } else {
            $filters['rider_id'] = (isset($_POST['rider_id']) && is_numeric($_POST['rider_id']) && !empty(trim($_POST['rider_id']))) ? $this->input->post('rider_id', true) : '';
            $filters['status'] = (isset($_POST['status']) && !empty(trim($_POST['status']))) ? $this->input->post('status', true) : '';
            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 10;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $this->input->post('sort', true) : 'transactions.id';
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $this->input->post('order', true) : 'DESC';
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : '';
            $tmpRow = $rows = array();
            $data = $this->Rider_model->get_rider_cash_collection($limit, $offset, $sort, $order, $search, (isset($filters)) ? $filters : null);
            if (isset($data['data']) && !empty($data['data'])) {
                foreach ($data['data'] as $row) {
                    $tmpRow['id'] = $row['id'];
                    $tmpRow['name'] = $row['name'];
                    $tmpRow['mobile'] = $row['mobile'];
                    $tmpRow['order_id'] = (isset($row['order_id']) && !empty($row['order_id'])) ? $row['order_id'] : "";
                    $tmpRow['cash_received'] = $row['cash_received'];
                    $tmpRow['type'] = $row['type'];
                    $tmpRow['amount'] = $row['amount'];
                    $tmpRow['message'] = $row['message'];
                    $tmpRow['transaction_date'] = $row['transaction_date'];
                    $tmpRow['date'] = $row['date'];
                    if (isset($row['order_id']) && !empty($row['order_id']) && $row['order_id'] != "") {
                        $order_data = fetch_orders($row['order_id']);
                        $tmpRow['order_details'] = $order_data['order_data'][0];
                    } else {
                        $tmpRow['order_details'] = "";
                    }
                    $rows[] = $tmpRow;
                }
                if ($data['error'] == false) {
                    $data['data'] = $rows;
                } else {
                    $data['data'] = array();
                }
            }
            print_r(json_encode($data));
        }
    }

    public function update_customer_wallet()
    {

        //user_id:635
        //type:credit
        //amount:200
        //message:wallet credit

        $this->form_validation->set_rules('user_id', 'User ID', 'trim|required|xss_clean');
        $this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('message', 'Message', 'trim|required|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
        } else {
            if ($_POST['type'] == 'debit' || $_POST['type'] == 'credit') {
                $message = (isset($_POST['message']) && !empty($_POST['message'])) ? $this->input->post('message', true) : "Balance " . $_POST['type'] . "ed.";
                $response = update_wallet_balance($_POST['type'], $_POST['user_id'], $_POST['amount'], $message);
                print_r(json_encode($response));
            }
        }
    }


    public function get_sliders()
    {
        // branch_id : 7

        $this->form_validation->set_rules('branch_id', 'Branch Id', 'trim|required|xss_clean|numeric');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            echo json_encode($this->response);
            return false;
        } else {
            $branch_id = (isset($_POST['branch_id']) && !empty($_POST['branch_id'])) ? $_POST['branch_id'] : '';
            $res = $this->Slider_model->get_slider($branch_id);
            // print_r($res);
            if (isset($res) && !empty($res)) {

                $i = 0;
                foreach ($res as $row) {
                    
                    $res[$i]['branch_details'] = fetch_details(['id' => $res[$i]['branch_id']], 'branch')[0];
                    $res[$i]['relative_path'] = $res[$i]['relative_path'];
                    $res[$i]['image'] = $res[$i]['image'];
                    if (strtolower($res[$i]['type']) == 'categories') {
                        $id = (!empty($res[$i]['type_id']) && isset($res[$i]['type_id'])) ? $res[$i]['type_id'] : '';
                        $cat_res = $this->category_model->get_categories($id);
                        $res[$i]['data'] = $cat_res;
                    } else if (strtolower($res[$i]['type']) == 'products') {
                        $id = (!empty($res[$i]['type_id']) && isset($res[$i]['type_id'])) ? $res[$i]['type_id'] : '';
                        $pro_res = fetch_product(NULL, NULL, NULL,$id);
                        $res[$i]['data'] = $pro_res['product'];
                    } else {
                        $res[$i]['data'] = [];
                    }
                    $i++;
                }

                $this->response['error'] = false;
                $this->response['message'] = 'Data retrieved successfully';
                $this->response['data'] = $res;
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Data not retrieved';
                $this->response['data'] = array();
            }
            print_r(json_encode($this->response));
        }
    }

    public function manage_slider()
    {

        // branch_id : 1
        // slider_type : default/categories/products
        // image : 
        // category_id : 1  if type is categories
        // product_id : 1 if type is products 
        // edit_slider : 1 if wants to update slider 

        $this->form_validation->set_rules('branch_id', 'Branch ID', 'trim|xss_clean|required');
        $this->form_validation->set_rules('slider_type', 'Slider Type', 'trim|xss_clean|required');
        $this->form_validation->set_rules('image', 'Slider Image', 'trim|required|xss_clean', array('required' => 'Slider image is required'));

        if (isset($_POST['slider_type']) && $_POST['slider_type'] == 'categories') {
            $this->form_validation->set_rules('category_id', 'Category id', 'trim|required|xss_clean');
        }
        if (isset($_POST['slider_type']) && $_POST['slider_type'] == 'products') {
            $this->form_validation->set_rules('product_id', 'Product', 'trim|required|xss_clean');
        }

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
        } else {
            $branch_ids = isset($_POST['branch_id']) ? explode(",", $_POST['branch_id']) : [];
            foreach ($branch_ids as $branch_id) {
                $is_branch_exist = fetch_details(['id' => $branch_id], 'branch');
                if (empty($is_branch_exist)) {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Branch does not exist';
                    print_r(json_encode($this->response));
                    return false;
                }
            }
            $_POST['branch'] = isset($_POST['branch_id']) ? explode(",", $_POST['branch_id']) : [];

            $slider_id = $this->Slider_model->add_slider($_POST);
            if(isset($_POST['edit_slider']) && !empty($_POST['edit_slider'])){
                $data = fetch_details(['id' => $_POST['edit_slider']], 'sliders');
                if (strtolower($data[0]['type']) == 'categories') {
                    $id = (!empty($data[0]['type_id']) && isset($data[0]['type_id'])) ? $data[0]['type_id'] : '';
                    $cat_res = $this->category_model->get_categories($id);
                    $data[0]['data'] = $cat_res;
                } else if (strtolower($data[0]['type']) == 'products') {
                    $id = (!empty($data[0]['type_id']) && isset($data[0]['type_id'])) ? $data[0]['type_id'] : '';
                    $pro_res = fetch_product(NULL, NULL, NULL,$id);
                    $data[0]['data'] = $pro_res['product'];
                } else {
                    $data[0]['data'] = [];
                }
                $data[0]['relative_path'] = $data[0]['image'];
                $data[0]['image'] = base_url($data[0]['image']);
                $branch_detail = fetch_details(['id' => $data[0]['branch_id']], 'branch');
                $data[0]['branch_details'] = $branch_detail[0];
            }else{
                $data = [];
                foreach ($slider_id as $key => $value) {
                    $key1 =  fetch_details(['id' => $value], 'sliders');

                    if (strtolower($key1[0]['type']) == 'categories') {
                        $id = (!empty($key1[0]['type_id']) && isset($key1[0]['type_id'])) ? $key1[0]['type_id'] : '';
                        $cat_res = $this->category_model->get_categories($id);
                        $key1[0]['data'] = $cat_res;
                    } else if (strtolower($key1[0]['type']) == 'products') {
                        $id = (!empty($key1[0]['type_id']) && isset($key1[0]['type_id'])) ? $key1[0]['type_id'] : '';
                        $pro_res = fetch_product(NULL, NULL, NULL,$id);
                        $key1[0]['data'] = $pro_res['product'];
                    } else {
                        $key1[0]['data'] = [];
                    }

                    $key1[0]['relative_path'] = $key1[0]['image'];
                    $key1[0]['image'] = base_url($key1[0]['image']);
                    $branch_detail = fetch_details(['id' => $key1[0]['branch_id']], 'branch');
                    $key1[0]['branch_details'] = $branch_detail[0];
                    $data[] = $key1[0];
                }
               
            }
            
            $this->response['error'] = false;
            $message = (isset($_POST['edit_slider'])) ? 'Slider Updated Successfully' : 'Slider Added Successfully';
            $this->response['message'] = $message;
            $this->response['data'] = $data;
            print_r(json_encode($this->response));
        }
    }


    public function delete_slider()
    {
        /*
            slider_id : 1 {required}
        */

        $this->form_validation->set_rules('slider_id', 'Slider Id', 'trim|xss_clean|numeric|required');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        } else {
            if (delete_details(['id' => $_POST['slider_id']], 'sliders') == TRUE) {
                $this->response['error'] = false;
                $this->response['message'] = 'Slider Deleted Successfully';
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Something Went Wrong';
            }
            print_r(json_encode($this->response));
        }
    }


    public function manage_offer()
    {

        // branch_id : 7
        // offer_type : default/categories/products
        // image : relative_path url
        // start_date : 2024-08-29 (YYYY-MM-DD)
        // end_date : 2024-10-30 (YYYY-MM-DD)
        // category_id : 1  if type is categories
        // product_id : 1 if type is products 
        // edit_offer : 1 if wants to update slider 

        $this->form_validation->set_rules('offer_type', 'Offer Type', 'trim|required|xss_clean');
        $this->form_validation->set_rules('branch_id', 'Branch ID', 'trim|required|xss_clean');
        $this->form_validation->set_rules('start_date', 'Start Date', 'trim|required|xss_clean');
        $this->form_validation->set_rules('end_date', 'End Date', 'trim|required|xss_clean');
        $this->form_validation->set_rules('image', 'Offer Image', 'trim|required|xss_clean', array('required' => 'Offer image is required'));


        if (isset($_POST['offer_type']) && $_POST['offer_type'] == 'categories' ?? '') {
            $this->form_validation->set_rules('category_id', 'Category Id', 'trim|required|xss_clean');
        } else if (isset($_POST['offer_type']) && $_POST['offer_type'] == 'products' ?? '') {
            $this->form_validation->set_rules('product_id', 'Product Id', 'trim|required|xss_clean');
        }

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
        } else {
            $branch_ids = isset($_POST['branch_id']) ? explode(",", $_POST['branch_id']) : [];
            foreach ($branch_ids as $branch_id) {
                $is_branch_exist = fetch_details(['id' => $branch_id], 'branch');
                if (empty($is_branch_exist)) {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Branch does not exist';
                    print_r(json_encode($this->response));
                    return false;
                }
            }
            $res = $this->Offer_model->add_offer($_POST);
            // print_r($res);
            // die;
            if(isset($_POST['edit_offer']) && !empty($_POST['edit_offer'])){

                $offer_data = fetch_details(['id' => $_POST['edit_offer']], 'offers');
                if (strtolower($offer_data[0]['type']) == 'categories') {
                    $id = (!empty($offer_data[0]['type_id']) && isset($offer_data[0]['type_id'])) ? $offer_data[0]['type_id'] : '';
                    $cat_res = $this->category_model->get_categories($id);
                    $offer_data[0]['data'] = $cat_res;
                } else if (strtolower($offer_data[0]['type']) == 'products') {
                    $id = (!empty($offer_data[0]['type_id']) && isset($offer_data[0]['type_id'])) ? $offer_data[0]['type_id'] : '';
                    $pro_res = fetch_product(NULL, NULL, NULL,$id);
                    $offer_data[0]['data'] = $pro_res['product'];
                } else {
                    $offer_data[0]['data'] = [];
                }
                $offer_data[0]['relative_path'] = $offer_data[0]['image'];
                $offer_data[0]['image'] = base_url($offer_data[0]['image']);
                $offer_data[0]['branch_details'] = fetch_details(['id' => $offer_data[0]['branch_id']], 'branch')[0];
            }else{

                $offer_data = fetch_details(['id' => $res], 'offers');
                if (strtolower($offer_data[0]['type']) == 'categories') {
                    $id = (!empty($offer_data[0]['type_id']) && isset($offer_data[0]['type_id'])) ? $offer_data[0]['type_id'] : '';
                    $cat_res = $this->category_model->get_categories($id);
                    $offer_data[0]['data'] = $cat_res;
                } else if (strtolower($offer_data[0]['type']) == 'products') {
                    $id = (!empty($offer_data[0]['type_id']) && isset($offer_data[0]['type_id'])) ? $offer_data[0]['type_id'] : '';
                    $pro_res = fetch_product(NULL, NULL, NULL,$id);
                    $offer_data[0]['data'] = $pro_res['product'];
                } else {
                    $offer_data[0]['data'] = [];
                }
                $offer_data[0]['relative_path'] = $offer_data[0]['image'];
                $offer_data[0]['image'] = base_url($offer_data[0]['image']);
                $offer_data[0]['branch_details'] = fetch_details(['id' => $offer_data[0]['branch_id']], 'branch')[0];

            }
            $this->response['error'] = false;
            $message = (isset($_POST['edit_offer'])) ? 'Offer Images Update Successfully' : 'Offer Images Added Successfully';
            $this->response['message'] = $message;
            $this->response['data'] = $offer_data;
            print_r(json_encode($this->response));
        }
    }
    public function delete_offer()
    {

        /*
          offer_id : 1 {required}
        */

        $this->form_validation->set_rules('offer_id', 'Offer Id', 'trim|required|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
        } else {
            $id = $this->input->post('offer_id');
            if (!is_exist(['id' => $id], 'offers')) {
                $this->response['error'] = true;
                $this->response['message'] = 'Offer is not exist in your database';
            } else {
                if (delete_details(['id' => $id], 'offers')) {
                    $this->response['error'] = false;
                    $this->response['message'] = 'Offer Deleted Successfully';
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Something Went Wrong';
                }
            }
        }
        echo json_encode($this->response);
    }


    public function get_offer_images()
    {

        $this->form_validation->set_rules('branch_id', 'Branch ID', 'trim|required|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
        } else {
            $branch_id = (isset($_POST['branch_id']) && !empty($_POST['branch_id'])) ? $_POST['branch_id'] : '';
            $res = fetch_details(['branch_id' => $branch_id], 'offers', '');
            $i = 0;
            foreach ($res as $row) {
                $res[$i]['branch_details'] = fetch_details(['id' => $res[$i]['branch_id']], 'branch')[0];
                $res[$i]['relative_path'] = $res[$i]['image'];
                $res[$i]['image'] = base_url($res[$i]['image']);
                if (strtolower($res[$i]['type']) == 'categories') {
                    $id = (!empty($res[$i]['type_id']) && isset($res[$i]['type_id'])) ? $res[$i]['type_id'] : '';
                    $cat_res = $this->category_model->get_categories($id);
                    $res[$i]['data'] = $cat_res;
                } else if (strtolower($res[$i]['type']) == 'products') {
                    $id = (!empty($res[$i]['type_id']) && isset($res[$i]['type_id'])) ? $res[$i]['type_id'] : '';
                    $pro_res = fetch_product(NULL, NULL, NULL, $id);
                    $res[$i]['data'] = $pro_res['product'];
                } else {
                    $res[$i]['data'] = [];
                }
                $i++;
            }
            $this->response['error'] = false;
            $this->response['message'] = 'Offer Images Retrived Successfully';
            $this->response['data'] = $res;
            print_r(json_encode($this->response));
        }
    }

    public function send_message()
    {

        /*
            user_type:admin
            user_id:1
            ticket_id:1	
            message:test	
            attachments[]:files  {optional} {type allowed -> image,video,document,spreadsheet,archive}
        */

        $this->form_validation->set_rules('user_type', 'User Type', 'trim|required|xss_clean');
        $this->form_validation->set_rules('ticket_id', 'Ticket id', 'trim|required|numeric|xss_clean');
        $this->form_validation->set_rules('user_id', 'User id', 'trim|required|numeric|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            $user_type = $this->input->post('user_type', true);
            $user_id = isset($_POST['user_id']) && $_POST['user_id'] !== null ? $_POST['user_id'] : '';
            // $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';  uncomment after adding token system
            $ticket_id = $this->input->post('ticket_id', true);
            $message = (isset($_POST['message']) && !empty(trim($_POST['message']))) ? $this->input->post('message', true) : "";


            $user = fetch_users($user_id);
            if (empty($user)) {
                $this->response['error'] = true;
                $this->response['message'] = "User not found!";
                $this->response['data'] = [];
                print_r(json_encode($this->response));
                return false;
            }
            if (!file_exists(FCPATH . TICKET_IMG_PATH)) {
                mkdir(FCPATH . TICKET_IMG_PATH, 0777);
            }

            $temp_array = array();
            $files = $_FILES;
            $images_new_name_arr = array();
            $images_info_error = "";
            $allowed_media_types = implode('|', allowed_media_types());
            $config = [
                'upload_path' => FCPATH . TICKET_IMG_PATH,
                'allowed_types' => $allowed_media_types,
                'max_size' => 8000,
            ];


            if (!empty($_FILES['attachments']['name'][0]) && isset($_FILES['attachments']['name'])) {
                $other_image_cnt = count($_FILES['attachments']['name']);
                $other_img = $this->upload;
                $other_img->initialize($config);

                for ($i = 0; $i < $other_image_cnt; $i++) {

                    if (!empty($_FILES['attachments']['name'][$i])) {

                        $_FILES['temp_image']['name'] = $files['attachments']['name'][$i];
                        $_FILES['temp_image']['type'] = $files['attachments']['type'][$i];
                        $_FILES['temp_image']['tmp_name'] = $files['attachments']['tmp_name'][$i];
                        $_FILES['temp_image']['error'] = $files['attachments']['error'][$i];
                        $_FILES['temp_image']['size'] = $files['attachments']['size'][$i];
                        if (!$other_img->do_upload('temp_image')) {
                            $images_info_error = 'attachments :' . $images_info_error . ' ' . $other_img->display_errors();
                        } else {
                            $temp_array = $other_img->data();
                            resize_review_images($temp_array, FCPATH . TICKET_IMG_PATH);
                            $images_new_name_arr[$i] = TICKET_IMG_PATH . $temp_array['file_name'];
                        }
                    } else {
                        $_FILES['temp_image']['name'] = $files['attachments']['name'][$i];
                        $_FILES['temp_image']['type'] = $files['attachments']['type'][$i];
                        $_FILES['temp_image']['tmp_name'] = $files['attachments']['tmp_name'][$i];
                        $_FILES['temp_image']['error'] = $files['attachments']['error'][$i];
                        $_FILES['temp_image']['size'] = $files['attachments']['size'][$i];
                        if (!$other_img->do_upload('temp_image')) {
                            $images_info_error = $other_img->display_errors();
                        }
                    }
                }

                //Deleting Uploaded attachments if any overall error occured
                if ($images_info_error != NULL || !$this->form_validation->run()) {
                    if (isset($images_new_name_arr) && !empty($images_new_name_arr || !$this->form_validation->run())) {
                        foreach ($images_new_name_arr as $key => $val) {
                            unlink(FCPATH . TICKET_IMG_PATH . $images_new_name_arr[$key]);
                        }
                    }
                }
            }
            if ($images_info_error != NULL) {
                $this->response['error'] = true;
                $this->response['message'] = $images_info_error;
                print_r(json_encode($this->response));
                return false;
            }
            $data = array(
                'user_type' => $user_type,
                'user_id' => $user_id,
                'ticket_id' => $ticket_id,
                'message' => $message
            );
            if (!empty($_FILES['attachments']['name'][0]) && isset($_FILES['attachments']['name'])) {
                $data['attachments'] = $images_new_name_arr;
            }
            $insert_id = $this->ticket_model->add_ticket_message($data);
            $system_settings = get_settings('system_settings', true);
            if (!empty($insert_id)) {
                $data1 = $this->config->item('type');
                $result = $this->ticket_model->get_messages($ticket_id, $user_id, "", "", "1", "", "", $data1, $insert_id);
                if (!empty($result)) {
                    /* Send custom notification message */

                    $ticket_res = fetch_details(['user_type' => 'user', 'ticket_id' => $ticket_id], 'ticket_messages', 'user_id');

                    $user_res = fetch_details(['id' => $ticket_res[0]['user_id']], "users", 'fcm_id', '', '', '', '');
                    $fcm_ids[0][] = $user_res[0]['fcm_id'];

                    $custom_notification = fetch_details(['type' => "ticket_message"], 'custom_notifications', '');

                    $hashtag_application_name = '< application_name >';

                    $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                    $hashtag = html_entity_decode($string);

                    $data = str_replace($hashtag_application_name, $system_settings['app_name'], $hashtag);
                    $message = output_escaping(trim($data, '"'));

                    $fcm_admin_subject = (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Attachments";
                    $fcm_admin_msg = (!empty($custom_notification)) ? $message : "Ticket Message";

                    if (!empty($fcm_ids)) {
                        $fcmMsg = array(
                            'title' => $fcm_admin_subject,
                            'body' => $fcm_admin_msg,
                            'type' => "ticket_message",
                            'type_id' => $ticket_id,
                            'chat' => json_encode($result['data']),
                            'content_available' => true
                        );
                        send_notification($fcmMsg, $fcm_ids,$fcmMsg,$fcm_admin_subject,$fcm_admin_msg,"ticket_message");
                    }
                }
                $this->response['error'] = false;
                $this->response['message'] = 'Ticket Message Added Successfully!';
                $this->response['data'] = $result['data'][0];
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Ticket Message Not Added';
                $this->response['data'] = (!empty($this->response['data'])) ? $this->response['data'] : [];
            }
        }
        print_r(json_encode($this->response));
    }

    public function update_fcm()
    {

        /* Parameters to be passed
             user_id:12
             fcm_id: FCM_ID
         */

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        }
        $user_id = isset($_POST['user_id']) && $_POST['user_id'] !== null ? $_POST['user_id'] : '';
        // $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';  uncomment after adding token system
        $user_res = update_details(['fcm_id' => $_POST['fcm_id']], ['id' => $user_id], 'users');

        if ($user_res) {
            $response['error'] = false;
            $response['message'] = 'Updated Successfully';
            $response['data'] = array();
            echo json_encode($response);
            return false;
        } else {
            $response['error'] = true;
            $response['message'] = 'Updation Failed !';
            $response['data'] = array();
            echo json_encode($response);
            return false;
        }
    }

    public function update_user_status()
    {
        $this->form_validation->set_rules('user_id', 'User ID', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('status', 'Status', 'trim|numeric|required|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        } else {
            $user_id = isset($_POST['user_id']) && $_POST['user_id'] !== null ? $_POST['user_id'] : '';
            $status = isset($_POST['status']) && $_POST['status'] !== null ? $_POST['status'] : '';
            if (update_details(['active' => $status], ['id' => $user_id], 'users')) {
                $this->response['error'] = false;
                $this->response['message'] = 'Status updated successfully';
                print_r(json_encode($this->response));
                
            }else{
                $this->response['error'] = true;
                $this->response['message'] = 'Something Went Wrong';
                print_r(json_encode($this->response));
            }
        }
    }

     public function manage_promocode(){
        /** Add/Update promocode
         * 
         * promocode_id:7 {only required while updateing the promocode}
         * branch_id:7{required}
         * promo_code:test promocode{required}
         * message:test{required}
         * start_date:2024-06-20 {required}
         * end_date:2024-06-25 {required}
         * no_of_users:5 {required}
         * minimum_order_amount:500 {required}
         * discount:20{required}
         * discount_type: percentage/amount {here need to select from these 2 options} {required}
         * max_discount_amount: 100{required}
         * repeat_usage:0/1 {required} {0: not-allowed, 1: allowed} {required}
         * no_of_repeat_usage:20 {only required when repeat_usage is 1(allowed)}
         * status:0/1 {0: deactive, 1: active}{required}
         * image:text type(relative_path url - from get_media api)
         * 
         **/
             
            $this->form_validation->set_rules('promocode_id', 'Promo Code Id ', 'trim|numeric|xss_clean');
            $this->form_validation->set_rules('branch_id', 'Branch Id ', 'trim|numeric|required|xss_clean');
            $this->form_validation->set_rules('promo_code', 'Promo Code ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('message', 'Message ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('start_date', 'Start date ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('end_date', 'End date ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('no_of_users', 'No of Users ', 'trim|required|numeric|xss_clean');
            $this->form_validation->set_rules('minimum_order_amount', 'Minimum Order Amount ', 'trim|numeric|required|xss_clean');
            $this->form_validation->set_rules('discount', 'Discount ', 'trim|required|numeric|xss_clean');
            $this->form_validation->set_rules('discount_type', 'Discount Type ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('max_discount_amount', 'Maximum Discount Amount ', 'trim|numeric|required|xss_clean');
            $this->form_validation->set_rules('repeat_usage', 'Repeat Usage ', 'trim|required|xss_clean');
            if ($_POST['repeat_usage'] == '1') {
                $this->form_validation->set_rules('no_of_repeat_usage', 'No. of Repeat Usage ', 'trim|required|numeric|xss_clean');
            }
            $this->form_validation->set_rules('status', 'Status ', 'trim|required|xss_clean');

            if (!$this->form_validation->run()) {

                $this->response['error'] = true;
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                if (isset($_POST['promocode_id']) && !empty($_POST['promocode_id'])) {
                
                    
                    $promocode_detail = fetch_details(['id' => $_POST['promocode_id']],'promo_codes','*');

                    if (is_exist(['promo_code' => $_POST['promo_code']], 'promo_codes', $_POST['promocode_id'])) {
                        $response["error"]   = true;
                        $response["message"] = "Promo Code Already Exists !";
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }


                } else {
                    if (is_exist(['promo_code' => $_POST['promo_code']], 'promo_codes')) {
                        $response["error"]   = true;
                        $response["message"] = "Promo Code Already Exists !";
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }
                }
                $minimum_order_amount = $_POST['minimum_order_amount'];
                $discount = $_POST['discount'];

                if ($_POST['discount_type'] == 'percentage') {
                    if ($discount <= 0 || $discount > 100) {
                        $response["error"]   = true;
                        $response["message"] = "Discount is invalid !";
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }
                } elseif ($_POST['discount_type'] == 'amount') {
                    if ($discount >= $minimum_order_amount) {
                        $response["error"]   = true;
                        $response["message"] = "Discount can not be grater then or equal to the Minimum order amount !";
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }
                }
                $startDate = $_POST["start_date"];
                $endDate = $_POST["end_date"];


                $startDateTime = new DateTime($startDate);
                $endDateTime = new DateTime($endDate);

                if ($endDateTime <= $startDateTime) {

                    $this->response['error'] = true;
                    $this->response['message'] = 'End date must be after start date.';
                    print_r(json_encode($this->response));
                } else {
                    
                    $_POST['edit_promo_code'] = $_POST['promocode_id'];
                    $this->Promo_code_model->add_promo_code_details($_POST);
                    $this->response['error'] = false;
                    $message = (isset($_POST['promocode_id'])) ? 'Promo code Updated Successfully' : 'Promo code Added Successfully';
                    $this->response['message'] = $message;
                    print_r(json_encode($this->response));
                }

            }
    }

    public function get_promo_codes()
    {
        /*
      get_promo_codes
             search : Search keyword // { optional }
             limit:25                // { default - 25 } optional
             offset:0                // { default - 0 } optional
             sort: id | date_created | last_updated                // { default - id } optional
             order:DESC/ASC          // { default - DESC } optional
             branch_id : 7 {required}
         */


        $this->form_validation->set_rules('search', 'Search keyword', 'trim|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');
        $this->form_validation->set_rules('branch_id', 'Branch Id', 'trim|required|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 10;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'DESC';
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'id';
            $from_admin_app = true;
            $promo_code = $this->Promo_code_model->get_promo_codes($limit, $offset, $sort, $order, $search,$from_admin_app);
            print_r(json_encode($promo_code));
            return false;
        }
    }

      public function delete_promocode(){
         
        /**
         * 
         * promocode_id : 47 {required}
         **/
 
        $this->form_validation->set_rules('promocode_id', 'Promocode Id', 'trim|required|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
                    $promocode_detail = fetch_details(['id' => $_POST['promocode_id']],'promo_codes','*');
                    if(empty($promocode_detail)){
                                $response["error"]   = true;
                                $response["message"] = "This promocode dose not exist..!";
                                $response["data"] = array();
                                echo json_encode($response);
                                return false;
                    }
                
                           if(delete_details(['id' => $_POST['promocode_id']] , 'promo_codes')){
                            $response["error"]   = false;
                            $response["message"] = "Promocode deleted successfully..!";
                            $response["data"] = array();
                            echo json_encode($response);
                            return false;
                           }else{
                             $response["error"]   = true;
                             $response["message"] = "Something went wrong..!";
                             $response["data"] = array();
                             echo json_encode($response);
                             return false;
                           }

                    
        }
    }

    public function add_attributes()
    {
        /*
            name:color 
            attribute_values:[{"value":"value1"},{"value":"value2"},{"value":"value3"}]       //{JSON ARRAY- index(value) must be same}
       */
        // if (!verify_tokens()) {
        //     return false;
        // }
        $this->form_validation->set_rules('name', 'Attribute Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('attribute_values', 'Attribute Values', 'trim|required|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response["data"] = array();
            print_r(json_encode($this->response));
            return false;
        } else {

            $name = (isset($_POST['name']) && !empty($_POST['name'])) ? $this->input->post("name", true) : "";
            $attribute_values = (isset($_POST['attribute_values']) && !empty($_POST['attribute_values'])) ? $this->input->post("attribute_values", true) : "";

            $data = array(
                'name' => $name,
                'attribute_values' => $attribute_values
            );

            if (is_exist(['name' => $name], 'attributes')) {
                $response["error"]   = true;
                $response["message"] = "This Attribute Already Exist.";
                $response["data"] = array();
                echo json_encode($response);
                return false;
            }
            $res = $this->Attribute_model->add_attributes($data);
            // print_r($res);
            $result = $this->Attribute_model->get_attributes("", "", "", "", 1, true,$res);


            $this->response['error'] = false;
            $this->response['message'] = 'Attribute Added Successfully';
            $this->response["data"] = $result['data'];
            print_r(json_encode($this->response));
        }
    }

    public function edit_attributes()
    {
        /*
            edit_attribute_id:1
            attribute_value_ids:1,2,3,0         // {provide zero if any new value added in edited attribute}
            name:color 
            value_name:red,blue,green,new_value   // {provide new attribute value if new added in edited attribute}
       */
        // if (!verify_tokens()) {
        //     return false;
        // }
        $this->form_validation->set_rules('edit_attribute_id', 'Edit Attribute ID', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('attribute_value_ids', 'Attribute Value IDs', 'trim|required|xss_clean');
        $this->form_validation->set_rules('name', 'Attribute Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('value_name', 'Value Name', 'trim|required|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        } else {

            $edit_attribute_id = $this->input->post("edit_attribute_id", true);
            $attribute_value_ids = $this->input->post("attribute_value_ids", true);
            $value_name = $this->input->post("value_name", true);
            $name = $this->input->post("name", true);

            $data = array(
                'edit_attribute_id' => $edit_attribute_id,
                'value_id' => explode(",", $attribute_value_ids),
                'value_name' => explode(",", $value_name),
                'name' => $name,
            );

            if (is_exist(['name' => $name], 'attributes', $edit_attribute_id)) {
                $response["error"]   = true;
                $response["message"] = "This Attribute Already Exist.";
                $response["data"] = array();
                echo json_encode($response);
                return false;
            }
            // Fetch the attribute_value_ids from attribute_values
            $attribute_values = fetch_details(['attribute_id' => $edit_attribute_id], 'attribute_values', 'id');

            // Convert the $attribute_values array to a flat array of IDs
            $attribute_ids = array_column($attribute_values, 'id');
            // print_r($attribute_ids);
            // die;

            // Passed attribute_value_ids from the edit API
            $passed_attribute_values = isset($attribute_value_ids) && !empty($attribute_value_ids) ? explode(",", $attribute_value_ids) : [];

            // Find the missing IDs
            $missing_ids = array_diff($attribute_ids, $passed_attribute_values);

            // Convert the missing IDs to a comma-separated string
            $missing_ids_string = isset($missing_ids) && !empty($missing_ids) ? implode(",", $missing_ids) : "";
          
            
            if ($this->Attribute_model->add_attributes($data)) {
                $result = $this->Attribute_model->get_attributes("", "", "", "", 1, true, $edit_attribute_id);

                $response["error"]   = true;
                $response["message"] = "This combination already exist ! Please provide a new combination";
                $response["data"] = array();
                echo json_encode($response);
                return false;
            } else {
                $result = $this->Attribute_model->get_attributes("", "", "", "", 1, true, $edit_attribute_id);
                /* while edit if any id is not passed then that value will be deleted from attribute_values */
                if(isset($missing_ids_string) && !empty($missing_ids_string)){
                    delete_details(['id' => $missing_ids_string], 'attribute_values');
                }
                $this->response['error'] = false;
                $this->response['message'] = "Attribute Updated Successfully";
                // $this->response["data"] = array();
                $this->response["data"] = $result['data'];

                print_r(json_encode($this->response));
                return false;
            }
        }
    }

    public function delete_attribute(){

        /*
            attribute_id:1
        */

        // if (!verify_tokens()) {
        //     return false;
        // }
        $this->form_validation->set_rules('attribute_id', 'Attribute ID', 'trim|numeric|required|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        } else {
            $attribute_values = fetch_details(['attribute_id' => $_POST['attribute_id']], 'attribute_values');
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
                $response['error'] = true;
                $response['message'] = "Attribute can not be deleted, Product varients are containing this attributes!.";
                print_r(json_encode($response));
                return false;
            } else {
                if (delete_details(['id' => $_POST['attribute_id']], 'attributes')) {
                    if (delete_details(['attribute_id' => $_POST['attribute_id']], 'attribute_values')) {
                        $response['error'] = false;
                        $response['message'] = "Attribute Deleted Successfully!.";
                        print_r(json_encode($response));
                        return false;
                    }
                }
            }
            
            
        }

    }

    public function get_attributes()
    {
        /*
            sort: name              // { name / id } optional
            order:DESC/ASC      // { default - ASC } optional
            search:value        // {optional} 
            limit:10  {optional}
            offset:10  {optional}
       */
        // if (!verify_tokens()) {
        //     return false;
        // }
        $this->form_validation->set_rules('sort', 'sort', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('search', 'search', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'Limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'Offset', 'trim|numeric|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        } else {
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $this->input->post('sort', true) : 'name';
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $this->input->post('order', true) : 'ASC';
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $limit = ($this->input->post('limit', true)) ? $this->input->post('limit', true) : NULL;
            $offset = ($this->input->post('offset', true)) ? $this->input->post('offset', true) : NULL;
            $result = $this->Attribute_model->get_attributes($sort, $order, $search, $offset, $limit, true);

            print_r(json_encode($result));
        }
    }


    public function get_attribute_values()
    {

        /*
            attribute_id:1  // {optional}
            sort:a.name         // { a.name / a.id } optional
            order:DESC/ASC      // { default - ASC } optional
            search:value        // {optional} 
            limit:10  {optional}
            offset:10  {optional}
       */

        if (!verify_tokens()) {
            return false;
        }
        $this->form_validation->set_rules('sort', 'sort', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('search', 'search', 'trim|xss_clean');
        $this->form_validation->set_rules('attribute_id', 'attribute id', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('limit', 'Limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'Offset', 'trim|numeric|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        } else {
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $this->input->post('sort', true) : 'a.name';
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $this->input->post('order', true) : 'ASC';
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $limit = ($this->input->post('limit', true)) ? $this->input->post('limit', true) : NULL;
            $offset = ($this->input->post('offset', true)) ? $this->input->post('offset', true) : NULL;
            $attribute_id = (isset($_POST['attribute_id']) && !empty(trim($_POST['attribute_id']))) ? $this->input->post('attribute_id', true) : "";
            $result = $this->Attribute_model->get_attribute_value($sort, $order, $search, $attribute_id, $offset, $limit, true);
            print_r(json_encode($result));
        }
    }

   public function manage_tax()
    {
        /*
        id: 1 {required only for update tax}
        title: tag_title {required}
        percentage: 2 {required}
         */

        $this->form_validation->set_rules('id', 'Tag Id', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');
        $this->form_validation->set_rules('percentage', 'Percentage', 'trim|required|numeric|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                if (is_exist(['title' => $_POST['title']], 'taxes')) {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Tax alredy exist !';
                    print_r(json_encode($this->response));
                    return false;
                }

                $this->db->set(['title' => $_POST['title']])->where('id', $_POST['id'])->update('taxes');
                $tax_detail = fetch_details(['id' => $_POST['id']], 'taxes');


                $this->response['error'] = false;
                $this->response['message'] = 'Tax Updated successfully';
                $this->response['data'] = $tax_detail;
                print_r(json_encode($this->response));
            } else {
                if (isset($_POST['title']) && !empty($_POST['title'])) {
                    if (is_exist(['title' => $_POST['title']], 'taxes')) {
                        $this->response['error'] = true;
                        $this->response['message'] = 'Tax alredy exist !';
                        print_r(json_encode($this->response));
                        return false;
                    }
                    $data = [
                        'title' => $_POST['title'],
                        'percentage' => $_POST['percentage'],
                    ];
                    $this->db->insert('taxes', $data);
                    $tax_id = $this->db->insert_id();

                    $tax_detail = fetch_details(['id' => $tax_id], 'taxes');

                    $this->response['error'] = false;
                    $this->response['message'] = 'Tax Adedd successfully';
                    $this->response['data'] = $tax_detail;
                    print_r(json_encode($this->response));
                }
            }
        }
    }

    public function get_taxes()
    {


        /*
           limit:10 {optional}
           offset:0 {optional}
           search:tax_title {optional}
       
        */

        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('search', 'search', 'trim|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {

            $search = isset($_POST['search']) && !empty($_POST['search']) ? $_POST['search'] : "";
            $limit = isset($_POST['limit']) && !empty($_POST['limit']) ? $_POST['limit'] : 25;
            $offset = isset($_POST['offset']) && !empty($_POST['offset']) ? $_POST['offset'] : 0;

            $tax_res = $this->Tax_model->get_taxes($search, $limit, $offset);

            $this->response['error'] = (empty($tax_res)) ? true : false;
            $this->response['total'] = !empty($tax_res['data']) ? count($tax_res['data']) : 0;
            $this->response['message'] = (empty($tax_res)) ? 'Tax does not exist' : 'Tax retrieved successfully';
            $this->response['data'] = $tax_res['data'];

            print_r(json_encode($this->response));
        }
    }

    public function delete_tax()
    {
            /*
                tax_id: 1 {required}
            */

        $this->form_validation->set_rules('tax_id', 'Tax Id', 'required|trim|numeric|xss_clean');
        if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['message'] = strip_tags(validation_errors());
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return;
        } else {
        $tax_details = fetch_details(['id' => $_POST['tax_id']], 'taxes');
        if (!empty($tax_details)) {
            if (delete_details(['id' => $_POST['tax_id']], 'taxes')) {
                $this->response['error'] = false;
                $this->response['message'] = 'Tax Deleted successfully!';
                print_r(json_encode($this->response));
                return false;
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Something went wrong!';
                print_r(json_encode($this->response));
                return false;
            }
        } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Tax does not exist!';
                print_r(json_encode($this->response));
                return false;
        }
            }
    }

    public function update_tax_status(){
        /** 
         * tax_id : 1 {required}
         * status : 0/1 {0: deactive, 1: active} {required}
         **/

         $this->form_validation->set_rules('tax_id', 'Tax Id', 'trim|numeric|required|xss_clean');
         $this->form_validation->set_rules('status', 'Status', 'trim|numeric|required|xss_clean');
         if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        }

        if(update_details(['status' => $_POST['status']],['id' => $_POST['tax_id']],'taxes')){
                $this->response['error'] = false;
                $this->response['message'] = "Status updated successfully !";
                $this->response['data'] = array();
        }else{
                $this->response['error'] = true;
                $this->response['message'] = "something went wrong !";
                $this->response['data'] = array();
        }
            print_r(json_encode($this->response));
    }

   public function get_sections()
    {
            /*
                branch_id:7             {required}
                limit:10            // { default - 25 } {optional}
                offset:0            // { default - 0 } {optional}
                section_id:4            {optional}
                language:en            // { default - en } {optional} - Language code (en, ar, he)
            */

        $this->form_validation->set_rules('branch_id', 'branch id', 'trim|required|xss_clean');
        $this->form_validation->set_rules('limit', 'Limit', 'trim|xss_clean');
        $this->form_validation->set_rules('offset', 'Offset', 'trim|xss_clean');
        $this->form_validation->set_rules('section_id', 'Section Id', 'trim|xss_clean');
        $this->form_validation->set_rules('language', 'Language', 'trim|xss_clean');
        

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        }

        $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
        $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
        $section_id = (isset($_POST['section_id']) && !empty(trim($_POST['section_id']))) ? $this->input->post('section_id', true) : 0;
        
        // Get language parameter, default to 'en'
        $language_code = (isset($_POST['language']) && !empty(trim($_POST['language']))) ? $this->input->post('language', true) : 'en';
        $language_code = $this->db->escape_str($language_code);
        
        // Get product-related parameters
        $user_id = (isset($_POST['user_id']) && !empty(trim($_POST['user_id']))) ? $this->input->post('user_id', true) : '';
        $p_limit = (isset($_POST['p_limit']) && is_numeric($_POST['p_limit']) && !empty(trim($_POST['p_limit']))) ? $this->input->post('p_limit', true) : 10;
        $p_offset = (isset($_POST['p_offset']) && is_numeric($_POST['p_offset']) && !empty(trim($_POST['p_offset']))) ? $this->input->post('p_offset', true) : 0;
        $p_sort = (isset($_POST['p_sort']) && !empty(trim($_POST['p_sort']))) ? $this->input->post('p_sort', true) : 'pid';
        $p_order = (isset($_POST['p_order']) && !empty(trim($_POST['p_order']))) ? $this->input->post('p_order', true) : 'desc';
        $filter_by = (isset($_POST['filter_by']) && !empty(trim($_POST['filter_by']))) ? $this->input->post('filter_by', true) : 'p.id';
        
        // Query sections with translations using LEFT JOIN
        // Prioritize translations table for all languages including English
        // Fallback to main table only if translation doesn't exist (for backward compatibility)
        $this->db->select('s.*, st.title as translated_title, st.short_description as translated_short_description');
        $this->db->from('sections s');
        $this->db->join('section_translations st', "st.section_id = s.id AND st.language_code = '{$language_code}'", 'LEFT');
        $this->db->where('s.branch_id', $_POST['branch_id']);
        if (isset($_POST['section_id']) && !empty($_POST['section_id'])) {
            $this->db->where('s.id', $section_id);
        }
        $this->db->limit($limit, $offset);
        $sections = $this->db->order_by('s.row_order')->get()->result_array();
        
        // Process sections to use translations when available, fallback to English or main table
        for ($i = 0; $i < count($sections); $i++) {
            // Use translation if available, otherwise try English translation, then fallback to main table
            if (!empty($sections[$i]['translated_title'])) {
                $sections[$i]['title'] = $sections[$i]['translated_title'];
                $sections[$i]['short_description'] = !empty($sections[$i]['translated_short_description']) ? $sections[$i]['translated_short_description'] : $sections[$i]['short_description'];
            } elseif ($language_code != 'en') {
                // If requested language is not English and no translation exists, try English translation
                $english_translation = $this->db->where('section_id', $sections[$i]['id'])
                                                 ->where('language_code', 'en')
                                                 ->get('section_translations')
                                                 ->row_array();
                if (!empty($english_translation['title'])) {
                    $sections[$i]['title'] = $english_translation['title'];
                    $sections[$i]['short_description'] = !empty($english_translation['short_description']) ? $english_translation['short_description'] : $sections[$i]['short_description'];
                }
                // Otherwise, keep main table values (backward compatibility)
            }
            // Remove temporary translation fields
            unset($sections[$i]['translated_title']);
            unset($sections[$i]['translated_short_description']);
        }

        if (!empty($sections)) {
            for ($i = 0; $i < count($sections); $i++) {
                $product_ids = explode(',', $sections[$i]['product_ids']);
                $product_ids = array_filter($product_ids);
                $filters['show_only_active_products'] = 1;
                if (isset($_POST['top_rated_foods']) && !empty($_POST['top_rated_foods'])) {
                    $filters['product_type'] = (isset($_POST['top_rated_foods']) && $_POST['top_rated_foods'] == 1) ? 'top_rated_foods_including_all_foods' : null;
                } else {
                    if (isset($sections[$i]['product_type']) && !empty($sections[$i]['product_type'])) {
                        $filters['product_type'] = (isset($sections[$i]['product_type'])) ? $sections[$i]['product_type'] : null;
                    }
                }
                $filters['branch_id'] = (isset($_POST['branch_id']) && !empty($_POST['branch_id'])) ? $this->input->post("branch_id", true) : 0;
                $filters['language'] = $language_code; // Add language to filters for product translations
                $categories = (isset($sections[$i]['categories']) && !empty($sections[$i]['categories']) && $sections[$i]['categories'] != NULL) ? explode(',', $sections[$i]['categories']) : null;

                $products = fetch_product("", $user_id, (isset($filters)) ? $filters : null, (isset($product_ids) && !empty($product_ids)) ? $product_ids : null, $categories, $p_limit, $p_offset, $p_sort, $p_order, null, null, null, $filter_by);
                if (!empty($products['product'])) {
                    $this->response['error'] = false;
                    $this->response['message'] = "Sections retrived successfully";
                    $sections[$i]['title'] = output_escaping($sections[$i]['title']);
                    $sections[$i]['short_description'] = output_escaping($sections[$i]['short_description']);
                    $sections[$i]['categories'] = (isset($sections[$i]['categories']) && !empty($sections[$i]['categories'])) ? $sections[$i]['categories'] : "";
                    $sections[$i]['product_ids'] = (isset($sections[$i]['product_ids']) && !empty($sections[$i]['product_ids'])) ? $sections[$i]['product_ids'] : "";
                    $sections[$i]['total'] = strval(count($products['product']));
                    $sections[$i]['filters'] = (isset($products['filters'])) ? $products['filters'] : [];
                    $sections[$i]['product_tags'] = (isset($products['product_tags']) && !empty($products['product_tags'])) ? $products['product_tags'] : [];
                    $sections[$i]['product_details'] = $products['product'];
                    unset($sections[$i]['product_details'][0]['total']);
                } else {
                    $this->response['error'] = false;
                    $this->response['message'] = "Sections retrived successfully";
                    $sections[$i]['total'] = "0";
                    $sections[$i]['filters'] = [];
                    $sections[$i]['product_details'] = [];
                }
            }
            if (isset($_POST['section_id']) && !empty(trim($_POST['section_id']))) {

                $this->response['total'] = strval(count($sections[0]['product_details']));
            } else {
                $this->response['total'] = strval(count($sections));
            }
            $this->response['data'] = $sections;
        } else {
            $this->response['error'] = true;
            $this->response['message'] = "No sections are available";
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        }
        print_r(json_encode($this->response));
    }

    public function delete_section(){

        /*
            section_id: 1 {required}       
        */

        $this->form_validation->set_rules('section_id', 'Section Id', 'required|trim|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $tag_details = fetch_details(['id' => $_POST['section_id']], 'sections');
            if (!empty($tag_details)) {
                if (delete_details(['id' => $_POST['section_id']], 'sections')) {
                    $this->response['error'] = false;
                    $this->response['message'] = 'Section Deleted successfully!';
                    print_r(json_encode($this->response));
                    return false;
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Something went wrong!';
                    print_r(json_encode($this->response));
                    return false;
                }
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Section does not exist!';
                print_r(json_encode($this->response));
                return false;
            }
        }
     
    }
    
    public function get_addons(){
        
        /* 
            limit : 10 {optional}
            offset : 0 {optional}
            search : capsicome {optional}
        */

        $search = $this->input->post('search', true);
        $limit =  $this->input->post('limit', true);
        $offset = isset($_POST['offset']) ? $this->input->post('offset', true) : 0;

        $this->db->select('*');
        $this->db->from('product_add_ons');

        if (!empty($search)) {
            $this->db->like('title', $search);
        }

        if (!empty($limit)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        $add_on_snaps = $query->result_array();
        
            if(empty($add_on_snaps)){
                $this->response['error'] = true;
                $this->response['message'] = 'Addons not found!';
                $this->response['data'] = array();
            }else{
                $this->response['error'] = false;
                $this->response['message'] = 'Addons fetched successfully!';
                $this->response['data'] = $add_on_snaps;
            }
            print_r(json_encode($this->response));
    }

    public function manage_sections(){

        /*
            id : 2 {only required when update the section}
            title : Food On Offer
            short_description : Food On Offer
            branch_id : 7
            categories : 12,13,14 {only required when product_type is  NOT 'custom_foods'}
            product_type : new_added_foods, food_on_offer, top_rated_foods, most_ordered_foods, custom_foods
            product_ids : 2,3,4 {only required when product_type is 'custom_foods'}
        */
        
        $this->form_validation->set_rules('id', 'Section Id', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('title', 'Title', 'required|trim|xss_clean');
        $this->form_validation->set_rules('short_description', 'Short Description', 'required|trim|xss_clean');
        $this->form_validation->set_rules('branch_id', 'Branch Id', 'required|trim|xss_clean');
        $this->form_validation->set_rules('categories', 'Categories', 'required|trim|xss_clean');
        $this->form_validation->set_rules('product_type', 'Categories', 'required|trim|xss_clean');
        if($_POST['product_type'] == 'custom_foods'){
            $this->form_validation->set_rules('product_ids', 'Product Ids', 'required|trim|xss_clean');
        }
         if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            if(isset($_POST['id']) && !empty($_POST['id'])){
                $data = [
                    'edit_featured_section' => $_POST['id'],
                    'title' => $_POST['title'],
                    'short_description' => $_POST['short_description'],
                    'branch_id' => $_POST['branch_id'],
                    'categories[]' => $_POST['categories'],
                    'product_type' => $_POST['product_type'],
                    'product_ids[]' => $_POST['product_ids'],
                ];
            }else{
                $data = [
                    'title' => $_POST['title'],
                    'short_description' => $_POST['short_description'],
                    'branch_id' => $_POST['branch_id'],
                    'categories[]' => $_POST['categories'],
                    'product_type' => $_POST['product_type'],
                    'product_ids[]' => $_POST['product_ids'],
                ];
            }
            
            $section =  $this->Featured_section_model->add_featured_section($data);

            if(!empty($section)){
                $this->response['error'] = false;
                $this->response['message'] = isset($_POST['id']) ? 'Section updated successfully' : 'Section added successfully';
                $this->response['data'] = array();
            }else{
                $this->response['error'] = true;
                $this->response['message'] = isset($_POST['id']) ? 'Section dose not updated' : 'Section dose not added';
                $this->response['data'] = array();
            }

           print_r(json_encode($this->response));
        }
    }

}

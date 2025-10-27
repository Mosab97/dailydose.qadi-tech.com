<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Orders extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model('Order_model');
        // $this->load->library('Breadcrumb');
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (!has_permissions('read', 'orders')) {
                $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
                redirect('admin/home', 'refresh');
            } 
            $this->data['main_page'] = TABLES . 'manage-orders';
            $settings = get_settings('system_settings', true);
            // $this->data['breadcrumbs'] = $this->breadcrumb->display();
            $this->data['title'] = 'Order Management | ' . $settings['app_name'];
            $this->data['meta_description'] = ' Order Management  | ' . $settings['app_name'];
            $this->data['about_us'] = get_settings('about_us');
            $this->data['curreny'] = get_settings('currency');
            $orders_count['awaiting'] = orders_count("awaiting", $_SESSION['branch_id']);
            $orders_count['pending'] = orders_count("pending", $_SESSION['branch_id']);
            $orders_count['confirmed'] = orders_count("confirmed", $_SESSION['branch_id']);
            $orders_count['preparing'] = orders_count("preparing", $_SESSION['branch_id']);
            $orders_count['ready_for_pickup'] = orders_count("ready_for_pickup", $_SESSION['branch_id']);
            $orders_count['out_for_delivery'] = orders_count("out_for_delivery", $_SESSION['branch_id']);
            $orders_count['delivered'] = orders_count("delivered", $_SESSION['branch_id']);
            $orders_count['cancelled'] = orders_count("cancelled", $_SESSION['branch_id']);
            $orders_count['draft'] = orders_count("draft", $_SESSION['branch_id']);
            $this->data['status_counts'] = $orders_count;
            if (!isset($_SESSION['branch_id'])) {

                redirect('admin/branch', 'refresh');
            } else {

                $this->load->view('admin/template', $this->data);
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function view_orders()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
                return $this->Order_model->get_orders_list();

        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function view_latest_orders()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (has_permissions('read', 'orders')) {
                $latest_orders = 1;
                return $this->Order_model->get_orders_list(NULL, false, NULL, 0, 5," o.id ", "DESC", NULL, $latest_orders);
              
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function delete_orders()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                $this->response['error'] = true;
                $this->response['message'] = DEMO_VERSION_MSG;
                echo json_encode($this->response);
                return false;
                exit();
            }
            if (print_msg(!has_permissions('delete', 'orders'), PERMISSION_ERROR_MSG, 'orders')) {
                return false;
            }
            $delete = array(
                "order_items" => 0,
                "orders" => 0,
            );
            $orders = $this->db->where(' oi.order_id=' . $_GET['id'])->join('orders o', 'o.id=oi.order_id', 'right')->get('order_items oi')->result_array();
            if (!empty($orders)) {
                // delete orders
                if (delete_details(['order_id' => $_GET['id']], 'order_items')) {
                    $delete['order_items'] = 1;
                }
                if (delete_details(['id' => $_GET['id']], 'orders')) {
                    $delete['orders'] = 1;
                }
                if (is_exist(['order_id' => $_GET['id']], "pending_orders")) {
                    delete_details(['order_id' => $_GET['id']], "pending_orders");
                    $delete['orders'] = 1;
                }
            }
            $deleted = FALSE;
            if (!in_array(0, $delete)) {
                $deleted = TRUE;
            }
            if ($deleted == TRUE) {
                $response['error'] = false;
                $response['message'] = 'Order Deleted Successfully';
                $response['permission'] = !has_permissions('delete', 'orders');
            } else {
                $response['error'] = true;
                $response['message'] = 'Something went wrong';
            }
            echo json_encode($response);
        } else {
            redirect('admin/login', 'refresh');
        }
    }


    /* Update complete order status */
    public function update_orders()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('update', 'orders'), PERMISSION_ERROR_MSG, 'orders')) {
                return false;
            }

            $this->form_validation->set_rules('orderid', 'Order Id', 'numeric|trim|required|xss_clean');
            $this->form_validation->set_rules('deliver_by', 'Deliver By', 'numeric|trim|xss_clean');
            $this->form_validation->set_rules('val', 'Val', 'trim|required|xss_clean');
            $this->form_validation->set_rules('field', 'Field', 'trim|required|xss_clean');
            if (isset($_POST['val']) && !empty($_POST['val']) && $_POST['val'] == 'cancelled') {
                $this->form_validation->set_rules('reason', 'reason', 'trim|required|xss_clean');
            }
            if (isset($_POST['is_self_pick_up']) && !empty($_POST['is_self_pick_up']) && $_POST['val'] != 'cancelled') {
                $this->form_validation->set_rules('owner_note', 'owner_note', 'trim|xss_clean');
                $this->form_validation->set_rules('self_pickup_time', 'Self Pickup Time', 'trim|required|xss_clean');
            }

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                $msg = '';
                $order_id = $this->input->post('orderid', true);
                $deliver_by = (isset($_POST['deliver_by']) && !empty($_POST['deliver_by'])) ? $this->input->post('deliver_by', true) : "0";
                $settings = get_settings('system_settings', true);
                $app_name = isset($settings['app_name']) && !empty($settings['app_name']) ? $settings['app_name'] : '';
                $reason = (isset($_POST['reason']) && !empty($_POST['reason'])) ? $this->input->post('reason', true) : "";
                $owner_note = (isset($_POST['owner_note']) && !empty($_POST['owner_note'])) ? $this->input->post('owner_note', true) : "";
                $self_pickup_time = (isset($_POST['self_pickup_time']) && !empty($_POST['self_pickup_time'])) ? $this->input->post('self_pickup_time', true) : "";
                $val = $this->input->post('val', true);
                $field = $this->input->post('field', true);

             
                $res = validate_order_status($order_id, $val, 'orders');


                if ($res['error']) {
                    $this->response['error'] = true;
                    $this->response['message'] = $msg . $res['message'];
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }

                if (isset($deliver_by) && !empty($deliver_by) && isset($order_id) && !empty($order_id)) {
                    if ($val == "pending") {
                        $this->response['error'] = true;
                        $this->response['message'] = "First confirm the order by restaurant then you can assign rider for this order.";
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['data'] = array();
                        print_r(json_encode($this->response));
                        return false;
                    }
                    $result = update_rider($deliver_by, $order_id, $val);
                    if ($result['error']) {
                        $this->response['error'] = true;
                        $this->response['message'] = $result['message'];
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['data'] = array();
                        print_r(json_encode($this->response));
                        return false;
                    } else {
                        $msg  = $result['message'];
                    }
                }
                $priority_status = array();
                if ($val == 'out_for_delivery') {
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

                if (isset($order_id) && isset($field) && isset($val)) {
                    if ($field == 'status' && $update_status == 1) {

                        $current_orders_status = fetch_details($where_id, 'orders', 'user_id,active_status');
                        $user_id = $current_orders_status[0]['user_id'];
                        $current_orders_status = $current_orders_status[0]['active_status'];
                        if ($priority_status[$val] > $priority_status[$current_orders_status]) {
                            $set = [
                                $field => $val, 
                                "reason" => $reason,
                                "owner_note" => $owner_note,
                                "self_pickup_time" => $self_pickup_time,
                                "cancel_by" => $this->session->userdata('user_id')
                            ];
                            if ($this->Order_model->update_order($set, $where_id, $_POST['json'])) {
                                if ($this->Order_model->update_order(['active_status' => $val], $where_id)) {
                                    $error = false;
                                }
                            }

                            if ($val == "cancelled") {
                                if (is_exist(['order_id' => $order_id], "pending_orders")) {
                                    delete_details(['order_id' => $order_id], "pending_orders");
                                }
                            }


                            if ($error == false) {
                                /* Send notification */

                                // custome notification

                                if ($val  == 'pending') {
                                    $type = ['type' => "customer_order_pending"];
                                } elseif ($val == 'confirmed') {
                                    $type = ['type' => "customer_order_confirm"];
                                } elseif ($val == 'preparing') {
                                    $type = ['type' => "customer_order_preparing"];
                                } elseif ($val == 'delivered') {
                                    $type = ['type' => "customer_order_delivered"];
                                } elseif ($val == 'cancelled') {
                                    $type = ['type' => "customer_order_cancel"];
                                } elseif ($val == 'out_for_delivery') {
                                    $type = ['type' => "customer_order_out_for_delivery"];
                                } elseif ($val == 'ready_for_pickup') {
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
                                $body = (!empty($custom_notification)) ? $message :  'Order status updated to ' . $val . ' for your order ID #' . $order_id . ' please take note of it! Thank you for ordering with us.';
                                send_notifications($user_id, "user", $title, $body, "order", $order_id);

                                /* Process refer and earn bonus */
                                process_refund($order_id, $val, 'orders');
                                if (trim($val == 'cancelled')) {
                                    $data = fetch_details(['order_id' => $order_id], 'order_items', 'product_variant_id,quantity');
                                    $product_variant_ids = $qtns = [];
                                    foreach ($data as $d) {
                                        array_push($product_variant_ids, $d['product_variant_id']);
                                        array_push($qtns, $d['quantity']);
                                    }
                                    update_stock($product_variant_ids, $qtns, 'plus');
                                }
                                $response = process_referral_bonus($user_id, $order_id, $val);
                                $message = 'Status Updated Successfully';
                            }
                        }
                    }
                    if ($error == true) {
                        $message = $msg . 'Status Updation Failed';
                    }
                }
                $response['error'] = $error;
                $response['message'] = $message;
                $response['total_amount'] = (!empty($data) ? $data : '');
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function edit_orders()
    {

        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (!has_permissions('read', 'orders')) {
                $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
                redirect('admin/home', 'refresh');
            }
            $this->data['main_page'] = FORMS . 'edit-orders';
            $settings = get_settings('system_settings', true);

            $this->data['title'] = 'View Order | ' . $settings['app_name'];
            $this->data['meta_description'] = 'View Order | ' . $settings['app_name'];
            $res = $this->Order_model->get_order_details(['o.id' => $_GET['edit_id']]);

            if (has_permissions('read', 'rider')) {
            $this->data['delivery_res'] = $this->db->select("u.id, u.username,u.accept_orders, (SELECT COUNT(rider_id) FROM orders WHERE rider_id = u.id AND active_status NOT IN ('cancelled', 'delivered')) as rider_orders")
            ->where(['ug.group_id' => '3', 'u.active' => 1, 'u.accept_orders' => 1])
            ->where("FIND_IN_SET('".$res[0]['user_city']."', u.serviceable_city) >", 0)
            ->join('users_groups ug', 'ug.user_id = u.id')
            ->get('users u')
            ->result_array();
            }else{
                $this->data['delivery_res'] = [];
                $this->data['permissions_message'] = PERMISSION_ERROR_MSG;
            }

            $this->data['branch_name'] = fetch_details(['id' => $res[0]['branch_id']], "branch", "branch_name,address");
            if (isset($_GET['edit_id']) && !empty($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
                if($res[0]['branch_id'] == $_SESSION['branch_id']){
                    $items = [];
                foreach ($res as $row) {

                    $temp['id'] = $row['order_item_id'];
                    $temp['add_ons'] = $row['add_ons'];
                    $temp['item_otp'] = $row['item_otp'];
                    $temp['product_id'] = $row['product_id'];
                    $temp['product_variant_id'] = $row['product_variant_id'];
                    $temp['product_type'] = $row['type'];
                    $temp['pname'] = $row['pname'];
                    $temp['vname'] = $row['variant_name'];
                    $temp['quantity'] = $row['quantity'];
                    $temp['is_cancelable'] = $row['is_cancelable'];
                    $temp['tax_amount'] = $row['tax_amount'];
                    $temp['discounted_price'] = $row['discounted_price'];
                    $temp['price'] = $row['price'];
                    $temp['row_price'] = isset($row['row_price']) ? $row['row_price'] : "";
                    $temp['active_status'] = $row['active_status'];
                    $temp['product_image'] = $row['product_image'];
                    $temp['product_variants'] = get_variants_values_by_id($row['product_variant_id']);
                    array_push($items, $temp);
                }
                $this->data['order_detls'] = $res;

                $this->data['items'] = $items;

                $this->data['settings'] = $settings;
                $this->load->view('admin/template', $this->data);
                }else{
                    redirect('admin/orders/', 'refresh');
                }                
            } else {
                redirect('admin/orders/', 'refresh');
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }
}

<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Home_model extends CI_Model
{

    public function count_new_orders($branch_id = '', $from_app='')
    {
        
        $res = $this->db->select('count(id) as counter');

        // Fetch user_id from session
        if ($from_app != '1') {
            $user_id = $this->session->userdata('user_id');

            // Check if the user is a rider
            if ($this->ion_auth->is_rider()) {
                $this->db->where('o.rider_id', $user_id);
            }
        }

        // Add branch_id condition if provided
        if (!empty($branch_id)) {
            $this->db->where('o.branch_id', $branch_id);
        }

        $res = $this->db->get('orders o')->result_array();
      
        // Ensure a counter value is always returned
        return isset($res[0]['counter']) ? $res[0]['counter'] : 0;
    }


    public function count_orders_by_status($status, $branch_id = '')
    {
        $this->db->select('COUNT(id) as counter');
        $this->db->where('active_status', $status);

        // Add branch_id condition if provided
        if (!empty($branch_id)) {
            $this->db->where('branch_id', $branch_id);
        }

        $res = $this->db->get('orders o')->result_array();
        return $res[0]['counter'];
    }


    public function count_new_users()
    {
        $res = $this->db->select('count(u.id) as counter')->join('users_groups ug', ' ug.`user_id` = u.`id` ')
            ->where('ug.group_id=2')
            ->get('`users u`')->result_array();
        return $res[0]['counter'];
    }

    public function count_riders($branch_id = '')
    {
        $this->db->select('COUNT(u.id) as counter');
        $this->db->where('ug.group_id', '3');
        $this->db->join('users_groups ug', 'ug.user_id = u.id');

        // Add branch_id condition if provided
        if (!empty($branch_id)) {
            $this->db->where('u.branch_id', $branch_id);
        }

        $res = $this->db->get('users u')->result_array();
        return $res[0]['counter'];
    }

    public function count_branch()
    {

            $res = $this->db->select('count(b.id) as counter')->get('`branch` b')->result_array();
           
            return $res[0]['counter'];
        
    }

    public function count_products($branch_id = "")
    {
        $res = $this->db->select('count(id) as counter ');
        if (!empty($branch_id) && $branch_id != '') {
            $res->where('branch_id=' . $branch_id);
        }
        $count = $res->get('`products`')->result_array();
        return $count[0]['counter'];
    }
    public function count_tags($partner_id = "")
    {
        $res = $this->db->select('count(id) as counter ');
        if (!empty($partner_id) && $partner_id != '') {
            $res->where('partner_id=' . $partner_id);
        }
        $count = $res->get('`partner_tags`')->result_array();
        return $count[0]['counter'];
    }

    

public function count_products_stock_low_status($branch_id)
{
    $settings = get_settings('system_settings', true);
    $low_stock_limit = isset($settings['low_stock_limit']) ? $settings['low_stock_limit'] : 5;

    // Start building the query
    $this->db->select('COUNT(DISTINCT(p.id)) as `total`')
             ->from('products p')
             ->join('product_variants pv', 'pv.product_id = p.id', 'left'); // Use LEFT JOIN for products without variants

    // Where stock type is not null
    $this->db->where('p.stock_type IS NOT NULL');

    // Grouped conditions for low stock (either product or variant)
    $this->db->group_start()
             ->where('p.stock <=', $low_stock_limit)
             ->or_where('pv.stock <=', $low_stock_limit)
             ->group_end();

    // Grouped conditions for availability (either product or variant)
    $this->db->group_start()
             ->where('p.availability', '1')
             ->or_where('pv.availability', '1')
             ->group_end();

    // Branch filter, only applied if a branch_id is provided
    if (!empty($branch_id)) {
        $this->db->where('p.branch_id', $branch_id);
    }

    // Execute the query and return the result
    $product_count = $this->db->get()->result_array();

    // Check if result is found, otherwise return 0
    return isset($product_count[0]['total']) ? $product_count[0]['total'] : 0;
}


    public function count_products_availability_status($branch_id = "")
    {
        $count_res = $this->db->select(' COUNT( distinct(p.id)) as `total` ')->join('product_variants', 'product_variants.product_id = p.id');
        $where = "p.stock_type is  NOT NULL";
        if (!empty($branch_id) && $branch_id != '') {
            $count_res->where('p.branch_id  =', $branch_id);
        }
        $count_res->where($where);
        $count_res->where('p.stock ', '0');
        $count_res->where('p.availability ', '0');
        $count_res->or_where('product_variants.stock ', '0');
        $count_res->where('product_variants.availability', '0');
        $product_count = $count_res->get('products p')->result_array();
            // print_r($this->db->last_query());
            // die;
        return $product_count[0]['total'];
    }





    public function total_earnings($type = "overall")
    {
        // $select = "";
        // if ($type == "overall") {
           
        //     $select = "SUM(final_total) as total ";
        // }
        // $count_res = $this->db->select($select);
        // $where = ['active_status' => 'delivered'];
        // $count_res->where($where);

        // $product_count = $count_res->get('orders')->result_array();

        
       
        // return $product_count[0]['total'];

        // ========================================================================
        
        $select = "";
        if ($type == "overall") {
        
            $select = "SUM(final_total) as total";
           
        }
        $where = ['active_status' => 'delivered'];

        $count_res = $this->db->select($select);
        $count_res->where($where);

        $product_count = $count_res->get('orders')->result_array();
        
        $overall_branch_order_total = $product_count[0]['total'];

        $count_res_order = $this->db->select("SUM(promo_discount) as promo_discount");
        $count_res_order->where($where);

        $product_count_orders = $count_res_order->get('orders')->result_array();
        
        $overall_branch_promo_discount_total = $product_count_orders[0]['promo_discount'];

        $this->db->select('SUM(t.amount) as total_amount');
        $this->db->from('transactions t');
        $this->db->join('orders o', 't.order_id = o.id');  // Join using order_id
        $this->db->join('users u', 'o.rider_id = u.id');  // Connect to riders through orders
        $this->db->join('users_groups ug', 'u.id = ug.user_id');  // Connect to user groups
        // $this->db->where('o.branch_id', $_SESSION['branch_id']);
        $this->db->where('o.active_status', "delivered");
        $this->db->where('t.transaction_type', "wallet");
        $this->db->where('t.type', "credit");
        $this->db->where('ug.group_id', 3);  // Group ID 3 identifies riders

        $result = $this->db->get()->row_array();
        $overall_rider_commission = $result['total_amount'];

       
        // return $overall_branch_order_total - $overall_branch_promo_discount_total - $overall_rider_commission;
        return $overall_branch_order_total - $overall_rider_commission;

    }

    public function branch_earnings($type = "branch")
    {
        $select = "";
        if ($type == "branch") {
        
            $select = "SUM(final_total) as total";
           
        }
        $where = ['active_status' => 'delivered', 'branch_id' => $_SESSION['branch_id']];

        $count_res = $this->db->select($select);
        $count_res->where($where);

        $product_count = $count_res->get('orders')->result_array();
        
        $overall_branch_order_total = $product_count[0]['total'];

        $count_res_order = $this->db->select("SUM(promo_discount) as promo_discount");
        $count_res_order->where($where);

        $product_count_orders = $count_res_order->get('orders')->result_array();
        
        $overall_branch_promo_discount_total = $product_count_orders[0]['promo_discount'];

        $this->db->select('SUM(t.amount) as total_amount');
        $this->db->from('transactions t');
        $this->db->join('orders o', 't.order_id = o.id');  // Join using order_id
        $this->db->join('users u', 'o.rider_id = u.id');  // Connect to riders through orders
        $this->db->join('users_groups ug', 'u.id = ug.user_id');  // Connect to user groups
        $this->db->where('o.branch_id', $_SESSION['branch_id']);
        $this->db->where('o.active_status', "delivered");
        $this->db->where('t.transaction_type', "wallet");
        $this->db->where('t.type', "credit");
        $this->db->where('ug.group_id', 3);  // Group ID 3 identifies riders

        $result = $this->db->get()->row_array();
        $overall_rider_commission = $result['total_amount'];

       
        // return $overall_branch_order_total - $overall_branch_promo_discount_total - $overall_rider_commission;
        return $overall_branch_order_total - $overall_rider_commission;
    }

    // =========================================================
    // without minuse promo dicount 
    //     public function branch_earnings($type = "branch")
    // {
    //     if ($type != "branch") {
    //         return 0;
    //     }

    //     $branch_id = $_SESSION['branch_id'];

    //     // Step 1: Get total final_total for delivered orders of this branch
    //     $this->db->select_sum('final_total');
    //     $this->db->where(['active_status' => 'delivered', 'branch_id' => $branch_id]);
    //     $order_total_result = $this->db->get('orders')->row_array();
    //     $total_earning = isset($order_total_result['final_total']) ? $order_total_result['final_total'] : 0;

    //     // Step 2: Get order IDs that are delivered and have a rider_id
    //     $this->db->select('id');
    //     $this->db->where([
    //         'active_status' => 'delivered',
    //         'branch_id' => $branch_id,
    //     ]);
    //     $this->db->where("rider_id IS NOT NULL", null, false);
    //     $delivered_orders = $this->db->get('orders')->result_array();

    //     $order_ids = array_column($delivered_orders, 'id');
    //     $rider_commission = 0;

    //     if (!empty($order_ids)) {
    //         // Step 3: Join transactions with users_group where group_id = 3 (rider)
    //         $this->db->select('SUM(t.amount) as rider_commission');
    //         $this->db->from('transactions t');
    //         $this->db->join('users_groups ug', 't.user_id = ug.user_id');
    //         $this->db->where_in('t.order_id', $order_ids);
    //         $this->db->where('ug.group_id', 3);
    //         $rider_result = $this->db->get()->row_array();
    //         $rider_commission = isset($rider_result['rider_commission']) ? $rider_result['rider_commission'] : 0;
    //     }

    //     // Final net earnings
    //     $net_earning = $total_earning - $rider_commission;

    //     return $net_earning;
    // }

    // ==================================================
    // with minuse promo diccount

    // public function branch_earnings($type = "branch")
    // {
    //     if ($type != "branch") {
    //         return 0;
    //     }

    //     $branch_id = $_SESSION['branch_id'];

    //     // Step 1: Get total final_total for delivered orders of this branch
    //     $this->db->select_sum('final_total');
    //     $this->db->where(['active_status' => 'delivered', 'branch_id' => $branch_id]);
    //     $order_total_result = $this->db->get('orders')->row_array();
    //     $total_earning = isset($order_total_result['final_total']) ? $order_total_result['final_total'] : 0;
    //     // print_r($total_earning);
    //     // die;
    //     // Step 2: Get order IDs that are delivered and have a rider_id
    //     $this->db->select('id');
    //     $this->db->where([
    //         'active_status' => 'delivered',
    //         'branch_id' => $branch_id,
    //     ]);
    //     $this->db->where("rider_id IS NOT NULL", null, false);
    //     $delivered_orders = $this->db->get('orders')->result_array();

    //     $order_ids = array_column($delivered_orders, 'id');
    //     $rider_commission = 0;

    //     if (!empty($order_ids)) {
    //         // Step 3: Join transactions with users_groups where group_id = 3 (riders)
    //         $this->db->select('SUM(t.amount) as rider_commission');
    //         $this->db->from('transactions t');
    //         $this->db->join('users_groups ug', 't.user_id = ug.user_id');
    //         $this->db->where_in('t.order_id', $order_ids);
    //         $this->db->where('ug.group_id', 3);
    //         $rider_result = $this->db->get()->row_array();
    //         // echo "<pre>";
    //         // print_r($rider_result);
    //         $rider_commission = isset($rider_result['rider_commission']) ? $rider_result['rider_commission'] : 0;
    //     }

    //     // Step 4: Get promocode discount for orders of this branch
    //     $this->db->select_sum('promo_discount');
    //     $this->db->where([
    //         'branch_id' => $branch_id,
    //         'is_credited' => 1,
    //         'active_status' => 'delivered'
    //     ]);
    //     $promo_result = $this->db->get('orders')->row_array();
    //     $promocode_discount_total = isset($promo_result['promo_discount']) ? $promo_result['promo_discount'] : 0;

    //     // Final calculation
    //     // echo "<pre>";
    //     // print_r($rider_commission);
    //     $net_earning = $total_earning - $rider_commission - $promocode_discount_total;

    //     return $net_earning;
    // }

    // ====================================================
    


    public function getTopEarningBranch()
    {
        $this->db->select('branch.branch_name AS branch_name, SUM(orders.final_total) AS total_earnings');
        $this->db->from('orders');
        $this->db->join('branch', 'orders.branch_id = branch.id', 'inner');
        $this->db->where('orders.active_status', 'delivered'); // Only include delivered orders
        $this->db->group_by('orders.branch_id');
        $this->db->order_by('total_earnings', 'DESC');
        $this->db->limit(1); // Only get the top earning branch
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $top_branch = $query->result_array(); // Returns the top earning branch
            
            return $top_branch[0]['branch_name'];
        }

        return null; // No data available
    }


}

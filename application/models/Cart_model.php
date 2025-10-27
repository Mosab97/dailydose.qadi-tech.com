<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Cart_model extends CI_Model
{


    function add_to_cart($data, $check_status = TRUE)
    {
        $data = escape_array($data);

        $product_variant_id = explode(',', $data['product_variant_id']);
        $branch_id = explode(',', $data['branch_id']);
        $qty = explode(',', $data['qty']);
        $add_on_id = (isset($data['add_on_id']) && !empty($data['add_on_id'])) ? explode(',', $data['add_on_id']) : NULL;
        $add_on_qty = (isset($data['add_on_qty']) && !empty($data['add_on_qty'])) ? explode(',', $data['add_on_qty']) : 1;

        if ($check_status == TRUE) {
            $check_current_stock_status = validate_stock($product_variant_id, $qty);
            if (!empty($check_current_stock_status) && $check_current_stock_status['error'] == true) {
                // $check_current_stock_status['csrfName'] = $this->security->get_csrf_token_name();
                // $check_current_stock_status['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($check_current_stock_status));
                return true;
            }
        }
        $product_id = 0;

        for ($i = 0; $i < count($product_variant_id); $i++) {
            if (isset($data['add_on_id']) && !empty($data['add_on_id'])) {
                $addon_variant_combination = $product_variant_id[$i] . ',' . $data['add_on_id'];
            }
            $cart_data = [
                'user_id' => $data['user_id'],
                'product_variant_id' => $product_variant_id[$i],
                'addon_ids' => isset($data['add_on_id']) ? $data['add_on_id'] : "",
                'addon_variant_combination' => isset($addon_variant_combination) && !empty($addon_variant_combination) ? $addon_variant_combination : "",
                'branch_id' => $branch_id[0],
                'qty' => $qty[$i],
                'is_saved_for_later' => (isset($data['is_saved_for_later']) && !empty($data['is_saved_for_later']) && $data['is_saved_for_later'] == '1') ? $data['is_saved_for_later'] : '0',
            ];

            if (isset($add_on_id) && !empty($add_on_id)) {
                $product_id = fetch_details(['id' => $product_variant_id[$i]], "product_variants", 'product_id');
                /** set data for product add ons */
                $j = 0;
                foreach ($add_on_id as $row) {
                    $tempRow['user_id'] = $data['user_id'];
                    $tempRow['product_id'] = $product_id[0]['product_id'];
                    $tempRow['product_variant_id'] = $product_variant_id[$i];
                    $tempRow['add_on_id'] = $row;
                    $tempRow['addon_variant_combination'] = $addon_variant_combination;
                    $tempRow['qty'] = $add_on_qty[$j];
                    $add_on_data[] = $tempRow;
                    $j++;
                }
            }

            if ($qty[$i] == 0) {
                if (isset($add_on_id) && !empty($add_on_id)) {
                    $cart_data['product_id'] = $product_id[0]['product_id'];
                }
                $this->remove_from_cart($cart_data);
            } else {

                $cart_details = fetch_details(['user_id' => $data['user_id'], 'product_variant_id' => $product_variant_id[$i], 'addon_ids' => $data['add_on_id']], 'cart', '*');
                if (!empty($cart_details)) {
                    if (isset($_POST['cart_id'])) {
                        $fetch_cart_details = fetch_details(['id' => $_POST['cart_id']], 'cart', '*');
                        if ($addon_variant_combination == $cart_details[0]['addon_variant_combination']) {


                            $this->db->set($cart_data)->where(['user_id' => $data['user_id'], 'product_variant_id' => $product_variant_id[$i], 'addon_ids' => $data['add_on_id']])->update('cart');
                        } else {

                            $this->db->set($cart_data)->where(['user_id' => $data['user_id'], 'product_variant_id' => $product_variant_id[$i], 'addon_ids' => $data['add_on_id']])->update('cart');
                        }
                    } else {

                        $this->db->set($cart_data)->where(['user_id' => $data['user_id'], 'product_variant_id' => $product_variant_id[$i], 'addon_ids' => $data['add_on_id']])->update('cart');
                    }
                } else {
                    if (isset($_POST['cart_id'])) {

                        $this->db->set($cart_data)->where(['id' => $_POST['cart_id']])->update('cart');
                        delete_details(['cart_id' => $_POST['cart_id']], 'cart_add_ons');
                    } else {

                        $this->db->insert('cart', $cart_data);
                        $cart_id = $this->db->insert_id();
                    }
                }
                if (isset($add_on_id) && !empty($add_on_id)) {
                    /** update or add add_ons */

                    if ($this->db->select('id')->where(['user_id' => $data['user_id'], 'product_variant_id' => $product_variant_id[$i], 'addon_variant_combination' => $addon_variant_combination])->get('cart_add_ons')->num_rows() > 0) {

                        $test = fetch_details(['user_id' => $data['user_id'], 'product_variant_id' => $product_variant_id[$i], 'addon_variant_combination' => $addon_variant_combination], 'cart_add_ons', '*');

                        $cart_id = $test[0]['cart_id'];

                        delete_details(['product_variant_id' => $product_variant_id[$i], 'user_id' => $data['user_id'], 'addon_variant_combination' => $addon_variant_combination], 'cart_add_ons');
                    }

                    $addons_data = [];
                    foreach ($add_on_data as $addons_data) {

                        $test5 = fetch_details(['addon_variant_combination' => $addon_variant_combination], 'cart', '*');

                        if (!empty($test5)) {
                            $cart_id = $test5[0]['id'];
                        } else {
                            $cart_id = $data['cart_id'];
                        }

                        $addons_data['cart_id'] = $cart_id;
                        $this->db->insert('cart_add_ons', $addons_data);
                    }
                } else {

                    if ($this->db->select('id')->where(['user_id' => $data['user_id'], 'product_variant_id' => $product_variant_id[$i], 'addon_variant_combination' => $addon_variant_combination])->get('cart_add_ons')->num_rows() > 0) {

                        delete_details(['product_variant_id' => $product_variant_id[$i], 'user_id' => $data['user_id'], 'addon_variant_combination' => $addon_variant_combination], 'cart_add_ons');
                    }
                }
            }
        }
        return false;
    }

    function remove_from_cart($data)
    {
        if (isset($data['user_id']) && !empty($data['user_id'])) {
            $this->db->where('user_id', $data['user_id']);
            $this->db->where('branch_id', $data['branch_id']);
            if (isset($data['cart_id'])) {
                $cart_id = explode(',', $data['cart_id']);
                $this->db->where_in('id', $cart_id);
            }
            $this->db->delete('cart');
            if (isset($data['cart_id'])) {
                $cart_id = explode(',', $data['cart_id']);
                delete_details(['user_id' => $data['user_id']], "cart_add_ons", "cart_id", $cart_id);
            }
            if (isset($data['user_id']) && !empty($data['user_id']) && !isset($data['cart_id'])) {
                delete_details(['user_id' => $data['user_id']], "cart_add_ons");
            }
            return true;
        } else {
            return false;
        }
    }

    // function get_user_cart($user_id, $is_saved_for_later = 0, $product_variant_id = '', $branch_id = NULL)
    // {

    //     $q = $this->db->join('product_variants pv', 'pv.id=c.product_variant_id')
    //         ->join('products p', 'p.id=pv.product_id')

    //         ->where(['c.user_id' => $user_id, 'c.branch_id' => $branch_id, 'p.status' => '1', 'pv.status' => 1, 'qty !=' => '0', 'is_saved_for_later' => $is_saved_for_later])
    //         ->where('(pv.availability IS NULL OR pv.availability = 1)');
    //     if (!empty($product_variant_id)) {
    //         $q->where('c.product_variant_id', $product_variant_id);
    //     }
    //     $res = $q->select('c.*, c.id as cart_id,p.is_prices_inclusive_tax,p.name,p.tax as tax_id,p.id,p.image,p.short_description,p.minimum_order_quantity,p.total_allowed_quantity,pv.price,pv.special_price,pv.id as product_variant_id, pv.availability')->order_by('c.id', 'DESC')->get('cart c')->result_array();

    //     if (!empty($res)) {
    //         $res = array_map(function ($d) {
    //             $default_tax = get_settings('system_settings', true);
    //             $tax_percentage = fetch_details(['id' => $d['tax_id']], 'taxes', 'percentage')[0]['percentage'];
    //             $d['tax_percentage'] = $tax_percentage;

    //             $percentage = (isset($d['tax_percentage']) && intval($d['tax_percentage']) > 0 && $d['tax_percentage'] != null) ? $d['tax_percentage'] : '0';

    //             if ((isset($d['is_prices_inclusive_tax']) && $d['is_prices_inclusive_tax'] == 0) || (!isset($d['is_prices_inclusive_tax'])) && $percentage > 0) {
    //                 $price_tax_amount = $d['price'] * ($percentage / 100);
    //                 $special_price_tax_amount = $d['special_price'] * ($percentage / 100);
    //             } else {
    //                 $price_tax_amount = 0;

    //                 $special_price_tax_amount = 0;
    //             }
    //             if ($default_tax['default_taxation_system'] == '1') {

    //                 $d['price'] = $d['price'];
    //                 $d['special_price'] = $d['special_price'];
    //             } else {

    //                 $d['price'] = $d['price'] + $price_tax_amount;
    //                 $d['special_price'] = $d['special_price'] + $special_price_tax_amount;
    //             }

    //             // $d['price'] = $d['price'];
    //             // $d['special_price'] = $d['special_price'];
    //             $d['default_taxation_system'] = $default_tax['default_taxation_system'];
    //             $d['minimum_order_quantity'] = isset($d['minimum_order_quantity']) && !empty($d['minimum_order_quantity']) ? $d['minimum_order_quantity'] : 1;
    //             $d['image'] = isset($d['image']) && !empty($d['image']) ? base_url() . $d['image'] : "";
    //             $d['total_allowed_quantity'] = isset($d['total_allowed_quantity']) && !empty($d['total_allowed_quantity']) ? $d['total_allowed_quantity'] : '';
    //             $d['product_variants'] = get_variants_values_by_id($d['product_variant_id']);
    //             return $d;
    //         }, $res);
    //     }

    //     return $res;
    // }

    function get_user_cart($user_id, $is_saved_for_later = 0, $product_variant_id = '', $branch_id = NULL)
    {

        $q = $this->db->join('product_variants pv', 'pv.id=c.product_variant_id')
            ->join('products p', 'p.id=pv.product_id')

            ->where(['c.user_id' => $user_id, 'c.branch_id' => $branch_id, 'p.status' => '1', 'pv.status' => 1, 'qty !=' => '0', 'is_saved_for_later' => $is_saved_for_later])
            ->where('(pv.availability IS NULL OR pv.availability = 1)');
        if (!empty($product_variant_id)) {
            $q->where('c.product_variant_id', $product_variant_id);
        }
        $res = $q->select('c.*, c.id as cart_id,p.is_prices_inclusive_tax,p.name,p.tax as tax_id,p.id,p.image,p.short_description,p.minimum_order_quantity,p.total_allowed_quantity,pv.price,pv.special_price,pv.id as product_variant_id, pv.availability')->order_by('c.id', 'DESC')->get('cart c')->result_array();

        if (!empty($res)) {

            $res = array_map(function ($d) {
                $tax_percentage = fetch_details(['id' => $d['tax_id']], 'taxes', 'percentage')[0]['percentage'];
                $d['tax_percentage'] = $tax_percentage;

                $percentage = (isset($d['tax_percentage']) && intval($d['tax_percentage']) > 0 && $d['tax_percentage'] != null) ? $d['tax_percentage'] : '0';

                if ((isset($d['is_prices_inclusive_tax']) && $d['is_prices_inclusive_tax'] == 0) || (!isset($d['is_prices_inclusive_tax'])) && $percentage > 0) {
                    $price_tax_amount = $d['price'] * ($percentage / 100);
                    $special_price_tax_amount = $d['special_price'] * ($percentage / 100);
                } else {
                    $price_tax_amount = 0;

                    $special_price_tax_amount = 0;
                }

                $d['price'] = $d['price'];
                $d['special_price'] = $d['special_price'];
                $d['minimum_order_quantity'] = isset($d['minimum_order_quantity']) && !empty($d['minimum_order_quantity']) ? $d['minimum_order_quantity'] : 1;
                $d['image'] = isset($d['image']) && !empty($d['image']) ? base_url() . $d['image'] : "";
                $d['total_allowed_quantity'] = isset($d['total_allowed_quantity']) && !empty($d['total_allowed_quantity']) ? $d['total_allowed_quantity'] : '';
                $d['product_variants'] = get_variants_values_by_id($d['product_variant_id']);
                return $d;
            }, $res);
        }

        return $res;
    }

    function re_order_cart($data, $check_status = TRUE)
    {
        $data = escape_array($data);
        $order_detail = fetch_orders($data['order_id']);


        $productVariantIds = [];
        $productquantities = [];
        $branchId = [];
        $addonIds = [];
        $addonquantities = [];

        foreach ($order_detail['order_data'] as $order) {
            foreach ($order['order_items'] as $item) {
                $productVariantIds[] = $item['product_variant_id'];
                $productquantities[] = $item['quantity'];
                $branchId[] = $item['branch_id'];
                if (isset($item['add_ons']) && is_array($item['add_ons'])) {
                    // Loop through each add_on in the add_ons array
                    foreach ($item['add_ons'] as $add_on) {
                        // Check if the add_on is an object of type stdClass
                        if (is_object($add_on)) {
                            // Check if the add_on object has an id property
                            if (property_exists($add_on, 'id')) {
                                // Add the id and qty to the array
                                $addonIds[] = $add_on->id;
                                $addonquantities[] = $add_on->qty;
                            }
                        }
                    }
                }
            }
        }

        $variant_id = isset($productVariantIds) ? implode(",", $productVariantIds) : "";
        $branch_ids = isset($branchId) ? implode(",", $branchId) : "";
        $prduct_qtys = isset($productquantities) ? implode(",", $productquantities) : "";
        $add_ons_id = isset($addonIds) ? implode(",", $addonIds) : "";
        $add_ons_qty = isset($addonquantities) ? implode(",", $addonquantities) : "";

        $product_variant_id = isset($variant_id) ? explode(',', $variant_id) : "";
        $qty = isset($prduct_qtys) ? explode(',', $prduct_qtys) : "";
        $branch_ids = isset($branch_ids) ? explode(',', $branch_ids) : "";
        $add_on_id = (isset($add_ons_id) && !empty($add_ons_id)) ? explode(',', $add_ons_id) : NULL;
        $add_on_qty = (isset($add_ons_qty) && !empty($add_ons_qty)) ? explode(',', $add_ons_qty) : 1;

        if ($check_status == TRUE) {
            $check_current_stock_status = validate_stock($product_variant_id, $qty);
            if (!empty($check_current_stock_status) && $check_current_stock_status['error'] == true) {
                $check_current_stock_status['csrfName'] = $this->security->get_csrf_token_name();
                $check_current_stock_status['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($check_current_stock_status));
                return true;
            }
        }
        $product_id = 0;

        for ($i = 0; $i < count($product_variant_id); $i++) {
            $cart_data = [
                'user_id' => $order_detail['order_data'][0]['user_id'],
                'product_variant_id' => $product_variant_id[$i],
                'qty' => $qty[$i],
                'branch_id' => $branch_ids[$i],
                'is_saved_for_later' => (isset($data['is_saved_for_later']) && !empty($data['is_saved_for_later']) && $data['is_saved_for_later'] == '1') ? $data['is_saved_for_later'] : '0',
            ];
            if (isset($add_on_id) && !empty($add_on_id)) {
                $product_id = fetch_details(['id' => $product_variant_id[$i]], "product_variants", 'product_id');
                /** set data for product add ons */
                $j = 0;
                foreach ($add_on_id as $row) {
                    $tempRow['user_id'] = $order_detail['order_data'][0]['user_id'];
                    $tempRow['product_id'] = $product_id[0]['product_id'];
                    $tempRow['product_variant_id'] = $product_variant_id[$i];
                    $tempRow['add_on_id'] = $row;
                    $tempRow['qty'] = $add_on_qty[$j];
                    $add_on_data[] = $tempRow;
                    $j++;
                }
            }

            if ($qty[$i] == 0) {
                if (isset($add_on_id) && !empty($add_on_id)) {
                    $cart_data['product_id'] = $product_id[0]['product_id'];
                }
                $this->remove_from_cart($cart_data);
            } else {
                if ($this->db->select('id')->where(['user_id' => $data['user_id'], 'product_variant_id' => $product_variant_id[$i]])->get('cart')->num_rows() > 0) {
                    $this->db->set($cart_data)->where(['user_id' => $data['user_id'], 'product_variant_id' => $product_variant_id[$i]])->update('cart');
                } else {
                    $this->db->insert('cart', $cart_data);
                }
                if (isset($add_on_id) && !empty($add_on_id)) {
                    /** update or add add_ons */
                    if ($this->db->select('id')->where(['user_id' => $data['user_id'], 'product_variant_id' => $product_variant_id[$i]])->where_in("add_on_id", $add_on_id)->get('cart_add_ons')->num_rows() > 0) {
                        delete_details(['product_variant_id' => $product_variant_id[$i], 'user_id' => $data['user_id']], 'cart_add_ons');
                    }
                    $this->db->insert_batch('cart_add_ons', $add_on_data);
                } else {
                    if ($this->db->select('id')->where(['user_id' => $data['user_id'], 'product_variant_id' => $product_variant_id[$i]])->get('cart_add_ons')->num_rows() > 0) {
                        delete_details(['product_variant_id' => $product_variant_id[$i], 'user_id' => $data['user_id']], 'cart_add_ons');
                    }
                }
            }
        }
        return false;
    }

    function cart_notification()
    {

        $this->db->distinct();
        $this->db->select('cart.user_id, users.fcm_id,users.web_fcm_id');
        $this->db->from('cart');
        $this->db->join('users', 'cart.user_id = users.id');
        $this->db->where('TIMESTAMPDIFF(HOUR, date_created, NOW()) >', 24);
        $query = $this->db->get();
        $result = $query->result_array();

        foreach ($result as $fcm_id) {


            if (!empty($result[0]['fcm_id'])) {
                // Step 1: Group by platform
                $groupedByPlatform = [];
                foreach ($result as $item) {
                    $platform = $item['platform'];
                    $groupedByPlatform[$platform][] = $item['fcm_id'];
                    $groupedByPlatform['web'][] = $item['web_fcm_id'];
                }
                // Step 2: Chunk each platform group into arrays of 1000
                $fcm_ids = [];
                foreach ($groupedByPlatform as $platform => $fcmIds) {
                    $fcm_ids[$platform] = array_chunk($fcmIds, 1000);
                }
            }
        }
        $registrationIDs = $fcm_ids;
        $fcmMsg = array(
            'title' => "🔔 Items in Your Cart Are Waiting",
            'body' => "Don't miss out! Finish your purchase before your favorites are gone😋",
            'type' => "cart_notification",
        );

        // $registrationIDs_chunks = array_chunk($registrationIDs, 1000);
        $fcmFields = send_notification($fcmMsg, $registrationIDs, $fcmMsg, $fcmMsg['title'], $fcmMsg['body'], $fcmMsg['type']);
    }
}

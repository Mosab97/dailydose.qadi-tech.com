<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cron_job extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation', 'upload']);
        $this->load->helper(['url', 'language', 'file']);
        $this->load->model(['Partner_model', 'Order_model', 'Cart_model']);
    }
    public function settle_admin_commission()
    {
        return $this->Partner_model->settle_admin_commission();
    }
    public function settle_payment()
    {

        $currency = get_settings('currency');


        $row = $this->db->select('*')->where('payment_method', 'PayPal')->or_where('payment_method', 'midtrans')->get('orders')->result_array();

        foreach ($row as $order_details) {


            $transaction = $this->db->select('*')->where('order_id', $order_details['id'])->get('transactions')->result_array();
            if (empty($transaction)) {
                $user_res = fetch_details(['id' => $order_details['user_id']], 'users', 'fcm_id,web_fcm_id');

                $fcm_ids = array();
                if (!empty($user_res[0]['fcm_id'])) {
                    $fcm_ids[0][] = $user_res[0]['fcm_id'];
                }
                if (!empty($user_res[0]['web_fcm_id'])) {
                    $fcm_ids[0][] = $user_res[0]['web_fcm_id'];
                }
                $wallet_balance = $order_details['wallet_balance'];
                $user_id = $order_details['user_id'];
                if ($wallet_balance != 0) {
                    /* update user's wallet */
                    $returnable_amount = $wallet_balance;
                  
                    $fcmMsg = array(
                        'title' => "Amount Credited To Wallet",
                        'body' => $currency . ' ' . $returnable_amount,
                        'type' => "wallet"
                    );
                    send_notification($fcmMsg, $fcm_ids);
                    $re =  update_wallet_balance('credit', $user_id, $returnable_amount, 'Wallet Amount Credited for Order Item ID  : ' . $order_details['id']);

                    delete_details(['id' => $order_details['id']], 'orders');
                    delete_details(['order_id' => $order_details['id']], 'order_items');
                }
            }
        }
    }

    public function draft_order_settel()
    {
        return $this->Order_model->delete_draft_orders();
    }

    public function cart_notification()
    {
        return $this->Cart_model->cart_notification();
    }
}

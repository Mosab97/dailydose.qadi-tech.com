<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Point_of_sale_model extends CI_Model
{
    function get_users($search_term = "")
    {
        // Fetch users

        $this->db->select('*');
        $this->db->where("active", 1);
        $this->db->group_start(); // Start grouping
        $this->db->like("username", $search_term);
        $this->db->or_like("mobile",
            $search_term
        );
        $this->db->or_like("email", $search_term);
        $this->db->group_end(); // End grouping
        $fetched_records = $this->db->get('users');
        $users = $fetched_records->result_array();




        // Initialize Array with fetched data
        $data = array();
        foreach ($users as $user) {
            

            $data[] = array("id" => $user['id'], "text" => $user['username'] . " | " . $user['mobile'] . " | " . $user['email'], "number" => $user['mobile'], "email" => $user['email'], "name" => $user['username']);
        }
        return $data;
    }
}

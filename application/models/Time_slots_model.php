<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Time_slots_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }



    public function add_time_slots($data)
    {
        $timeslots = fetch_details(['branch_id' => $data['branch_id']], 'time_slots');
        if(empty($timeslots)){
            // print_r($data);
            // die;
            // $this->db->insert('ticket_types', $data)
            return $this->db->insert('time_slots', $data);
        }else{
            print_r("else");
            die;
            $this->db->where('id', $timeslots[0]['id']);
            return $this->db->update('time_slots', $data);

        }
    }

    public function update_time_slot($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('time_slots', $data);
    }

    public function delete_time_slots($ids)
    {
        $this->db->where_in('id', $ids);
        return $this->db->delete('time_slots');
    }

    public function get_time_slots_by_branch($branch_id)
    {
        $this->db->where('branch_id', $branch_id);
        $query = $this->db->get('time_slots');
        return $query->result_array();
    }
}

<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Faq_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    function add_faq($data)
    {
        $data = escape_array($data);
        $faq_data = [
            'question' => $data['question'],
            'answer' => $data['answer']
        ];
        if (isset($data['edit_faq']) && !empty($data['edit_faq'])) {
            $this->db->set($faq_data)->where('id', $data['edit_faq'])->update('faqs');
            return $data['edit_faq'];
        } else {
            $this->db->insert('faqs', $faq_data);
            return $this->db->insert_id();
        }
    }

    function get_faqs($offset, $limit, $sort, $order)
    {
        $faqs_data = [];
        $count_res = $this->db->select(' COUNT(id) as `total` ')->where('status', '1')->get('faqs')->result_array();
        $search_res = $this->db->select(' * ')->where('status', '1')->order_by($sort, $order)->limit($limit, $offset)->get('faqs')->result_array();
        if (!empty($search_res)) {
            for ($i = 0; $i < count($search_res); $i++) {
                $search_res[$i] = output_escaping($search_res[$i]);
            }
        }
        $faqs_data['total'] = $count_res[0]['total'];
        $faqs_data['data'] = $search_res;
        return  $faqs_data;
    }
    public function get_faq_list($id = '', $search = '', $offset = '0', $limit = '10', $sort = 'id', $order = 'DESC')
    {
        $multipleWhere = '';
        $where = array();

        if (!empty($search)) {
            $multipleWhere = [
                'f.id' => $search,
                'f.question' => $search,
                'f.answer' => $search
            ];
        }

        if (!empty($id)) {
            $where['f.id'] = $id;
        }

        // count of total product faqs
        $this->db->select('COUNT(f.id) as total')->from('faqs f')->where_in('status', ['1', '2']);

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_start();
            $this->db->or_like($multipleWhere);
            $this->db->group_end();
        }

        if (isset($where) && !empty($where)) {
            $this->db->where($where);
        }

        $count_res = $this->db->get()->row_array();
        $total = $count_res['total'];

        // get product faqs data 
        $this->db->select('*')->from('faqs f')->where_in('status', ['1', '2']);

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_start();
            $this->db->or_like($multipleWhere);
            $this->db->group_end();
        }

        if (isset($where) && !empty($where)) {
            $this->db->where($where);
        }

        $this->db->order_by($sort, $order)->limit($limit, $offset);

        $faq_search_res = $this->db->get()->result_array();

        // Remove \r\n from the answer field
        foreach ($faq_search_res as $faq) {
          
            $faq['answer'] = output_escaping(str_replace('\r\n', '', $faq['answer']));
        }

        return array(
            'error' => empty($faq_search_res) ? true : false,
            'message' => empty($faq_search_res) ? 'FAQs does not exist' : 'FAQs retrieved successfully',
            'total' => $total,
            'data' => $faq_search_res
        );
    }

}
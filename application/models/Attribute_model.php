<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Attribute_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    public function add_attributes($data)
    {
      
        if (!isset($data['edit_attribute_id'])) {
            $attr_vals = json_decode($data['attribute_values'], true);
            $attr_vals = array_column($attr_vals, 'value');
        }
        $data = escape_array($data);
        // print_r($data);
        $rows = $tempRow = array();
        $attr_data = [
            'name' => $data['name'],
        ];

        if (isset($data['edit_attribute_id'])) {
            // print_R($data);die;
            if(isset($data['value_id']) && !empty($data['value_id']) && isset($data['value_name']) && !empty($data['value_name'])){

                $data['value_id'] = array_map('intval', $data['value_id']);
                $attribute_values = array_combine($data['value_id'], $data['value_name']);
                
                            foreach ($attribute_values as $key => $val) {
                               
                                $tempRow['attribute_id'] = $data['edit_attribute_id'];
                                $tempRow['value'] = $val;
                                $tempRow['status'] = 1;
                                /* check for duplicate entry */
                                if (is_exist(['attribute_id' => $data['edit_attribute_id'], 'value' => $val], 'attribute_values', $key)) {
                                    return true;
                                }
                                
                                if ($key === "" || $key === 0 || $key === "0") {
                
                                    $this->db->insert('attribute_values', $tempRow);
                                } else {
                                    $this->db->set($tempRow)->where('id', $key)->update('attribute_values');
                                   
                                }
                            }
                            $this->db->set($attr_data)->where('id', $data['edit_attribute_id'])->update('attributes');
            }
        } else {
            $this->db->insert('attributes', $attr_data);
            $insert_id = $this->db->insert_id();
            if (!empty($insert_id) && !empty($attr_vals)) {
                /* insert attribute values */
                foreach ($attr_vals as $row => $val) {
                    $tempRow['attribute_id'] = $insert_id;
                    $tempRow['value'] = $val;
                    $tempRow['status'] = 1;
                    $rows[] = $tempRow;
                }
                $this->db->insert_batch('attribute_values', $rows);
                // return true;
                return $insert_id;
            } else {
                return false;
            }
        }

        if (isset($data['edit_attribute_id'])) {
            if(isset($data['value_id']) && !empty($data['value_id']) && isset($data['value_name']) && !empty($data['value_name'])){
                $value_name = $data['value_name'];
                $value_id = $data['value_id'];
    
                $blankKeys = array_keys($value_id, null);
    
                foreach ($blankKeys as $blankKey) {
                    if (isset($value_name[$blankKey])) {
                        $tempRow = array(
                            'attribute_id' => $data['edit_attribute_id'],
                            'value' => $value_name[$blankKey], // Use the individual value here
                            'status' => 1
                        );
                        $this->db->insert('attribute_values', $tempRow);
                    }
                }
            }
        }
    }


    public function get_attribute_list(
        $offset = 0,
        $limit = 10,
        $sort = 'id',
        $order = 'DESC'
    ) {
        $multipleWhere = '';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "attr.id";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['attr.id' => $search, 'attr.name' => $search, 'av.value' => $search];
        }

        $where = ['av.status' => 1];

        $count_res = $this->db->select(' COUNT(DISTINCT attr.id) as `total` ')->join('attribute_values av', 'av.attribute_id = attr.id');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->or_like($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $attr_count = $count_res->get('attributes attr')->result_array();

        foreach ($attr_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' attr.* ,GROUP_CONCAT(av.value) as attribute_values,GROUP_CONCAT(av.id) as attribute_value_ids')->join('attribute_values av', 'av.attribute_id = attr.id');
        
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->or_like($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }
        $city_search_res = $search_res->group_by('attr.id')->order_by($sort, $order)->limit($limit, $offset)->get('attributes attr')->result_array();
        $bulkData = $rows = $tempRow = array();
        $bulkData['total'] = $total;
        foreach ($city_search_res as $row) {
            $row = output_escaping($row);
            $operate = ' <a href="javascript:void(0)" class="edit_attribute btn btn-success btn-xs mr-1 mb-1" title="Edit" data-id="' . $row['id'] . '" data-name="' . $row['name'] . '" data-attribute_values="' . $row['attribute_values'] . '" data-attribute_value_ids="' . $row['attribute_value_ids'] . '" data-toggle="modal" data-target="#attribute-modal"><i class="fa fa-pen"></i></a>';
            if ($row['status'] == '1') {
                $tempRow['status'] = '<a class="badge badge-success text-white" >Active</a>';
                $operate .= '<a class="btn btn-warning btn-xs update_active_status mr-1 mb-1" data-table="attributes" title="Deactivate" href="javascript:void(0)" data-id="' . $row['id'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-eye-slash"></i></a>';
            } else {
                $tempRow['status'] = '<a class="badge badge-danger text-white" >Inactive</a>';
                $operate .= '<a class="btn btn-primary mr-1 mb-1 btn-xs update_active_status" data-table="attributes" href="javascript:void(0)" title="Active" data-id="' . $row['id'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-eye"></i></a>';
            }
            $operate .= '<a class="delete-attibute-combination btn btn-danger btn-xs mr-1 mb-1" title="Delete" href="javascript:void(0)" data-id="' . $row['id'] . '" ><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row['id'];
            $tempRow['name'] = $row['name'];
            $tempRow['attribute_values'] = (isset($row['attribute_values']) && !empty($row['attribute_values'])) ? $row['attribute_values'] : "";
            $tempRow['attribute_value_ids'] = (isset($row['attribute_value_ids']) && !empty($row['attribute_value_ids'])) ? $row['attribute_value_ids'] : "";
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }




    function get_attributes($sort = "name", $order = "ASC", $search = "", $offset = "", $limit = NULL, $ignore_status = false,$id = NULL)
    {
        $multipleWhere = '';
        $where = array();
        if (!empty($search)) {
            $multipleWhere = [
                '`a.name`' => $search
            ];
        }

        $search_res = $this->db->select('*');
        if ($ignore_status == false) {
            $search_res->where("status=1");
        }

        if(isset($id) && !empty($id)){
            $search_res->where("id=".$id);
        }

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_start();
            $search_res->or_like($multipleWhere);
            $search_res->group_end();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }
        $attribute_set = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('attributes')->result_array();
        // print_r($this->db->last_query());
        // die;
        $bulkData = array();
        $bulkData['error'] = (empty($attribute_set)) ? true : false;
        $bulkData['total'] = (count($attribute_set));
        $bulkData['message'] = (empty($attribute_set)) ? 'Something went wrong' : 'Data retrived successfully';
        if (!empty($attribute_set)) {
            for ($i = 0; $i < count($attribute_set); $i++) {
                $attribute_set[$i] = output_escaping($attribute_set[$i]);
            }
        }
        $bulkData['data'] = (empty($attribute_set)) ? [] : $attribute_set;
        return $bulkData;
    }

    function get_attribute_value($sort = "av.id", $order = "ASC", $search = "", $attribute_id = "", $offset = NULL, $limit = NULL, $ignore_status = false)
    {
        $multipleWhere = '';
        $where = array();
        if (!empty($search)) {
            $multipleWhere = [
                '`a.name`' => $search,
                '`av.value`' => $search,
                '`av.swatche_value`' => $search
            ];
        }

        $search_res = $this->db->select('av.*,a.name as attribute_name')->join('attributes a', 'a.id=av.attribute_id');
        if ($ignore_status == false) {
            $search_res->where("av.status=1 and a.status=1");
        }
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_start();
            $search_res->or_like($multipleWhere);
            $search_res->group_end();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }
        if (isset($attribute_id) && !empty($attribute_id)) {
            $search_res->where('av.attribute_id = ' . $attribute_id);
        }
        $attribute_set = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('attribute_values av')->result_array();
        $bulkData = array();
        $bulkData['error'] = (empty($attribute_set)) ? true : false;
        $bulkData['total'] = (count($attribute_set));
        $bulkData['message'] = (empty($attribute_set)) ? 'Something went wrong' : 'Data retrived successfully';
        if (!empty($attribute_set)) {
            for ($i = 0; $i < count($attribute_set); $i++) {
                $attribute_set[$i] = output_escaping($attribute_set[$i]);
            }
        }
        $bulkData['data'] = (empty($attribute_set)) ? [] : $attribute_set;
        return $bulkData;
    }
}

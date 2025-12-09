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

                // Process values maintaining the form's index order
                $value_ids = isset($data['value_id']) ? $data['value_id'] : [];
                $value_names = isset($data['value_name']) ? $data['value_name'] : [];
                
                // Process each value in the order they appear in the form (by array index)
                foreach ($value_names as $form_index => $val) {
                    $value_id = isset($value_ids[$form_index]) ? intval($value_ids[$form_index]) : 0;
                    
                    $tempRow['attribute_id'] = $data['edit_attribute_id'];
                    $tempRow['value'] = $val;
                    $tempRow['status'] = 1;
                    
                    // Check if this is a new value (empty or 0 value_id)
                    if (empty($value_id) || $value_id === 0) {
                        // New value - insert it
                        /* check for duplicate entry */
                        if (is_exist(['attribute_id' => $data['edit_attribute_id'], 'value' => $val], 'attribute_values')) {
                            return true;
                        }
                        
                        $this->db->insert('attribute_values', $tempRow);
                        $new_value_id = $this->db->insert_id();
                        
                        // Get translations using form index (JavaScript sends with form index for new values)
                        $translations = isset($data['attribute_value_translations'][$form_index]) ? $data['attribute_value_translations'][$form_index] : [];
                        // Always include English from main value field
                        if (!isset($translations['en']) || empty($translations['en']['value'])) {
                            $translations['en'] = ['value' => $val];
                        }
                        // Save attribute value translations (only once)
                        if (!empty($translations)) {
                            $this->save_attribute_value_translations($new_value_id, $translations);
                        }
                    } else {
                        // Existing value - update it
                        /* check for duplicate entry */
                        if (is_exist(['attribute_id' => $data['edit_attribute_id'], 'value' => $val], 'attribute_values', $value_id)) {
                            return true;
                        }
                        
                        $this->db->set($tempRow)->where('id', $value_id)->update('attribute_values');
                        
                        // Get translations using value_id (JavaScript sends with value_id for existing values)
                        // But also check form index as fallback
                        $translations = [];
                        if (isset($data['attribute_value_translations'][$value_id])) {
                            $translations = $data['attribute_value_translations'][$value_id];
                        } elseif (isset($data['attribute_value_translations'][$form_index])) {
                            $translations = $data['attribute_value_translations'][$form_index];
                        }
                        // Always include English from main value field
                        if (!isset($translations['en']) || empty($translations['en']['value'])) {
                            $translations['en'] = ['value' => $val];
                        }
                        // Save attribute value translations (only once)
                        if (!empty($translations)) {
                            $this->save_attribute_value_translations($value_id, $translations);
                        }
                    }
                }
                            $this->db->set($attr_data)->where('id', $data['edit_attribute_id'])->update('attributes');
                            
                            // Save attribute translations if provided
                            if (isset($data['attribute_translations']) && !empty($data['attribute_translations'])) {
                                $this->save_attribute_translations($data['edit_attribute_id'], $data['attribute_translations']);
                            }
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
                
                // Save attribute translations if provided
                if (isset($data['attribute_translations']) && !empty($data['attribute_translations'])) {
                    $this->save_attribute_translations($insert_id, $data['attribute_translations']);
                }
                
                // Get inserted attribute value IDs and save translations
                $inserted_values = $this->db->where('attribute_id', $insert_id)->get('attribute_values')->result_array();
                if (!empty($inserted_values) && isset($data['attribute_value_translations'])) {
                    $value_index = 0;
                    foreach ($inserted_values as $inserted_value) {
                        if (isset($data['attribute_value_translations'][$value_index]) && !empty($data['attribute_value_translations'][$value_index])) {
                            $this->save_attribute_value_translations($inserted_value['id'], $data['attribute_value_translations'][$value_index]);
                        }
                        $value_index++;
                    }
                }
                
                // return true;
                return $insert_id;
            } else {
                // Save attribute translations even if no values
                if (isset($data['attribute_translations']) && !empty($data['attribute_translations'])) {
                    $this->save_attribute_translations($insert_id, $data['attribute_translations']);
                }
                return $insert_id;
            }
        }

        // Note: Blank keys (new values) are now handled in the main loop above
        // This section is removed to prevent duplicate inserts
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

    /**
     * Save attribute translations for a given attribute
     * @param int $attribute_id - Attribute ID
     * @param array $translations - Array of translations with language codes as keys
     *                              Example: ['en' => ['name' => '...'], 'ar' => ['name' => '...']]
     * @return bool
     */
    public function save_attribute_translations($attribute_id, $translations)
    {
        if (empty($attribute_id) || empty($translations)) {
            return false;
        }

        // Ensure translations is an array
        if (!is_array($translations)) {
            return false;
        }

        foreach ($translations as $language_code => $translation_data) {
            // Ensure translation_data is an array
            if (!is_array($translation_data)) {
                continue;
            }
            if (empty($translation_data['name'])) {
                continue; // Skip if name is empty
            }

            $data = [
                'attribute_id' => $attribute_id,
                'language_code' => $language_code,
                'name' => $translation_data['name'],
            ];

            // Check if translation already exists
            $existing = $this->db->where('attribute_id', $attribute_id)
                                 ->where('language_code', $language_code)
                                 ->get('attribute_translations')
                                 ->row_array();

            if ($existing) {
                // Update existing translation
                $this->db->where('id', $existing['id'])
                         ->update('attribute_translations', [
                             'name' => $data['name'],
                         ]);
            } else {
                // Insert new translation
                $this->db->insert('attribute_translations', $data);
            }
        }

        return true;
    }

    /**
     * Get attribute translations for a given attribute
     * @param int $attribute_id - Attribute ID
     * @param string|null $language_code - Specific language code to retrieve (optional)
     * @return array - Formatted array with language codes as keys
     */
    public function get_attribute_translations($attribute_id, $language_code = null)
    {
        if (empty($attribute_id)) {
            return [];
        }

        $this->db->where('attribute_id', $attribute_id);
        
        if (!empty($language_code)) {
            $this->db->where('language_code', $language_code);
        }

        $result = $this->db->get('attribute_translations')->result_array();

        // Format result as associative array with language code as key
        $formatted = [];
        foreach ($result as $row) {
            $formatted[$row['language_code']] = [
                'name' => $row['name'],
            ];
        }

        return $formatted;
    }

    /**
     * Save attribute value translations for a given attribute value
     * @param int $attribute_value_id - Attribute Value ID
     * @param array $translations - Array of translations with language codes as keys
     *                              Example: ['en' => ['value' => '...'], 'ar' => ['value' => '...']]
     * @return bool
     */
    public function save_attribute_value_translations($attribute_value_id, $translations)
    {
        if (empty($attribute_value_id) || empty($translations)) {
            return false;
        }

        // Ensure translations is an array
        if (!is_array($translations)) {
            return false;
        }

        foreach ($translations as $language_code => $translation_data) {
            // Ensure translation_data is an array
            if (!is_array($translation_data)) {
                continue;
            }
            if (empty($translation_data['value'])) {
                continue; // Skip if value is empty
            }

            $data = [
                'attribute_value_id' => $attribute_value_id,
                'language_code' => $language_code,
                'value' => $translation_data['value'],
            ];

            // Check if translation already exists
            $existing = $this->db->where('attribute_value_id', $attribute_value_id)
                                 ->where('language_code', $language_code)
                                 ->get('attribute_value_translations')
                                 ->row_array();

            if ($existing) {
                // Update existing translation
                $this->db->where('id', $existing['id'])
                         ->update('attribute_value_translations', [
                             'value' => $data['value'],
                         ]);
            } else {
                // Insert new translation
                $this->db->insert('attribute_value_translations', $data);
            }
        }

        return true;
    }

    /**
     * Get attribute value translations for a given attribute value
     * @param int $attribute_value_id - Attribute Value ID
     * @param string|null $language_code - Specific language code to retrieve (optional)
     * @return array - Formatted array with language codes as keys
     */
    public function get_attribute_value_translations($attribute_value_id, $language_code = null)
    {
        if (empty($attribute_value_id)) {
            return [];
        }

        $this->db->where('attribute_value_id', $attribute_value_id);
        
        if (!empty($language_code)) {
            $this->db->where('language_code', $language_code);
        }

        $result = $this->db->get('attribute_value_translations')->result_array();

        // Format result as associative array with language code as key
        $formatted = [];
        foreach ($result as $row) {
            $formatted[$row['language_code']] = [
                'value' => $row['value'],
            ];
        }

        return $formatted;
    }
}

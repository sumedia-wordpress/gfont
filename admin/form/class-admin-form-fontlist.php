<?php

class Sumedia_GFont_Admin_Form_Fontlist extends Sumedia_Base_Form
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $update_data_ids = [];

    public function load()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->table_name;
        $results = $wpdb->get_results("SELECT * FROM `" . $table_name . "`", ARRAY_A);
        foreach ($results as $row) {
            $this->data[$row['id']] = $row;
        }
    }

    public function get_data()
    {
        return $this->data;
    }

    public function do_request($request_data)
    {
        if (!isset($request_data['ids'])) {
            return;
        }

        foreach($request_data['ids'] as $id) {
            if (!isset($this->data[$id])) {
                continue;
            }

            if ($this->data[$id]['use_flag'] == 1 && !isset($request_data['use_flag'][$id])) {
                $this->data[$id]['use_flag'] = 0;
                $this->update_data_ids[] = $id;
            } elseif ($this->data[$id]['use_flag'] == 0 && isset($request_data['use_flag'][$id])) {
                $this->data[$id]['use_flag'] = 1;
                $this->update_data_ids[] = $id;
            }
        }
    }

    public function save()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->table_name;

        foreach ($this->update_data_ids as $id) {
            if (!isset($this->data[$id])) {
                continue;
            }

            $sql = "UPDATE `" . $table_name . "` SET 
                `use_flag` = %s 
                WHERE `id` = '%s'";
            $prepare = $wpdb->prepare($sql, $this->data[$id]['use_flag'], $id);
            $wpdb->query($prepare);
        }

        $this->update_data_ids = [];
    }

}
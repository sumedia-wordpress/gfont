<?php

class Sumedia_GFont_Fontlist_Table extends WP_List_Table
{
    var $_table_name = 'sumedia_gfont_fonts';

    function get_columns()
    {
        return array(
            'cb' => '<input type="checkbox" />',
            'id' => __('ID'),
            'fontfamily' => __('Font Family', 'sumedia-gfont'),
            'fontname' => __('Font Name', 'sumedia-gfont'),
            'use_flag' => __('Use this font', 'sumedia-gfont')
        );
    }

    function get_sortable_columns()
    {
        return array(
            'fontfamily' => array('fontfamily', true),
            'fontname' => array('fontname', false),
            'use_flag' => array('use_flag', false)
        );
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
            case 'fontfamily':
            case 'fontname':
            case 'use_flag':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function column_cb($item)
    {
        $checkbox = '<input type="hidden" name="ids[' . $item['id'] . ']" value="' . $item['id'] . '" />';
        $checkbox .= '<input type="checkbox" name="use_flag[' . $item['id'] . ']" 
            value="' . $item['id'] . '"';
        if ($item['use_flag'] == 1) {
            $checkbox .= ' checked="checked"';
        }
        $checkbox .= ' />';
        return $checkbox;
    }

    function column_use_flag($item)
    {
        if ($item['use_flag'] == 1) {
            return __('Yes', 'sumedia-gfont');
        } else {
            return __('No', 'sumedia-gfont');
        }
    }

    function get_bulk_actions()
    {
        return array(
            'use_flag' => __('Use this font', 'sumedia-gfont')
        );
    }

    function prepare_items()
    {
        global $wpdb, $_wp_column_headers;

        $screen = get_current_screen();

        $columns = $this->get_columns();
        $hidden = array('id');
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array(
            $columns,
            $hidden,
            $sortable
        );

        $this->process_bulk_action();

        $table_name = $wpdb->prefix . $this->_table_name;
        $query = "SELECT * FROM `" . $table_name . "`";
        $this->items = $wpdb->get_results($query, ARRAY_A);

        usort($this->items, function($a, $b){
            $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'fontfamily';
            $order = isset($_REQUEST['order']) && $_REQUEST['order'] == 'DESC' ? 'DESC' : 'ASC';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'ASC' ? $result : -$result);
        });

        $per_page = 25;
        $current_page = $this->get_pagenum();
        $total_items = count($this->items);
        $this->items = array_slice($this->items, (($current_page-1) * $per_page), $per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
    }

}
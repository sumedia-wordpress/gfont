<?php

namespace Sumedia\Gfont\Admin\Table;

class Fontlist extends \WP_List_Table
{
    var $_table_name = 'sumedia_gfont_fonts';

    function get_columns()
    {
        return array(
            'cb' => '<input type="checkbox" />',
            'id' => __('ID', SUMEDIA_GFONT_PLUGIN_NAME),
            'google_url' => __('Google URL', SUMEDIA_GFONT_PLUGIN_NAME),
            'fontname' => __('Font Name', SUMEDIA_GFONT_PLUGIN_NAME)
        );
    }

    function get_sortable_columns()
    {
        return array(
            'google_url' => array('google_url', true),
            'fontname' => array('fontname', false)
        );
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
            case 'google_url':
            case 'fontname':
                return $item[$column_name];
        }
    }

    function column_fontname($item)
    {
        return $item['fontname'] . '<br /><small>CSS: font-family:' . $item['fontname'] . ';<br />HTML Class: ' . $item['fontname'] . '</small>';
    }

    function column_cb($item)
    {
        $checkbox = '<input type="checkbox" name="ids[' . $item['id'] . ']" value="' . $item['id'] . '" />';
        return $checkbox;
    }

    function get_bulk_actions()
    {
        return array(
            'Delete' => __('Delete', SUMEDIA_GFONT_PLUGIN_NAME)
        );
    }

    function prepare_items()
    {
        global $wpdb;

        $columns = $this->get_columns();
        $hidden = array('id');
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array(
            $columns,
            $hidden,
            $sortable
        );

        $per_page = 20;
        $current_page = $this->get_pagenum();

        $table_name = $wpdb->prefix . $this->_table_name;
        $query = "SELECT COUNT(`id`) AS item_count FROM `" . $table_name . "`";
        $row = $wpdb->get_row($query, ARRAY_A);
        $total_items = $row['item_count'];
        
        $query = "SELECT * FROM `" . $table_name . "`";
        if (isset($_REQUEST['s'])) {
            $s = $_REQUEST['s']; // escaped in next two lines
            $query .= " WHERE `google_url` LIKE \"" . $wpdb->_real_escape('%' . $s . '%') . "\"";
            $query .= " OR `fontname` LIKE \"" . $wpdb->_real_escape('%' . $s . '%') . "\"";
            unset($s); // so now one will use anymore unescaped var
        }
        if (isset($_REQUEST['orderby'])) {
            $query .= " ORDER BY " . $wpdb->_real_escape($_REQUEST['orderby']);
        }
        if (isset($_REQUEST['order'])) {
            $query .= " " . ($_REQUEST['order'] == 'desc' ? 'DESC' : 'ASC');
        }
        $query .= " LIMIT " . $per_page . " OFFSET " . (((int) $current_page-1) * $per_page);

        $this->items = $wpdb->get_results($query, ARRAY_A);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
    }

}
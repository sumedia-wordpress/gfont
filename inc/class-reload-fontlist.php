<?php

class Sumedia_GFont_Reload_Fontlist
{
    public $table_name = 'sumedia_gfont_fonts';

    public function execute()
    {
        $fontlist = $this->get_font_list();
        $dblist = $this->get_db_list();


        $add_to_db = array();
        foreach ($fontlist as $fontdata) {
            list($fontfamily, $fontname) = array_values($fontdata);
            $found = false;
            foreach ($dblist as $dbdata) {
                list($dbfontfamily, $dbfontname) = array_values($dbdata);
                if($dbfontfamily == $fontfamily && $dbfontname == $fontname) {
                    $found = true;
                }
            }
            if (!$found) {
                $add_to_db[] = $fontdata;
            }
        }

        $remove_from_db = array();
        foreach ($dblist as $dbdata) {
            list($dbfontfamily, $dbfontname) = array_values($dbdata);
            $found = false;
            foreach ($fontlist as $fontdata) {
                list($fontfamily, $fontname) = array_values($fontdata);
                if ($fontfamily == $dbfontfamily && $fontname == $dbfontname) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $remove_from_db[] = $fontdata;
            }
        }

        if (count($add_to_db)) {
            $this->add_to_db($add_to_db);
        }

        if (count($remove_from_db)) {
            $this->remove_from_db($remove_from_db);
        }
    }

    /**
     * @return array
     */
    public function get_db_list()
    {
        global $wpdb;
        $list = array();
        $table_name = $wpdb->prefix . $this->table_name;
        $results = $wpdb->get_results("SELECT `fontfamily`, `fontname` FROM `" . $table_name . "`", ARRAY_A);
        foreach ($results as $row) {
            $list[] = $row;
        }
        return $list;
    }

    /**
     * @return array
     */
    public function get_font_list()
    {
        $list = array();
        $dir = plugin_dir_path(__DIR__) . '/assets/fonts';
        $dh = opendir($dir);
        if ($dh) {
            while(false !== ($file = readdir($dh))) {
                if ('.' == $file || '..' == $file) {
                    continue;
                }
                $fontfamily = $file;
                $dh2 = opendir($dir . '/' . $file);
                if ($dh2) {
                    while(false !== ($file2 = readdir($dh2))) {
                        if (substr($file2,-4) == '.css') {
                            $fontname = substr($file2, 0, -4);
                            $list[] = array(
                                'fontfamily' => $fontfamily,
                                'fontname' => $fontname,
                                'use_flag' => 0
                            );
                        }
                    }
                    closedir($dh2);
                }
            }
            closedir($dh);
        }
        return $list;
    }

    public function add_to_db($fonts)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->table_name;

        foreach ($fonts as $fontdata) {
            list($fontfamily, $fontname) = array_values($fontdata);

            $query = "INSERT IGNORE INTO  `" . $table_name . "` (`fontfamily`, `fontname`)
                VALUES(%s, %s)";
            $prepare = $wpdb->prepare($query, $fontfamily, $fontname);
            $wpdb->query($prepare);
        }
    }

    public function remove_from_db($fonts)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->table_name;

        foreach ($fonts as $fontdata) {
            list($fontfamily, $fontname) = array_values($fontdata);

            $query = "DELETE FROM `" . $table_name . "`
                WHERE `fontfamily` = %s AND `fontname` = %s";
            $prepare = $wpdb->prepare($query, $fontfamily, $fontname);
            $wpdb->query($prepare);
        }
    }
}
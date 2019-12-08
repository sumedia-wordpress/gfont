<?php

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

class Sumedia_GFont_Installer
{
    /**
     * @var string
     */
    protected $currentVersion;

    /**
     * @var string
     */
    protected $optionName = 'sumedia_gfont_version';

    /**
     * @var string
     */
    protected $table_name = 'sumedia_gfont_fonts';

    public function __construct()
    {
        $this->currentVersion = SUMEDIA_GFONT_VERSION;
    }

    public function install()
    {
        $this->install_gfont_table();
        add_option($this->optionName, $this->currentVersion);
    }

    protected function install_gfont_table()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->table_name;

        $query = "SHOW TABLES LIKE '" . $table_name . "'";
        $row = $wpdb->get_row($query);
        if ($row) {
            return;
        }

        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE `$table_name` (
            `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `fontfamily` VARCHAR(128) NOT NULL,
            `fontname` VARCHAR(128) NOT NULL,            
            `use_flag` INT(1)
        ) $charset_collate;";
        dbDelta($sql);

        $sql = "ALTER TABLE `$table_name` ADD UNIQUE KEY `font_index` (`fontfamily`, `fontname`);";
        $this->db->query($sql);
    }
}
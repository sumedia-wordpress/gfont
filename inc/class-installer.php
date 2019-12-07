<?php

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

class Sumedia_GFont_Installer
{
    /**
     * @var string
     */
    protected $installedVersion;

    /**
     * @var string
     */
    protected $currentVersion;

    /**
     * @var string
     */
    protected $optionName = 'sumedia_gfont_version';

    /**
     * @var wpdb
     */
    protected $db;

    public function __construct()
    {
        global $wpdb;
        $this->installedVersion = get_option('sumedia_gfont_version');
        $this->currentVersion = SUMEDIA_GFONT_VERSION;
        $this->db = $wpdb;
    }

    public function install()
    {

        if (-1 == version_compare($this->installedVersion, $this->currentVersion)) {
            if (-1 == version_compare($this->installedVersion, '0.1.0')) {
                $this->install_gfont_table();
            }
            //add_option($this->optionName, $this->currentVersion);
        }
    }

    protected function install_gfont_table()
    {
        $charset_collate = $this->db->get_charset_collate();
        $table_name = $this->db->prefix . 'sumedia_gfont_fonts';

        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
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
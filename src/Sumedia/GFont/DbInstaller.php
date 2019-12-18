<?php

namespace Sumedia\GFont;

class DbInstaller
{
    /**
     * @var string
     */
    protected $current_version;

    /**
     * @var string
     */
    protected $option_name;

    /**
     * @var string
     */
    protected $table_name;

    public function __construct()
    {
        $this->current_version = SUMEDIA_GFONT_VERSION;
        $this->option_name = str_replace('-', '_', SUMEDIA_GFONT_PLUGIN_NAME) . '_version';
        $this->table_name = str_replace('-', '_', SUMEDIA_GFONT_PLUGIN_NAME) . '_fonts';
    }

    public function install()
    {
        $installed_version = get_option($this->option_name);
        if (!$installed_version || version_compare($installed_version, $this->current_version, '<')) {
            $this->install_table();
            $this->migrate_table();
            add_option($this->option_name, $this->current_version);
        }
    }

    protected function install_table()
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
        $wpdb->query($sql);
    }

    protected function migrate_table()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->table_name;

        $sql = "DESCRIBE `" . $table_name . "`";
        $fields = $wpdb->get_results($sql, ARRAY_A);

        $found = false;
        foreach ($fields as $field) {
            if ($field['Field'] == 'fontfamily') {
                $found = true;
            }
        }

        if ($found) {
            $sql = "ALTER TABLE `" . $table_name . "` 
                DROP COLUMN `fontfamily`,                
                ADD `google_url` VARCHAR(256) NOT NULL AFTER `id`";
            $wpdb->query($sql);

            $sql = "TRUNCATE TABLE `" . $table_name . "`";
            $wpdb->query($sql);
        }
    }
}
<?php

class Sumedia_GFont_Plugin
{
    public function init()
    {
        $this->textdomain();
        $this->plugin_view();
        $this->controller();
        $this->enqueue_styles();
    }

    public function install()
    {
        $installer = new Sumedia_GFont_Db_Installer;
        $installer->install();
    }

    function textdomain()
    {
        load_plugin_textdomain(
            SUMEDIA_GFONT_PLUGIN_NAME,
            false,
            SUMEDIA_GFONT_PLUGIN_NAME . DIRECTORY_SEPARATOR . 'languages'
        );
    }

    public function plugin_view()
    {
        $plugins = Sumedia_Base_Registry_View::get('Sumedia_Base_Admin_View_Plugins');
        $plugins->add_plugin(SUMEDIA_GFONT_PLUGIN_NAME, [
            'name' => 'Google Fonts',
            'version' => SUMEDIA_GFONT_VERSION,
            'options' => [
                [
                    'name' => __('Fontlist', SUMEDIA_GFONT_PLUGIN_NAME),
                    'url' => admin_url('admin.php?page=sumedia&plugin=' . SUMEDIA_GFONT_PLUGIN_NAME . '&action=fontlist')
                ],
                [
                    'name' => __('Create new', SUMEDIA_GFONT_PLUGIN_NAME),
                    'url' => admin_url('admin.php?page=sumedia&plugin=' . SUMEDIA_GFONT_PLUGIN_NAME . '&action=new')
                ]
            ],
            'description_template' => Suma\ds(SUMEDIA_PLUGIN_PATH . SUMEDIA_GFONT_PLUGIN_NAME . '/admin/templates/plugin.phtml')
        ]);
    }

    public function controller()
    {
        if (isset($_GET['page']) && isset($_GET['plugin']) && isset($_GET['action'])) {
            if ($_GET['page'] == 'sumedia' && $_GET['plugin'] == SUMEDIA_GFONT_PLUGIN_NAME)
            {
                if ($_GET['action'] == 'fontlist') {
                    $controller = Sumedia_GFont_Admin_Controller_Fontlist::get_instance();
                } elseif ($_GET['action'] == 'new') {
                    $controller = Sumedia_GFont_Admin_Controller_New::get_instance();
                } elseif ($_POST['action'] == 'delete') {
                    $controller = Sumedia_GFont_Admin_Controller_Delete::get_instance();
                }

                if (isset($controller)) {
                    add_action('admin_init', [$controller, 'prepare']);
                    add_action('admin_init', [$controller, 'execute']);
                }
            }
        }
    }

    public function enqueue_styles()
    {
        add_action('wp_enqueue_scripts', function(){
            global $wpdb;
            $table_name = $wpdb->prefix . 'sumedia_gfont_fonts';

            $query = "SELECT `fontname` FROM `" . $table_name . "` WHERE `use_flag` = 1";
            $results = $wpdb->get_results($query, ARRAY_A);
            if ($results) {
                foreach ($results as $fontdata) {
                    $cssFile = SUMEDIA_PLUGIN_URL . SUMEDIA_GFONT_PLUGIN_NAME . '/data/webfonts/' . $fontdata['fontname'] . '/' . $fontdata['fontname'] . '.css';
                    wp_enqueue_style('suma_gfont_' . $fontdata['fontname'], $cssFile);
                }
            }
        });
    }
}
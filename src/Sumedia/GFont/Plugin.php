<?php

namespace Sumedia\GFont;

use Sumedia\GFont\Base\Registry;

class Plugin
{
    public function init()
    {
        $this->textdomain();
        add_action('admin_print_styles', [$this, 'admin_stylesheets']);
        add_action('admin_menu', [$this, 'setup_menu']);
        $this->controller();
        $this->enqueue_styles();
    }

    public function install()
    {
        $installer = new DbInstaller();
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

    public function setup_menu()
    {
        $menu = Registry::get('Sumedia\GFont\Admin\View\Menu');
        add_submenu_page(
            'plugins.php',
            $menu->get_page_title(),
            $menu->build_iconified_title(),
            'manage_options',
            $menu->get_slug(),
            [$menu, 'render'],
            $menu->get_pos()
        );
    }

    public function admin_stylesheets()
    {
        $cssFile = SUMEDIA_GFONT_PLUGIN_URL . '/assets/css/admin-style.css';
        wp_enqueue_style('sumedia_admin_style', $cssFile);
    }

    public function controller()
    {
        if (isset($_GET['page']) && $_GET['page'] == SUMEDIA_GFONT_PLUGIN_NAME) {
            $action = isset($_POST['action']) ? $_POST['action'] : null;
            $action = null == $action && isset($_GET['action']) ? $_GET['action'] : $action;
            if (!preg_match('#^[a-z0-9_\-]+$#i', $action)) {
                return;
            }

            $controller = 'Sumedia\GFont\Admin\Controller\\' . $action;

            $check_file = SUMEDIA_GFONT_PLUGIN_PATH . DS . 'src' . DS . str_replace('\\', DS, $controller) . '.php';
            if (file_exists($check_file)) {
                $controller = Registry::get($controller);
                add_action('admin_init', [$controller, 'prepare']);
                add_action('admin_init', [$controller, 'execute']);
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
                    $baseurl = wp_get_upload_dir()['baseurl'];
                    $cssFile = $baseurl . '/' . SUMEDIA_GFONT_PLUGIN_NAME . '/webfonts/' . $fontdata['fontname'] . '/' . $fontdata['fontname'] . '.css';
                    wp_enqueue_style('suma_gfont_' . $fontdata['fontname'], $cssFile);
                }
            }
        });
    }
}
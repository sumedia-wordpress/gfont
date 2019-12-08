<?php

class Sumedia_GFont_Plugin
{
    public function textdomain()
    {
        //$event = new Sumedia_Base_Event(function () {
        load_plugin_textdomain(
            'sumedia-gfont',
            false,
            SUMEDIA_GFONT_PLUGIN_NAME . '/languages/');
        //});
        //add_action('plugins_loaded', [$event, 'execute']);
    }

    public function installer()
    {
        $installer = new Sumedia_GFont_Installer;
        register_activation_hook(__FILE__, [$installer, 'install']);
    }

    public function view()
    {
        $view = Sumedia_Base_Registry::get_instance('view');
        $plugins = $view->get('sumedia_base_admin_view_plugins');
        $plugins->plugins[SUMEDIA_GFONT_PLUGIN_NAME] = [
            'description_template' => SUMEDIA_PLUGIN_PATH . SUMEDIA_GFONT_PLUGIN_NAME . '/admin/templates/plugin.phtml'
        ];

        if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'sumedia' && isset($_REQUEST['plugin']) && $_REQUEST['plugin'] == 'gfont') {
            $view->get('sumedia_base_admin_view_menu')->template = SUMEDIA_PLUGIN_PATH . SUMEDIA_GFONT_PLUGIN_NAME . ds('/admin/templates/config.phtml');

            $heading = $view->get('sumedia_base_admin_view_heading');
            $heading->title = __('Google Fonts');
            $heading->side_title = __('Configuration');
            $heading->version = SUMEDIA_GFONT_VERSION;
        }

        $event = new Sumedia_Base_Event(function(){
            global $wpdb;
            $table_name = $wpdb->prefix . 'sumedia_gfont_fonts';

            $query = "SELECT `fontfamily`, `fontname` FROM `" . $table_name . "` WHERE `use_flag` = 1";
            $results = $wpdb->get_results($query, ARRAY_A);
            if ($results) {
                foreach ($results as $fontdata) {
                    $cssFile = SUMEDIA_PLUGIN_URL . SUMEDIA_GFONT_PLUGIN_NAME . '/assets/fonts/' . $fontdata['fontfamily'] . '/' . $fontdata['fontname'] . '.css';
                    wp_enqueue_style('suma_gfont_' . $fontdata['fontfamily'] . '.' . $fontdata['fontname'], $cssFile);
                }
            }
        });
        add_action('wp_enqueue_scripts', [$event, 'execute']);
    }

    public function post_use_flags()
    {
        if (isset($_GET['plugin']) && $_GET['plugin'] == 'gfont'
            && isset($_GET['action']) && $_GET['action'] == 'use_flag'
        ) {
            if (wp_verify_nonce($_POST['_wpnonce'], 'bulk-plugins_page_sumedia')) {
                $form = new Sumedia_GFont_Fontlist_Form();
                $form->load();
                $form->do_request($_POST);
                $form->save();
            }
            $event = new Sumedia_Base_Event(function() {
                wp_redirect(admin_url('admin.php?page=sumedia&plugin=gfont'));
            });
            add_action('template_redirect', [$event, 'execute']);
        }
    }

    public function post_reload_fonts()
    {
        if(isset($_GET['plugin']) && $_GET['plugin'] = 'gfont'
                && isset($_GET['action']) && $_GET['action'] == 'reload_fonts') {
            if (wp_verify_nonce($_GET['nonce'], 'sumedia-gfont-reload-fonts')) {
                $reloader = new Sumedia_GFont_Reload_Fontlist();
                $reloader->execute();
            }
            $event = new Sumedia_Base_Event(function() {
                wp_redirect(admin_url('admin.php?page=sumedia&plugin=gfont'));
            });
            add_action('template_redirect', [$event, 'execute']);

        }
    }
}
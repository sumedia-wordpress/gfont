<?php

/**
 * Sumedia Google Fonts
 *
 * @package     Sumedia_GFont
 * @copyright   Copyright (C) 2019, Sumedia - kontakt@sumedia-webdesign.de
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name: Sumedia Google Fonts
 * Plugin URI:  https://github.com/sumedia-wordpress/gfont
 * Description: Use Google Fonts with non-tracking data privacy
 * Version:     0.1.0
 * Requires at least: 5.3 (nothing else tested yet
 * Rewrires PHP: 5.3.2 (not tested, could work)
 * Author:      Sven Ullmann
 * Author URI:  https://www.sumedia-webdesign.de
 * License:     GPL v3
 * Text Domain: sumedia-gfont
 * Domain Path: /languages/
 * Bug Reporting: https://github.com/sumedia-wordpress/gfont/issues
 *
 * WC requires at least: 3.0
 * WC tested up to: 3.8
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!function_exists( 'add_filter')) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

if (!defined('SUMEDIA_BASE_VERSION')) {
    if (!func_exists('sumedia_base_plugin_missing_message')) {
        function sumedia_base_plugin_missing_message()
        {
            return print '<div id="message" class="error fade"><p>' . __('In order to use Sumedia Plugins you need to install Sumedia Base Plugin (sumedia-base).') . '</p></div>';
        }
        add_action('admin_notices', 'sumedia_base_plugin_missing_messsage');
    }
} else {

    add_action('init', 'sumedia_gfont_initialize', 10);

    function sumedia_gfont_initialize()
    {
        if (defined('SUMEDIA_GFONT_VERSION')) {
            return;
        }

        global $wpdb;

        define('SUMEDIA_GFONT_VERSION', '0.1.0');
        define('SUMEDIA_GFONT_PLUGIN_NAME', dirname(plugin_basename(__FILE__)));

        $autoloader = Sumedia_Base_Autoloader::get_instance();
        $autoloader->register_autoload_dir(SUMEDIA_GFONT_PLUGIN_NAME, 'inc');

        $installer = new Sumedia_GFont_Installer;
        register_activation_hook(__FILE__, [$installer, 'install']);

        $event = new Sumedia_Base_Event(function () {
            load_plugin_textdomain(
                'sumedia-gfont',
                false,
                SUMEDIA_GFONT_PLUGIN_NAME . '/languages/');
        });
        add_action('plugins_loaded', [$event, 'execute']);

        $registry = Sumedia_Base_Registry::get_instance();
        $view_renderer = $registry->get('view_renderer');

        $plugins = $view_renderer->get('plugins');
        $plugins->set_plugin(SUMEDIA_GFONT_PLUGIN_NAME, [
            'description_template' => __DIR__ . '/admin/templates/plugin.phtml',
            'options' => [
                'config_link' => admin_url('admin.php?page=sumedia&plugin=gfont')
            ]
        ]);

        if (isset($_GET['plugin']) && $_GET['plugin'] == 'gfont'
          && isset($_GET['action']) && $_GET['action'] == 'use_flag'
        ) {
            if (wp_verify_nonce($_POST['_wpnonce'], 'bulk-plugins_page_sumedia')) {
                $form = new Sumedia_GFont_Fontlist_Form();
                $form->load();
                $form->do_request($_POST);
                $form->save();
            }
            wp_redirect(admin_url('admin.php?page=sumedia&plugin=gfont'));
        }

        if(isset($_GET['plugin']) && $_GET['plugin'] = 'gfont'
          && isset($_GET['action']) && $_GET['action'] == 'reload_fonts') {
            if (wp_verify_nonce($_GET['nonce'], 'sumedia-gfont-reload-fonts')) {
                $reloader = new Sumedia_GFont_Reload_Fontlist();
                $reloader->execute();
            }
            wp_redirect(admin_url('admin.php?page=sumedia&plugin=gfont'));
        }

        $registry = Sumedia_Base_Registry::get_instance();
        $view_renderer = $registry->get('view_renderer');
        if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'sumedia' && isset($_REQUEST['plugin']) && $_REQUEST['plugin'] == 'gfont') {
            $view_renderer->set_template(SUMEDIA_PLUGIN_PATH . SUMEDIA_GFONT_PLUGIN_NAME . '/admin/templates/config.phtml');
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

}
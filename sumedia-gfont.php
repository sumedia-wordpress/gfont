<?php

/**
 * Sumedia Google Fonts
 *
 * @package     Sumedia_GFont
 * @copyright   Copyright (C) 2019, Sumedia - kontakt@sumedia-webdesign.de
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name: sumedia-gfont
 * Plugin URI:  https://github.com/sumedia-wordpress/gfont
 * Description: Use Google Fonts with non-tracking data privacy
 * Version:     0.1.1
 * Requires at least: 5.3 (nothing else tested yet)
 * Rewrires PHP: 5.6.0 (not tested, could work)
 * Author:      Sven Ullmann
 * Author URI:  https://www.sumedia-webdesign.de
 * License:     GPL-3.0-or-later
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

require_once(__DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/vendor/autoload.php'));
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'sumedia-base.php');

define('SUMEDIA_GFONT_VERSION', '0.1.1');
define('SUMEDIA_GFONT_PLUGIN_NAME', dirname(plugin_basename(__FILE__)));

require_once(__DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/inc/class-installer.php'));
$installer = new Sumedia_GFont_Installer;
register_activation_hook(__FILE__, [$installer, 'install']);

add_action('plugins_loaded', 'sumedia_gfont_textdomain');
function sumedia_gfont_textdomain()
{
    load_plugin_textdomain(
        'sumedia-gfont',
        false,
        SUMEDIA_GFONT_PLUGIN_NAME . DIRECTORY_SEPARATOR . 'languages');
}

add_action('init', 'sumedia_gfont_init', 10);
function sumedia_gfont_init()
{
    if (defined('SUMEDIA_BASE_VERSION')) {
        $autoloader = Sumedia_Base_Autoloader::get_instance();
        $autoloader->register_autoload_dir(SUMEDIA_GFONT_PLUGIN_NAME, 'inc');

        $plugin = new Sumedia_GFont_Plugin();
        $plugin->init();
    }
}
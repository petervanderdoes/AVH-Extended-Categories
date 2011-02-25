<?php
/*
Plugin Name: AVH Extended Categories Widgets
Plugin URI: http://blog.avirtualhome.com/wordpress-plugins
Description: Replacement of the category widget to allow for greater customization of the category widget.
Version: 3.3.3
Author: Peter van der Does
Author URI: http://blog.avirtualhome.com/

Copyright 2008  Peter van der Does  (email : peter@avirtualhome.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Include WordPress version
require (ABSPATH . WPINC . '/version.php');

global $plugin;

$pluginname = plugin_basename(trim($plugin));
$dir = current(explode('/', $pluginname));

define('AVHEC_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . $dir);
define('AVHEC_PLUGIN_NAME', $pluginname);

if ((float) $wp_version >= 2.8) {
	define('AVHEC_PLUGIN_URL', WP_PLUGIN_URL . '/' . $dir . '/2.8');
	define('AVHEC_ABSOLUTE_WORKING_DIR', AVHEC_PLUGIN_DIR . '/2.8');
	define('AVHEC_RELATIVE_WORKING_DIR', $dir . '/2.8');
	unset($dir);
	require (AVHEC_ABSOLUTE_WORKING_DIR . '/avh-ec.client.php');
} else {
	require_once 'widget-pre2.8.php';
}
?>
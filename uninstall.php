<?php
// This is an include file, all normal WordPress functions will still work.
// Because the plugin is already deactivated it won't regonize any class declarations.


if (! defined('ABSPATH') && ! defined('WP_UNINSTALL_PLUGIN'))
	exit();

global $wpdb;
if ('extended-categories-widget' == dirname($file)) {
	delete_option('avhec');
	$db__used_by_plugin = $wpdb->prefix . 'avhec_category_groups';
	$result = $wpdb->query('DROP TABLE IF EXISTS `' . $db__used_by_plugin . '`');
}
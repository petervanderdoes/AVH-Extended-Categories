<?php
/**
 * Singleton Class
 */
class AVH_EC_Singleton
{

	function &getInstance ( $class, $arg1 = null )
	{
		static $instances = array (); // array of instance names
		if ( array_key_exists( $class, $instances ) ) {
			$instance = & $instances[$class];
		} else {
			if ( ! class_exists( $class ) ) {
				switch ( $class )
				{
					case 'AVH_EC_Core' :
						require_once (AVHEC_WORKING_DIR . '/class/avh-ec.core.php');
						break;
				}
			}
			$instances[$class] = new $class( $arg1 );
			$instance = & $instances[$class];
		}
		return $instance;
	} // getInstance
} // singleton

require_once (AVHEC_WORKING_DIR . '/class/avh-ec.category-group.php');
require_once (AVHEC_WORKING_DIR . '/class/avh-ec.widgets.php');

/**
 * Initialize the plugin
 *
 */
function avhextendedcategories_init ()
{
	// Admin
	if ( is_admin() ) {
		require_once (AVHEC_WORKING_DIR . '/class/avh-ec.admin.php');
		$avhec_admin = new AVH_EC_Admin();
	}
	add_action( 'widgets_init', 'avhextendedcategories_widgets_init' );

} // End avhamazon_init()


/**
 * Register the widget
 *
 * @WordPress Action widgets_init
 * @since 3.0
 *
 */
function avhextendedcategories_widgets_init ()
{
	register_widget( 'WP_Widget_AVH_ExtendedCategories_Normal' );
	register_widget( 'WP_Widget_AVH_ExtendedCategories_Top' );
	register_widget( 'WP_Widget_AVH_ExtendedCategories_Category_Group' );
}

/**
 * Called on activation of the plugin.
 *
 */
function avhec_installPlugin ()
{
	global $wpdb;

	$catgrp = new AVH_EC_Category_Group();

	// Setup the DB Tables
	$charset_collate = '';

	if ( version_compare( mysql_get_server_info(), '4.1.0', '>=' ) ) {
		if ( ! empty( $wpdb->charset ) )
			$charset_collate = 'DEFAULT CHARACTER SET ' . $wpdb->charset;
		if ( ! empty( $wpdb->collate ) )
			$charset_collate .= ' COLLATE ' . $wpdb->collate;
	}

	if ( $wpdb->get_var( 'show tables like \'' . $wpdb->avhec_cat_group . '\'' ) != $wpdb->avhec_cat_group ) {

		$sql = 'CREATE TABLE `' . $wpdb->avhec_cat_group . '` ( `term_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0, `avhec_categories` LONGTEXT NOT NULL, PRIMARY KEY (`term_id`) )' . $charset_collate . ';';

		$result = $wpdb->query( $sql );
	}

	// Setup the standard groups
	$none_group_id = wp_insert_term( 'none', $catgrp->taxonomy_name, array ('description' => 'This group will not show the widget.' ) );
	$all_group_id = wp_insert_term( 'all', $catgrp->taxonomy_name, array ('description' => 'Holds all the categories.' ) );
	$home_group_id = wp_insert_term( 'home', $catgrp->taxonomy_name, array ('description' => 'This group will be shown on the front page.' ) );

	//Fill the standard groups with all categories
	$all_categories = $catgrp->getAllCategoriesTermID();
	$catgrp->setCategoriesForGroup( $all_group_id['term_id'], $all_categories );
	$catgrp->setCategoriesForGroup( $home_group_id['term_id'], $all_categories );

}

//		Activation Hook
add_action( 'activate_' . AVHEC_PLUGIN_NAME, 'avhec_installPlugin' );
add_action( 'plugins_loaded', 'avhextendedcategories_init' );
?>
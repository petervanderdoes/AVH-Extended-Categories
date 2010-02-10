<?php
/**
 * Singleton Class
 *
 */
class AVH_EC_Singleton
{

	/**
	 *
	 * @param $class
	 * @param $arg1
	 */
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
		$avhec_admin = new AVH_EC_Admin( );
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

add_action( 'plugins_loaded', 'avhextendedcategories_init' );
?>
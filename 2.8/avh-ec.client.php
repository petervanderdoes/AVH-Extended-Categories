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
						require_once (dirname( __FILE__ ) . '/class/avh-ec.core.php');
						break;
					case 'AVH_EC_Admin' :
						require_once (dirname( __FILE__ ) . '/class/avh-ec.admin.php');
						break;
					case 'AVH_EC_DB' :
						require_once (dirname( __FILE__ ) . '/class/avh-ec.db.php');
						break;

				}
			}
			$instances[$class] = new $class( $arg1 );
			$instance = & $instances[$class];
		}
		return $instance;
	} // getInstance
} // singleton

require_once (dirname( __FILE__ ) . '/class/avh-ec.widgets.php');

/**
 * Initialize the plugin
 *
 */
function avhextendedcategories_init ()
{
	// Admin
	if ( is_admin() ) {
		$avhec_admin = & AVH_EC_Singleton::getInstance('AVH_EC_Admin');
			// Activation Hook
			register_activation_hook( __FILE__, array (&$avhfdas_admin, 'installPlugin' ) );
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
	register_widget( 'WP_Widget_AVH_ExtendedCategories_Grouped' );
}

add_action( 'plugins_loaded', 'avhextendedcategories_init' );
?>
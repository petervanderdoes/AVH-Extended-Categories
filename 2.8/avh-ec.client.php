<?php
// Include all the classes we use.
require (dirname( __FILE__ ) . '/class/avh-ec.core.php');
require (dirname( __FILE__ ) . '/class/avh-ec.widgets.php');

/**
 * Initialize the plugin
 *
 */
function avhextendedcategories_init ()
{
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
}

add_action( 'plugins_loaded', 'avhextendedcategories_init' );
?>
<?php
class AVHExtendedCategoriesCore
{
	var $version;
	var $comment;
	var $info;

	/**
	 * PHP5 constructor
	 *
	 */
	function __construct ()
	{
		$this->version = '2.1';
		$this->comment = '<!-- AVH Extended Categories version ' . $this->version . ' | http://blog.avirtualhome.com/wordpress-plugins/ -->';

		// Determine installation path & url
		$path = str_replace( '\\', '/', dirname( __FILE__ ) );
		$path = substr( $path, strpos( $path, 'plugins' ) + 8, strlen( $path ) );
		$path = substr( $path, 0, strlen( $path ) - 6 );

		$info['siteurl'] = get_option( 'siteurl' );

		$info['plugin_url'] = WP_PLUGIN_URL;
		$info['plugin_dir'] = WP_PLUGIN_DIR;

		if ( $path != 'plugins' ) {
			$info['plugin_url'] .= '/' . $path;
			$info['plugin_dir'] .= '/' . $path;
		}

		$info['lang_dir'] = $path . '/lang';
		// Set class property for info
		$this->info = array ('home' => get_option( 'home' ), 'siteurl' => $info['siteurl'], 'plugin_url' => $info['plugin_url'], 'plugin_dir' => $info['plugin_dir'], 'lang_dir' => $info['lang_dir'] );

		$this->handleTextdomain();
	}

	/**
	 * PHP4 Constructor
	 *
	 * @return AVHExtendedCategoriesCore
	 */
	function AVHExtendedCategoriesCore ()
	{
		$this->__construct();
	}

	/**
	 * Singleton method
	 *
	 * @return object
	 */
	function getInstance ()
	{
		static $_instance;
		if ( $_instance === null ) {
			$_instance = & new AVHExtendedCategoriesCore( );
		}
		return $_instance;
	}

	/**
	 * Loads the i18n
	 *
	 * @return
	 */
	function handleTextdomain ()
	{

		load_plugin_textdomain( 'avh-ec', false, $this->info['lang_dir'] );

	}

	/**
	 * Used in forms to set the checked option.
	 *
	 * @param mixed $checked
	 * @param mixed_type $current
	 * @return string
	 *
	 * @since 2.0
	 */
	function isChecked ( $checked, $current )
	{
		if ( $checked == $current ) {
			return (' checked="checked"');
		}
		return ('');
	}
}
?>
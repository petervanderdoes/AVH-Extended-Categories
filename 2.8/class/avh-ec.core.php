<?php
class AVHExtendendCategoriesCore
{
	var $version;
	var $comment;

	/**
	 * PHP5 constructor
	 *
	 */
	function __construct ()
	{
		$this->version = '2.0-rc1';
		$this->comment = '<!-- AVH Extended Categories version ' . $this->version . ' | http://blog.avirtualhome.com/wordpress-plugins/ -->';
	}

	/**
	 * PHP4 Constructor
	 *
	 * @return AVHExtendendCategoriesCore
	 */
	function AVHExtendendCategoriesCore ()
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
			$_instance = & new self( );
		}
		return $_instance;
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
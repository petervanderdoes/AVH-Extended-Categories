<?php
/**
 * AVH Extended Categorie Database Class
 *
 * @author Peter van der Does
 * @copyright 2009
 */
class AVH_EC_DB
{

	/**
	 * PHP4 constructor.
	 *
	 */
	function AVH_FDAS_DB ()
	{
		return $this->__construct();
	}

	/**
	 * PHP5 Constructor
	 * Init the Database Abstraction layer
	 *
	 */
	function __construct ()
	{
		register_shutdown_function( array (&$this, '__destruct' ) );
	}

	/**
	 * PHP5 style destructor and will run when database object is destroyed.
	 *
	 * @return bool Always true
	 */
	function __destruct ()
	{
		return true;
	}
}
?>
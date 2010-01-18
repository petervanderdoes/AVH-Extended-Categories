<?php
/**
 * AVH Extended Categorie Category Group Class
 *
 * @author Peter van der Does
 * @copyright 2010
 */
class AVH_EC_Category_Group
{
	/**
	 * Taxonomy name
	 * @var string
	 */
	var $taxonomy_name;

	/**
	 * PHP4 constructor.
	 *
	 */
	function AVH_EC_Category_Group ()
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

		$this->taxonomy_name = 'avhec_catgroup';

		/**
		 * Setup Group Categories Taxonomy
		 */
		register_taxonomy( $this->taxonomy_name, 'post', array ('hierarchical' => false, 'label' => __( 'Category Groups', 'avh-ec' ), 'query_var' => true, 'rewrite' => true ) );
		register_taxonomy( $this->taxonomy_name, 'page', array ('hierarchical' => false, 'label' => __( 'Category Groups', 'avh-ec' ), 'query_var' => true, 'rewrite' => true ) );

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

	/**
	 * Get all groups term_id
	 *
	 * @return array Term_id
	 */
	function getAllGroups ()
	{
		$categories = get_categories();
		foreach ( $categories as $category ) {
			$all_cat_id[] = $category->term_id;
		}
		return ($all_cat_id);
	}

}
?>
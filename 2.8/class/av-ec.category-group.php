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

	/**
	 * Get the categories from the given group from the DB
	 *
	 * @param int $group_id The Taxonomy Term ID
	 * @return Array|False categories. Will return FALSE, if the row does not exists.
	 *
	 */
	function getCategoriesFromGroup ( $group_id )
	{
		global $wpdb;

		// Query database
		$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->avhec_cat_group WHERE term_taxonomy_id = %s", $group_id ) );

		if ( is_object( $result ) ) {
			$categories = $result->avhec_categories;
			$return = unserialize( $categories );
		} else {
			$return = false;
		}
		return ($return);
	}

	/**
	 * Set the categories for the given group from the DB. Insert the group if it doesn't exists.
	 *
	 * @param int $group_id The Taxonomy Term ID
	 * @param array $categories The categories
	 * @return Object (false if not found)
	 *
	 */
	function setCategoriesForGroup ( $group_id, $newcategories = array() )
	{
		global $wpdb;

		$oldcategories = $this->getCategoriesFromGroup( $group_id );
		$newcategories = serialize( $categories );
		// If the new and old values are the same, no need to update.
		if ( $newcategories === $oldcategories )
			return false;

		if ( false === $oldoldcategories ) {
			$sql = $wpdb->prepare( "INSERT INTO $wpdb->avhec_cat_group (term_taxonomy_id, avhec_categories) VALUES (%d, %s)", $group_id, $$newcategories );
		} else {
			$sql = $wpdb->prepare( "UPDATE $wpdb->avhec_cat_group SET avhec_categories=%s WHERE term_taxonomy_id=%d", $newcategories, $group_id );
		}

		// Query database
		$result = $wpdb->query( $sql );

		if ( $result ) {
			return $result;
		} else {
			return false;
		}
	}
}
?>
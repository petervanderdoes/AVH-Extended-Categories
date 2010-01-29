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
		global $wpdb;

		register_shutdown_function( array (&$this, '__destruct' ) );

		$this->taxonomy_name = 'avhec_catgroup';

		// add DB pointer
		$wpdb->avhec_cat_group = $wpdb->prefix . 'avhec_category_groups';

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
	function getAllCategoriesTermID ()
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
		$result = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM '.$wpdb->terms .'t, '.$wpdb->term_taxonomy.' tt, '.$wpdb->avhec_cat_group.' cg WHERE t.term_id = cg.term_id AND tt.term_taxonomy_id = cg.term_taxonomy_id and cg.term_taxonomy_id = %d', $group_id ) );

		if ( is_array( $result ) ) { // Call succeeded
			if ( empty( $result ) ) { // No rows found
				$return = array ();
			} else {
				foreach ( $result as $row ) {
					$return[] = $row->term_id;
				}
			}
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
	function setCategoriesForGroup ( $group_id, $categories = array() )
	{
		global $wpdb;

		$old_categories = $this->getCategoriesFromGroup( $group_id );
		if ( ! is_array( $categories ) ) {
			$categories = array ();
		}
		$new_categories = $categories;
		// If the new and old values are the same, no need to update.
		if ( $new_categories === $old_categories ) {
			return false;
		}

		$new = array_diff( $new_categories, $old_categories );
		$removed = array_diff( $old_categories, $new_categories );

		if ( ! empty( $new ) ) {
			foreach ( $new as $cat_term_id ) {
				$insert[] = '(' . $group_id . ',' . $cat_term_id . ')';
			}
			$value = implode( ',', $insert );
			$sql = 'INSERT INTO ' . $wpdb->avhec_cat_group . ' (term_taxonomy_id, term_id) VALUES ' . $value;
			$result = $wpdb->query( $sql );

		}

		if ( ! empty( $removed ) ) {
			$delete = implode( ',', $removed );
			$sql = $wpdb->prepare( 'DELETE FROM '.$wpdb->avhec_cat_group.' WHERE term_taxonomy_id=%d and term_id IN ('.$delete.')', $group_id );
			$result = $wpdb->query( $sql );

		}

		return $result;
	}

	/**
	 * Same as get_term_by, but returns the ID only if found, else false
	 * @param string $field
	 * @param string $value
	 * @return int|boolean
	 */
	function getTermIDBy ( $field, $value )
	{
		$row = get_term_by( $field, $value, $this->taxonomy_name );
		if ( false === $row ) {
			$return = false;
		} else {
			$return = ( int ) $row->term_taxonomy_id;
		}
		return ($return);
	}

	function getGroup($group_id) {
		global $wpdb;

		$sql=$wpdb->prepare('SELECT * FROM '.$wpdb->terms.' t, '.$wpdb->term_taxonomy.' tt WHERE t.term_id=tt.term_id AND tt.term_taxonomy_id=%d', $group_id);
		$result = $wpdb->get_row( $sql );
		if (null === $result) {
			$result=false;
		}
		return ($result);
	}

	function doInsertGroup ( $term, $args = array() )
	{
		$row = wp_insert_term( $term, $this->taxonomy_name, $args );
		return ($row['term_taxonomy_id']);
	}

	function doDeleteGroup ( $group_id )
	{

		global $wpdb;

		$group=$this->getGroup($group_id);
		$result = $wpdb->query( $wpdb->prepare( 'DELETE FROM '.$wpdb->avhec_cat_group.' WHERE term_taxonomy_id=%d', $group_id ) );
		$return = wp_delete_term( $group->term_id, $this->taxonomy_name );
		return ($return);
	}

	/**
	 * Deletes the given category from all groups
	 *
	 * @param $category_id
	 */
	function doDeleteCategoryFromGroup($category_id){
		global $wpdb;
		$result = $wpdb->query( $wpdb->prepare( 'DELETE FROM '.$wpdb->avhec_cat_group.' WHERE term_id=%d', $category_id ) );
	}
}
?>
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

	var $db_options_widget_titles;

	var $options_widget_titles;

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

		/**
		 * Taxonomy name
		 * @var string
		 */
		$this->taxonomy_name = 'avhec_catgroup';

		$this->db_options_widget_titles = 'avhec_widget_titles';
		// add DB pointer
		$wpdb->avhec_cat_group = $wpdb->prefix . 'avhec_category_groups';

		/**
		 * Create the table if it doesn't exist.
		 *
		 */
		if ( $wpdb->get_var( 'show tables like \'' . $wpdb->avhec_cat_group . '\'' ) != $wpdb->avhec_cat_group ) {
			add_action( 'init', array (&$this, 'doCreateTable' ) );
		}
		add_action( 'init', array (&$this, 'doRegisterTaxonomy' ) );
		add_action( 'init', array (&$this, 'doSetupOptions' ) );

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
	 * Setup Group Categories Taxonomy
	 * @WordPress_action init
	 *
	 */
	function doRegisterTaxonomy ()
	{
		/**
		 * Setup Group Categories Taxonomy
		 */
		if ( ! is_taxonomy( $this->taxonomy_name ) ) {
			register_taxonomy( $this->taxonomy_name, 'post', array ('hierarchical' => false, 'label' => __( 'Category Groups', 'avh-ec' ), 'query_var' => true, 'rewrite' => true, 'show_ui'=>false ) );
			register_taxonomy( $this->taxonomy_name, 'page', array ('hierarchical' => false, 'label' => __( 'Category Groups', 'avh-ec' ), 'query_var' => true, 'rewrite' => true, 'show_ui'=>false ) );
		}

		// Setup the standard groups if the none group does not exists.
		if ( false === $this->getTermIDBy( 'slug', 'none' ) ) {
			$none_group_id = wp_insert_term( 'none', $this->taxonomy_name, array ('description' => 'This group will not show the widget.' ) );
			$all_group_id = wp_insert_term( 'All', $this->taxonomy_name, array ('description' => 'Holds all the categories.' ) );
			$home_group_id = wp_insert_term( 'Home', $this->taxonomy_name, array ('description' => 'This group will be shown on the front page.' ) );

			//	Fill the standard groups with all categories
			$all_categories = $this->getAllCategoriesTermID();
			$this->setCategoriesForGroup( $all_group_id['term_id'], $all_categories );
			$this->setCategoriesForGroup( $home_group_id['term_id'], $all_categories );
			$this->setWidgetTitleForGroup( $all_group_id['term_id'], '' );
			$this->setWidgetTitleForGroup( $home_group_id['term_id'], '' );
		}
	}

	/**
	 * Setup the options for the widget titles
	 * @WordPress_action init
	 *
	 */
	function doSetupOptions ()
	{
		$options = get_option( $this->db_options_widget_titles );
		if ( ! $options ) {
			$options = array ();
			$id = $this->getTermIDBy( 'slug', 'all' );
			$options[$id] = '';
			$id = $this->getTermIDBy( 'slug', 'home' );
			$options[$id] = '';
			update_option( $this->db_options_widget_titles, $options );
		}
		$this->options_widget_titles = $options;
	}

	/**
	 * Create Table
	 * @WordPress_action init
	 *
	 */
	function doCreateTable ()
	{
		global $wpdb;

		// Setup the DB Tables
		$charset_collate = '';

		if ( version_compare( mysql_get_server_info(), '4.1.0', '>=' ) ) {
			if ( ! empty( $wpdb->charset ) )
				$charset_collate = 'DEFAULT CHARACTER SET ' . $wpdb->charset;
			if ( ! empty( $wpdb->collate ) )
				$charset_collate .= ' COLLATE ' . $wpdb->collate;
		}

		$sql = 'CREATE TABLE `' . $wpdb->avhec_cat_group . '` ( `group_term_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0, `term_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0, PRIMARY KEY (`group_term_id`, `term_id`) )' . $charset_collate . ';';

		$result = $wpdb->query( $sql );
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
		$result = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->terms . ' t, ' . $wpdb->avhec_cat_group . ' cg WHERE t.term_id = cg.term_id AND cg.group_term_id = %d', $group_id ) );

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
		sort( $old_categories );
		sort( $new_categories );
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
			$sql = 'INSERT INTO ' . $wpdb->avhec_cat_group . ' (group_term_id, term_id) VALUES ' . $value;
			$result = $wpdb->query( $sql );

		}

		if ( ! empty( $removed ) ) {
			$delete = implode( ',', $removed );
			$sql = $wpdb->prepare( 'DELETE FROM ' . $wpdb->avhec_cat_group . ' WHERE group_term_id=%d and term_id IN (' . $delete . ')', $group_id );
			$result = $wpdb->query( $sql );

		}

		return $result;
	}

	/**
	 * Set the Widget Title for a Group
	 * @param int $group_id
	 * @param string $widget_title
	 *
	 */
	function setWidgetTitleForGroup ( $group_id, $widget_title = '' )
	{
		$this->options_widget_titles[$group_id] = $widget_title;
		update_option( $this->db_options_widget_titles, $this->options_widget_titles );
	}

	/**
	 * Return the title for a group_id if exsist otherwise return false
	 * @param $group_id
	 *
	 */
	function getWidgetTitleForGroup ( $group_id )
	{
		if ( isset( $this->options_widget_titles[$group_id] ) ) {
			return ($this->options_widget_titles[$group_id]);
		}
		return false;
	}

	/**
	 * Delete the Widget Title for a group
	 *
	 * @param $group_id
	 */
	function doDeleteWidgetTitle ( $group_id )
	{
		if ( isset( $this->db_options_widget_titles[$group_id] ) ) {
			unset( $this->db_options_widget_titles[$group_id] );
		}
		update_option( $this->db_options_widget_titles, $this->options_widget_titles );
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
			$return = ( int ) $row->term_id;
		}
		return ($return);
	}

	/**
	 * Gets all information of a group
	 *
	 * @param $group_id
	 * @return Object|False Returns False when the group doesn't exists.
	 */
	function getGroup ( $group_id )
	{
		global $wpdb;

		$result = get_term( ( int ) $group_id, $this->taxonomy_name );
		if ( null === $result ) {
			$result = false;
		}
		return ($result);
	}

	/**
	 * Inserts a new group
	 *
	 * @param $term
	 * @param array $args
	 */
	function doInsertGroup ( $term, $args = array(), $widget_title = '' )
	{
		$row = wp_insert_term( $term, $this->taxonomy_name, $args );
		$this->setWidgetTitleForGroup( $term, $widget_title );
		return ($row['term_id']);
	}

	/**
	 * Deletes a group
	 *
	 * @param $group_id
	 */
	function doDeleteGroup ( $group_id )
	{

		global $wpdb;

		$group = $this->getGroup( $group_id );
		$result = $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->avhec_cat_group . ' WHERE group_term_id=%d', $group_id ) );
		$this->doDeleteWidgetTitle( $group_id );
		$return = wp_delete_term( $group->term_id, $this->taxonomy_name );

		return ($return);
	}

	/**
	 * Update a group
	 *
	 * @param $group_id
	 * @param $selected_categories
	 * @param $widget_title
	 *
	 * return -1,0,1 Unknown Group, Duplicate Slug, Succesfull
	 */
	function doUpdateGroup ( $group_id, $args = array(), $selected_categories, $widget_title = '' )
	{

		$group = $this->getGroup( $group_id );
		if ( is_object( $group ) ) {
			$id = wp_update_term( $group->term_id, $this->taxonomy_name, $args );
			if ( ! is_wp_error( $id ) ) {
				$this->setWidgetTitleForGroup( $group_id, $widget_title );
				$this->setCategoriesForGroup( $group_id, $selected_categories );
				$return = 1; // Succesful
			} else {
				$return = 0; // Duplicate Slug
			}
		} else {
			$return = - 1; // Unknown group
		}
		return ($return);
	}

	/**
	 * Deletes the given category from all groups
	 *
	 * @param $category_id
	 */
	function doDeleteCategoryFromGroup ( $category_id )
	{
		global $wpdb;
		$result = $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->avhec_cat_group . ' WHERE term_id=%d', $category_id ) );
	}
}
?>
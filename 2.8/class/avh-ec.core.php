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
		$this->version = '2.2';
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

	/**
	 * Get the base directory of a directory structure
	 *
	 * @param string $directory
	 * @return string
	 *
	 */
	function getBaseDirectory ( $directory )
	{
		//get public directory structure eg "/top/second/third"
		$public_directory = dirname( $directory );
		//place each directory into array
		$directory_array = explode( '/', $public_directory );
		//get highest or top level in array of directory strings
		$public_base = max( $directory_array );

		return $public_base;
	}

	/**
	 * Display or retrieve the HTML dropdown list of categories.
	 *
	 * The list of arguments is below:
	 *     'show_option_all' (string) - Text to display for showing all categories.
	 *     'show_option_none' (string) - Text to display for showing no categories.
	 *     'orderby' (string) default is 'ID' - What column to use for ordering the
	 * categories.
	 *     'order' (string) default is 'ASC' - What direction to order categories.
	 *     'show_last_update' (bool|int) default is 0 - See {@link get_categories()}
	 *     'show_count' (bool|int) default is 0 - Whether to show how many posts are
	 * in the category.
	 *     'hide_empty' (bool|int) default is 1 - Whether to hide categories that
	 * don't have any posts attached to them.
	 *     'child_of' (int) default is 0 - See {@link get_categories()}.
	 *     'exclude' (string) - See {@link get_categories()}.
	 *     'echo' (bool|int) default is 1 - Whether to display or retrieve content.
	 *     'depth' (int) - The max depth.
	 *     'tab_index' (int) - Tab index for select element.
	 *     'name' (string) - The name attribute value for selected element.
	 *     'class' (string) - The class attribute value for selected element.
	 *     'selected' (int) - Which category ID is selected.
	 *
	 * The 'hierarchical' argument, which is disabled by default, will override the
	 * depth argument, unless it is true. When the argument is false, it will
	 * display all of the categories. When it is enabled it will use the value in
	 * the 'depth' argument.
	 *
	 * @since 2.1.0
	 *
	 * @param string|array $args Optional. Override default arguments.
	 * @return string HTML content only if 'echo' argument is 0.
	 */
	function avh_wp_dropdown_categories ( $args = '' )
	{
		$mywalker = new AVH_Walker_CategoryDropdown( );

		$defaults = array ('show_option_all' => '', 'show_option_none' => '', 'orderby' => 'id', 'order' => 'ASC', 'show_last_update' => 0, 'show_count' => 0, 'hide_empty' => 1, 'child_of' => 0, 'exclude' => '', 'echo' => 1, 'selected' => 0, 'hierarchical' => 0, 'name' => 'cat', 'class' => 'postform', 'depth' => 0, 'tab_index' => 0, 'walker' => $mywalker );

		$defaults['selected'] = (is_category()) ? get_query_var( 'cat' ) : 0;

		$r = wp_parse_args( $args, $defaults );
		$r['include_last_update_time'] = $r['show_last_update'];
		extract( $r );

		$tab_index_attribute = '';
		if ( ( int ) $tab_index > 0 )
			$tab_index_attribute = ' tabindex="' . $tab_index . '"';

		$categories = get_categories( $r );

		$output = '';
		if ( ! empty( $categories ) ) {
			$output = '<select name="' . $name . '" id="' . $name . '" class="' . $class . '" ' . $tab_index_attribute . '>' . "\n";

			if ( $show_option_all ) {
				$show_option_all = apply_filters( 'list_cats', $show_option_all );
				$selected = ('0' === strval( $r['selected'] )) ? " selected='selected'" : '';
				$output .= '\t<option value="0"' . $selected . '>' . $show_option_all . '</option>' . "\n";
			}

			if ( $show_option_none ) {
				$show_option_none = apply_filters( 'list_cats', $show_option_none );
				$selected = ('-1' === strval( $r['selected'] )) ? " selected='selected'" : '';
				$output .= '\t<option value="-1"' . $selected . '>' . $show_option_none . '</option>' . "\n";
			}

			if ( $hierarchical )
				$depth = $r['depth']; // Walk the full depth.
			else
				$depth = - 1; // Flat.


			$output .= walk_category_dropdown_tree( $categories, $depth, $r );
			$output .= "</select>\n";
		}

		$output = apply_filters( 'wp_dropdown_cats', $output );

		if ( $echo )
			echo $output;

		return $output;
	}
}

class AVH_Walker_CategoryDropdown extends Walker_CategoryDropdown {
	function walk( $elements, $max_depth) {

		$args = array_slice(func_get_args(), 2);
		$output = '';

		if ($max_depth < -1) //invalid parameter
			return $output;

		if (empty($elements)) //nothing to walk
			return $output;

		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];

		// flat display
		if ( -1 == $max_depth ) {
			$empty_array = array();
			foreach ( $elements as $e )
				$this->display_element( $e, $empty_array, 1, 0, $args, $output );
			return $output;
		}

		/*
		 * need to display in hierarchical order
		 * seperate elements into two buckets: top level and children elements
		 * children_elements is two dimensional array, eg.
		 * children_elements[10][] contains all sub-elements whose parent is 10.
		 */
		$top_level_elements = array();
		$children_elements  = array();
		foreach ( $elements as $e) {
			if ( 0 == $e->$parent_field )
				$top_level_elements[] = $e;
			else
				$children_elements[ $e->$parent_field ][] = $e;
		}

		/*
		 * when none of the elements is top level
		 * assume the first one must be root of the sub elements
		 */
		if ( empty($top_level_elements) ) {

			$first = array_slice( $elements, 0, 1 );
			$root = $first[0];

			$top_level_elements = array();
			$children_elements  = array();
			foreach ( $elements as $e) {
				if ( $root->$parent_field == $e->$parent_field )
					$top_level_elements[] = $e;
				else
					$children_elements[ $e->$parent_field ][] = $e;
			}
		}

		if (count ($children_elements) >0 ) {

		}
		foreach ( $top_level_elements as $e )
			$this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );

		/*
		 * if we are displaying all levels, and remaining children_elements is not empty,
		 * then we got orphans, which should be displayed regardless
		 */
		if ( ( 0 == $max_depth || 1 == $max_depth) && count( $children_elements ) > 0 ) {
			$empty_array = array();
			foreach ( $children_elements as $orphans )
				foreach( $orphans as $op )
					$this->display_element( $op, $empty_array, 1, 0, $args, $output );
		 }

		 return $output;
	}
}

?>
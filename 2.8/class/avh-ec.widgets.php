<?php
class WP_Widget_AVH_ExtendedCategories_Normal extends WP_Widget
{
	var $core;

	/**
	 * PHP 5 Constructor
	 *
	 */
	function __construct ()
	{
		$this->core = AVHExtendendCategoriesCore::getInstance();

		//Convert the old option widget_extended_categories to widget_extended-categories
		$old = get_option( 'widget_extended_categories' );
		if ( ! (false === $old) ) {
			update_option( 'widget_extended-categories', $old );
			delete_option( 'widget_extended_categories' );
		}
		$widget_ops = array ('description' => __( "An extended version of the default Categories widget." ) );
		WP_Widget::__construct( 'extended-categories', __( 'AVH Extended Categories' ), $widget_ops );
	}

	function WP_Widget_AVH_ExtendedCategories_Normal ()
	{
		$this->__construct();
	}

	/**
	 * Display the widget
	 *
	 * @param unknown_type $args
	 * @param unknown_type $instance
	 */
	function widget ( $args, $instance )
	{
		extract( $args );

		$c = $instance['count'] ? '1' : '0';
		$h = $instance['hierarchical'] ? '1' : '0';
		$d = $instance['depth'] ? $instance['depth'] : -1;
		$e = $instance['hide_empty'] ? '1' : '0';
		$s = $instance['sort_column'] ? $instance['sort_column'] : 'name';
		$o = $instance['sort_order'] ? $instance['sort_order'] : 'asc';
		$r = $instance['rssfeed'] ? 'RSS' : '';
		$i = $instance['rssimage'] ? $instance['rssimage'] : '';
		if ( empty( $r ) ) {
			$i = '';
		}
		if ( ! empty( $i ) ) {
			if ( ! file_exists( ABSPATH . '/' . $i ) ) {
				$i = '';
			}
		}
		if (empty($d)) {
			$d=-1;
		}

		$title = empty( $instance['title'] ) ? __( 'Categories' ) : attribute_escape( $instance['title'] );
		$style = empty( $instance['style'] ) ? 'list' : $instance['style'];
		if ( $instance['post_category'] ) {
			$post_category = unserialize( $instance['post_category'] );
			$included_cats = implode( ",", $post_category );
		}
		$cat_args = array ('include' => $included_cats, 'orderby' => $s, 'order' => $o, 'show_count' => $c, 'hide_empty' => $e, 'hierarchical' => $h, 'depth' => $d, 'title_li' => '', 'show_option_none' => __( 'Select Category' ), 'feed' => $r, 'feed_image' => $i, 'name' => 'extended-categories-select-' . $this->number );
		echo $before_widget;
		echo $this->core->comment;
		echo $before_title . $title . $after_title;
		echo '<ul>';

		if ( $style == 'list' ) {
			wp_list_categories( $cat_args );
		} else {
			wp_dropdown_categories( $cat_args );
			echo '<script type=\'text/javascript\'>'."\n";
			echo '/* <![CDATA[ */'."\n";
			echo '            var ec_dropdown_' . $this->number . ' = document.getElementById("extended-categories-select-' . $this->number . '");'."\n";
			echo '            function ec_onCatChange_' . $this->number . '() {'."\n";
			echo '                if ( ec_dropdown_' . $this->number . '.options[ec_dropdown_' . $this->number . '.selectedIndex].value > 0 ) {'."\n";
			echo '                    location.href = "' . get_option( 'home' ) . '/?cat="+ec_dropdown_' . $this->number . '.options[ec_dropdown_' . $this->number . '.selectedIndex].value;'."\n";
			echo '                }'."\n";
			echo '            }'."\n";
			echo '            ec_dropdown_' . $this->number . '.onchange = ec_onCatChange_' . $this->number . ';'."\n";
			echo '/* ]]> */'."\n";
			echo '</script>'."\n";
		}
		echo '</ul>';
		echo $after_widget;
	}

	/**
	 * When Widget Control Form Is Posted
	 *
	 * @param unknown_type $new_instance
	 * @param unknown_type $old_instance
	 * @return unknown
	 */
	function update ( $new_instance, $old_instance )
	{
		// update the instance's settings
		if ( ! isset( $new_instance['submit'] ) ) {
			return false;
		}

		$instance = $old_instance;

		$instance['title'] = strip_tags( stripslashes( $new_instance['title'] ) );
		$instance['count'] = isset( $new_instance['count'] );
		$instance['hierarchical'] = isset( $new_instance['hierarchical'] );
		$instance['hide_empty'] = isset( $new_instance['hide_empty'] );
		$instance['sort_column'] = strip_tags( stripslashes( $new_instance['sort_column'] ) );
		$instance['sort_order'] = strip_tags( stripslashes( $new_instance['sort_order'] ) );
		$instance['style'] = strip_tags( stripslashes( $new_instance['style'] ) );
		$instance['rssfeed'] = isset( $new_instance['rssfeed'] );
		$instance['rssimage'] = attribute_escape( $new_instance['rssimage'] );
		if ( array_key_exists( 'all', $new_instance['post_category'] ) ) {
			$instance['post_category'] = false;
		} else {
			$instance['post_category'] = serialize( $new_instance['post_category'] );
		}
		$instance['depth'] = ( int ) $new_instance['depth'];
		if ( $instance['depth'] < 0 || 11 < $instance['depth'] ) {
			$instance['depth'] = 0;
		}
		return $instance;
	}

	/**
	 *  Display Widget Control Form
	 *
	 * @param unknown_type $instance
	 */
	function form ( $instance )
	{
		// displays the widget admin form
		$instance = wp_parse_args( ( array ) $instance, array ('title' => '', 'rssimage' => '', 'depth'=>0 ) );

		// Prepare data for display
		$title = htmlspecialchars( $instance['title'], ENT_QUOTES );
		$count = ( bool ) $instance['count'];
		$hierarchical = ( bool ) $instance['hierarchical'];
		$depth = (int) $instance['depth'];
		$hide_empty = ( bool ) $instance['hide_empty'];
		$sort_id = ($instance['sort_column'] == 'ID') ? ' SELECTED' : '';
		$sort_name = ($instance['sort_column'] == 'name') ? ' SELECTED' : '';
		$sort_count = ($instance['sort_column'] == 'count') ? ' SELECTED' : '';
		$sort_order_a = ($instance['sort_order'] == 'asc') ? ' SELECTED' : '';
		$sort_order_d = ($instance['sort_order'] == 'desc') ? ' SELECTED' : '';
		$style_list = ($instance['style'] == 'list') ? ' SELECTED' : '';
		$style_drop = ($instance['style'] == 'drop') ? ' SELECTED' : '';
		$rssfeed = ( bool ) $instance['rssfeed'];
		$rssimage = htmlspecialchars( $instance['rssimage'], ENT_QUOTES );
		$selected_cats = ($instance['post_category'] != '') ? unserialize ( $instance['post_category'] ) : false;

		if ($depth < 0 || 11 < $depth) {
			$depth = 0;
		}
		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'title' ) . '">';
		_e( 'Title:' );
		echo '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . $title . '" /> ';
		echo '</label>';
		echo '</p>';

		echo '<p>';

		echo '<label for="' . $this->get_field_id( 'count' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'count' ) . '"	name="' . $this->get_field_name( 'count' ) . '" ' . $this->core->isChecked( true, $count ) . ' /> ';
		_e( 'Show post counts' );
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'hierachical' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'hierachical' ) . '" name="' . $this->get_field_name( 'hierarchical' ) . '" ' . $this->core->isChecked( true, $hierarchical ) . ' /> ';
		_e( 'Show hierarchy' );
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'depth' ) . '">';
		_e( 'How many levels to show' );
		echo '</label>';
		echo '<select id="' . $this->get_field_id( 'depth' ) . '" name="' . $this->get_field_name( 'depth' ) . '"> ';
		echo '<option value="0" ' . ( 0 == $depth ? "selected='selected'" : '' ) . '>' . __( 'All Levels' ) . '</option>';
		echo '<option value="1" ' . ( 1 == $depth ? "selected='selected'" : '' ) . '>' . __( 'Toplevel only' ) . '</option>';
		for ($i=2;$i<=11;$i++){
			echo '<option value="'.$i.'" ' . ( $i == $depth ? "selected='selected'" : '' ) . '>' . __( 'Child ' ) . ($i-1).'</option>';
		}
		echo '</select>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'hide_empty' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'hide_empty' ) . '"	name="' . $this->get_field_name( 'hide_empty' ) . '" ' . $this->core->isChecked( true, $hide_empty ) . '/> ';
		_e( 'Hide empty categories' );
		echo '</label>';
		echo '<br />';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'sort_column' ) . '">';
		_e( 'Sort by ' );
		echo '<select id="' . $this->get_field_id( 'sort_column' ) . '" name="' . $this->get_field_name( 'sort_column' ) . '"> ';
		echo '<option value="ID" ' . $sort_id . '>' . __( 'ID' ) . '</option>';
		echo '<option value="name" ' . $sort_name . '>' . __( 'Name' ) . '</option>';
		echo '<option value="count" ' . $sort_count . '>' . __( 'Count' ) . '</option>';
		echo '</select>';
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'sort_order' ) . '">';
		_e( 'Sort order ' );
		echo '<select id="' . $this->get_field_id( 'sort_order' ) . '"	name="' . $this->get_field_name( 'sort_order' ) . '"> ';
		echo '<option value="asc" ' . $sort_order_a . '>' . __( 'Ascending' ) . '</option>';
		echo '<option value="desc" ' . $sort_order_d . '>' . __( 'Descending' ) . '</option>';
		echo '</select>';
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'style' ) . '">';
		_e( 'Display style ' );
		echo '<select id="' . $this->get_field_id( 'style' ) . '" name="' . $this->get_field_name( 'style' ) . '"> ';
		echo '<option value="list" ' . $style_list . '>' . __( 'List' ) . '</option>';
		echo '<option value="drop" ' . $style_drop . '>' . __( 'Drop down' ) . '</option>';
		echo '</select>';
		echo '</label>';
		echo '<br />';
		echo '</p>';

		echo '<p>';

		echo '<label for="' . $this->get_field_id( 'rssfeed' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'rssfeed' ) . '" name="' . $this->get_field_name( 'rssfeed' ) . '" ' . $this->core->isChecked( true, $rssfeed ) . '/> ';
		_e( 'Show RSS Feed' );
		echo '</label>';
		echo '<br />';

		echo '<label for="">';
		_e( 'Full path to RSS image:' );
		echo '<input class="widefat" id="' . $this->get_field_id( 'rssimage' ) . '" name="' . $this->get_field_name( 'rssimage' ) . '" type="text" value="' . $rssimage . '" />';
		echo '</label>';
		echo '</p>';

		echo '<p>';
		echo '<b>' . __( 'Include these categories' ) . '</b><hr />';
		echo '<ul id="categorychecklist" class="list:category categorychecklist form-no-clear" style="list-style-type: none; margin-left: 5px; padding-left: 0px; margin-bottom: 20px;">';
		echo '<li id="' . $this->get_field_id( 'category--1' ) . '" class="popular-category">';
		echo '<label for="' . $this->get_field_id( 'post_category' ) . '" class="selectit">';
		echo '<input value="all" id="' . $this->get_field_id( 'post_category' ) . '" name="' . $this->get_field_name( 'post_category' ) . '[all]" type="checkbox" ' . $this->core->isChecked( false, $selected_cats ) . '> ';
		_e( 'Include All Categories' );
		echo '</label>';
		echo '</li>';
		$this->avh_wp_category_checklist( 0, 0, $selected_cats, false, $this->number );
		echo '</ul>';
		echo '</p>';

		echo '<input type="hidden" id="' . $this->get_field_id( 'submit' ) . '" name="' . $this->get_field_name( 'submit' ) . '" value="1" />';
	}


	/**
	 * Creates the categories checklist
	 *
	 * @param int $post_id
	 * @param int $descendants_and_self
	 * @param array $selected_cats
	 * @param array $popular_cats
	 * @param int $number
	 */
	function avh_wp_category_checklist ( $post_id = 0, $descendants_and_self = 0, $selected_cats = false, $popular_cats = false, $number )
	{
		$walker = new AVH_Walker_Category_Checklist( );
		$walker->number = $number;
		$walker->input_id = $this->get_field_id( 'post_category' );
		$walker->input_name = $this->get_field_name( 'post_category' );
		$walker->li_id = $this->get_field_id( 'category--1' );

		$descendants_and_self = ( int ) $descendants_and_self;

		$args = array ();
		if ( is_array( $selected_cats ) )
			$args['selected_cats'] = $selected_cats;
		elseif ( $post_id )
			$args['selected_cats'] = wp_get_post_categories( $post_id );
		else
			$args['selected_cats'] = array ();

		if ( is_array( $popular_cats ) )
			$args['popular_cats'] = $popular_cats;
		else
			$args['popular_cats'] = get_terms( 'category', array ('fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );

		if ( $descendants_and_self ) {
			$categories = get_categories( "child_of=$descendants_and_self&hierarchical=0&hide_empty=0" );
			$self = get_category( $descendants_and_self );
			array_unshift( $categories, $self );
		} else {
			$categories = get_categories( 'get=all' );
		}

		// Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
		$checked_categories = array ();
		for ( $i = 0; isset( $categories[$i] ); $i ++ ) {
			if ( in_array( $categories[$i]->term_id, $args['selected_cats'] ) ) {
				$checked_categories[] = $categories[$i];
				unset( $categories[$i] );
			}
		}

		// Put checked cats on top
		echo call_user_func_array( array (&$walker, 'walk' ), array ($checked_categories, 0, $args ) );
		// Then the rest of them
		echo call_user_func_array( array (&$walker, 'walk' ), array ($categories, 0, $args ) );
	}
}



class WP_Widget_AVH_ExtendendCategories_Top extends WP_Widget  {
		var $core;

	/**
	 * PHP 5 Constructor
	 *
	 */
	function __construct ()
	{
		$this->core = AVHExtendendCategoriesCore::getInstance();

		$widget_ops = array ('description' => __( "Shows the top categories." ) );
		WP_Widget::__construct( false, __( 'AVH Extended Categories Top' ), $widget_ops );
	}

	function WP_Widget_AVH_ExtendedCategories_Top ()
	{
		$this->__construct();
	}

	/** Echo the widget content.
	 *
	 * Subclasses should over-ride this function to generate their widget code.
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	function widget($args, $instance) {

		extract( $args );

		$title = apply_filters('widget_title',empty( $instance['title'] ) ? __( 'Categories' ) :  $instance['title'] );
		$style = empty( $instance['style'] ) ? 'list' : $instance['style'];
		if ( !$a = (int) $instance['amount'] ) {
			$a = 5;
		} elseif ( $a < 1 ) {
			$a = 1;
		}
		$c = $instance['count'] ? '1' : '0';
		$s = $instance['sort_column'] ? $instance['sort_column'] : 'name';
		$o = $instance['sort_order'] ? $instance['sort_order'] : 'asc';
		$r = $instance['rssfeed'] ? 'RSS' : '';
		$i = $instance['rssimage'] ? $instance['rssimage'] : '';
		if ( empty( $r ) ) {
			$i = '';
		}
		if ( ! empty( $i ) ) {
			if ( ! file_exists( ABSPATH . '/' . $i ) ) {
				$i = '';
			}
		}

		$top_cats= get_terms( 'category', array ('fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'number' => $a, 'hierarchical' => false ) );
		$included_cats = implode( ",", $top_cats );

		$cat_args = array ('include' => $included_cats, 'orderby' => $s, 'order' => $o, 'show_count' => $c, 'hide_empty' => 0, 'hierarchical' => 0, 'depth' => -1, 'title_li' => '', 'show_option_none' => __( 'Select Category' ), 'feed' => $r, 'feed_image' => $i, 'name' => 'extended-categories-top-select-' . $this->number );
		echo $before_widget;
		echo $this->core->comment;
		echo $before_title . $title . $after_title;
		echo '<ul>';

		if ( $style == 'list' ) {
			wp_list_categories( $cat_args );
		} else {
			wp_dropdown_categories( $cat_args );
			echo '<script type=\'text/javascript\'>'."\n";
			echo '/* <![CDATA[ */'."\n";
			echo '            var ec_dropdown_top_' . $this->number . ' = document.getElementById("extended-categories-top-select-' . $this->number . '");'."\n";
			echo '            function ec_top_onCatChange_' . $this->number . '() {'."\n";
			echo '                if ( ec_dropdown_top_' . $this->number . '.options[ec_dropdown_top_' . $this->number . '.selectedIndex].value > 0 ) {'."\n";
			echo '                    location.href = "' . get_option( 'home' ) . '/?cat="+ec_dropdown_top_' . $this->number . '.options[ec_dropdown_top_' . $this->number . '.selectedIndex].value;'."\n";
			echo '                }'."\n";
			echo '            }'."\n";
			echo '            ec_dropdown_top_' . $this->number . '.onchange = ec_top_onCatChange_' . $this->number . ';'."\n";
			echo '/* ]]> */'."\n";
			echo '</script>'."\n";
		}
		echo '</ul>';
		echo $after_widget;
	}

	/** Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	function update ( $new_instance, $old_instance )
	{
		// update the instance's settings
		if ( ! isset( $new_instance['submit'] ) ) {
			return false;
		}

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['amount'] = ( int ) $new_instance['amount'];
		$instance['count'] = isset( $new_instance['count'] );
		$instance['sort_column'] = strip_tags( stripslashes( $new_instance['sort_column'] ) );
		$instance['sort_order'] = strip_tags( stripslashes( $new_instance['sort_order'] ) );
		$instance['style'] = strip_tags( stripslashes( $new_instance['style'] ) );
		$instance['rssfeed'] = isset( $new_instance['rssfeed'] );
		$instance['rssimage'] = esc_attr( $new_instance['rssimage'] );

		return $instance;
	}

	/** Echo the settings update form
	 *
	 * @param array $instance Current settings
	 */
	function form($instance) {
		// displays the widget admin form
		$instance = wp_parse_args( ( array ) $instance, array ('title' => '', 'rssimage' => '' ) );

		// Prepare data for display
		$title = htmlspecialchars( $instance['title'], ENT_QUOTES );
		if ( !$amount = (int) $instance['amount'] ) {
			$amount = 5;
		}
		$count = ( bool ) $instance['count'];
		$sort_id = ($instance['sort_column'] == 'ID') ? ' SELECTED' : '';
		$sort_name = ($instance['sort_column'] == 'name') ? ' SELECTED' : '';
		$sort_count = ($instance['sort_column'] == 'count') ? ' SELECTED' : '';
		$sort_order_a = ($instance['sort_order'] == 'asc') ? ' SELECTED' : '';
		$sort_order_d = ($instance['sort_order'] == 'desc') ? ' SELECTED' : '';
		$style_list = ($instance['style'] == 'list') ? ' SELECTED' : '';
		$style_drop = ($instance['style'] == 'drop') ? ' SELECTED' : '';
		$rssfeed = ( bool ) $instance['rssfeed'];
		$rssimage = esc_attr( $instance['rssimage'], ENT_QUOTES );

		if ($amount < 1 ) {
			$amount = 1;
		}
		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'title' ) . '">';
		_e( 'Title:' );
		echo '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . $title . '" /> ';
		echo '</label>';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'amount' ) . '">';
		_e( 'How categories to show' );
		echo '</label>';
		echo '<input class="widefat" id="' . $this->get_field_id( 'amount' ) . '" name="' . $this->get_field_name( 'amount' ) . '" type="text" value="' . $amount . '" /> ';
		echo '</select>';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'count' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'count' ) . '"	name="' . $this->get_field_name( 'count' ) . '" ' . $this->core->isChecked( true, $count ) . ' /> ';
		_e( 'Show post counts' );
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'sort_column' ) . '">';
		_e( 'Sort by ' );
		echo '<select id="' . $this->get_field_id( 'sort_column' ) . '" name="' . $this->get_field_name( 'sort_column' ) . '"> ';
		echo '<option value="ID" ' . $sort_id . '>' . __( 'ID' ) . '</option>';
		echo '<option value="name" ' . $sort_name . '>' . __( 'Name' ) . '</option>';
		echo '<option value="count" ' . $sort_count . '>' . __( 'Count' ) . '</option>';
		echo '</select>';
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'sort_order' ) . '">';
		_e( 'Sort order ' );
		echo '<select id="' . $this->get_field_id( 'sort_order' ) . '"	name="' . $this->get_field_name( 'sort_order' ) . '"> ';
		echo '<option value="asc" ' . $sort_order_a . '>' . __( 'Ascending' ) . '</option>';
		echo '<option value="desc" ' . $sort_order_d . '>' . __( 'Descending' ) . '</option>';
		echo '</select>';
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'style' ) . '">';
		_e( 'Display style ' );
		echo '<select id="' . $this->get_field_id( 'style' ) . '" name="' . $this->get_field_name( 'style' ) . '"> ';
		echo '<option value="list" ' . $style_list . '>' . __( 'List' ) . '</option>';
		echo '<option value="drop" ' . $style_drop . '>' . __( 'Drop down' ) . '</option>';
		echo '</select>';
		echo '</label>';
		echo '<br />';
		echo '</p>';

		echo '<p>';

		echo '<label for="' . $this->get_field_id( 'rssfeed' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'rssfeed' ) . '" name="' . $this->get_field_name( 'rssfeed' ) . '" ' . $this->core->isChecked( true, $rssfeed ) . '/> ';
		_e( 'Show RSS Feed' );
		echo '</label>';
		echo '<br />';

		echo '<label for="">';
		_e( 'Full path to RSS image:' );
		echo '<input class="widefat" id="' . $this->get_field_id( 'rssimage' ) . '" name="' . $this->get_field_name( 'rssimage' ) . '" type="text" value="' . $rssimage . '" />';
		echo '</label>';
		echo '</p>';

		echo '<input type="hidden" id="' . $this->get_field_id( 'submit' ) . '" name="' . $this->get_field_name( 'submit' ) . '" value="1" />';
	}

}
/**
 * Class that will display the categories
 *
 */
class AVH_Walker_Category_Checklist extends Walker
{
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id' ); //TODO: decouple this
	var $number;
	var $input_id;
	var $input_name;
	var $li_id;

	function start_lvl ( &$output, $depth, $args )
	{
		$indent = str_repeat( "\t", $depth );
		$output .= "$indent<ul class='children'>\n";
	}

	function end_lvl ( &$output, $depth, $args )
	{
		$indent = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	function start_el ( &$output, $category, $depth, $args )
	{
		extract( $args );
		$this->input_id = $this->input_id.'-'.$category->term_id;
		$class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
		$output .= "\n<li id='$this->li_id'$class>" . '<label for="' . $this->input_id . '" class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="' . $this->input_name . '['.$category->term_id.']" id="' . $this->input_id . '"' . (in_array( $category->term_id, $selected_cats ) ? ' checked="checked"' : "") . '/> ' . wp_specialchars( apply_filters( 'the_category', $category->name ) ) . '</label>';
	}

	function end_el ( &$output, $category, $depth, $args )
	{
		$output .= "</li>\n";
	}
}
?>
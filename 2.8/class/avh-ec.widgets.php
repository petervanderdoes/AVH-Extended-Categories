<?php
/**
 * Widget Class for displaying categories. Extended version of the dfeault categories.
 *
 */
class WP_Widget_AVH_ExtendedCategories_Normal extends WP_Widget
{
	var $core;

	/**
	 * PHP 5 Constructor
	 *
	 */
	function __construct ()
	{
		$this->core = & AVH_EC_Singleton::getInstance( 'AVH_EC_Core' );

		//Convert the old option widget_extended_categories to widget_extended-categories
		$old = get_option( 'widget_extended_categories' );
		if ( ! (false === $old) ) {
			update_option( 'widget_extended-categories', $old );
			delete_option( 'widget_extended_categories' );
		}
		$widget_ops = array ('description' => __( "An extended version of the default Categories widget.", 'avh-ec' ) );
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

		$selectedonly = $instance['selectedonly'] ? 1 : 0;
		$c = $instance['count'] ? '1' : '0';
		$h = $instance['hierarchical'] ? 1 : 0;
		$d = $instance['depth'] ? $instance['depth'] : 0;
		$e = $instance['hide_empty'] ? '1' : '0';
		$s = $instance['sort_column'] ? $instance['sort_column'] : 'name';
		$o = $instance['sort_order'] ? $instance['sort_order'] : 'asc';
		$r = $instance['rssfeed'] ? 'RSS' : '';
		$i = $instance['rssimage'] ? $instance['rssimage'] : '';
		$invert = $instance['invert_included'] ? '1' : '0';

		if ( empty( $r ) ) {
			$i = '';
		}

		if ( empty( $d ) ) {
			$d = 0;
		}

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Categories', 'avh-ec' ) : $instance['title'] );
		$style = empty( $instance['style'] ) ? 'list' : $instance['style'];

		$included_cats = '';
		if ( $instance['post_category'] ) {
			$post_category = unserialize( $instance['post_category'] );
			$children = array ();
			if ( ! $instance['selectedonly'] ) {
				foreach ( $post_category as $cat_id ) {
					$children = array_merge( $children, get_term_children( $cat_id, 'category' ) );
				}
			}
			$included_cats = implode( ",", array_merge( $post_category, $children ) );
		}

		if ( $invert ) {
			$inc_exc = 'exclude';
		} else {
			$inc_exc = 'include';
		}

		$options = $this->core->getOptions();
		$show_option_none = __( 'Select Category', 'avh-ec' );
		if ( $options['general']['selectcategory'] ) {
			$show_option_none = $options['general']['selectcategory'];
		}

		$cat_args = array ($inc_exc => $included_cats, 'orderby' => $s, 'order' => $o, 'show_count' => $c, 'hide_empty' => $e, 'hierarchical' => $h, 'depth' => $d, 'title_li' => '', 'show_option_none' => $show_option_none, 'feed' => $r, 'feed_image' => $i, 'name' => 'extended-categories-select-' . $this->number );
		echo $before_widget;
		echo $this->core->comment;
		echo $before_title . $title . $after_title;

		if ( $style == 'list' ) {
			echo '<ul>';
			$this->core->avh_wp_list_categories( $cat_args, $selectedonly );
			echo '</ul>';
		} else {
			$this->core->avh_wp_dropdown_categories( $cat_args, $selectedonly );
			echo '<script type=\'text/javascript\'>' . "\n";
			echo '/* <![CDATA[ */' . "\n";
			echo '            var ec_dropdown_' . $this->number . ' = document.getElementById("extended-categories-select-' . $this->number . '");' . "\n";
			echo '            function ec_onCatChange_' . $this->number . '() {' . "\n";
			echo '                if ( ec_dropdown_' . $this->number . '.options[ec_dropdown_' . $this->number . '.selectedIndex].value > 0 ) {' . "\n";
			echo '                    location.href = "' . get_option( 'home' ) . '/?cat="+ec_dropdown_' . $this->number . '.options[ec_dropdown_' . $this->number . '.selectedIndex].value;' . "\n";
			echo '                }' . "\n";
			echo '            }' . "\n";
			echo '            ec_dropdown_' . $this->number . '.onchange = ec_onCatChange_' . $this->number . ';' . "\n";
			echo '/* ]]> */' . "\n";
			echo '</script>' . "\n";
		}
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
		$instance['selectedonly'] = $new_instance['selectedonly'] ? 1 : 0;
		$instance['count'] = $new_instance['count'] ? 1 : 0;
		$instance['hierarchical'] = $new_instance['hierarchical'] ? 1 : 0;
		$instance['hide_empty'] = $new_instance['hide_empty'] ? 1 : 0;
		$instance['sort_column'] = strip_tags( stripslashes( $new_instance['sort_column'] ) );
		$instance['sort_order'] = strip_tags( stripslashes( $new_instance['sort_order'] ) );
		$instance['style'] = strip_tags( stripslashes( $new_instance['style'] ) );
		$instance['rssfeed'] = $new_instance['rssfeed'] ? 1 : 0;
		$instance['rssimage'] = strip_tags( stripslashes( $new_instance['rssimage'] ) );
		if ( array_key_exists( 'all', $new_instance['post_category'] ) ) {
			$instance['post_category'] = false;
		} else {
			$instance['post_category'] = serialize( $new_instance['post_category'] );
		}
		$instance['depth'] = ( int ) $new_instance['depth'];
		if ( $instance['depth'] < 0 || 11 < $instance['depth'] ) {
			$instance['depth'] = 0;
		}
		$instance['invert_included'] = $new_instance['invert_included'] ? 1 : 0;

		// If only the selected categories are to be displayed, this implies a flat view. Can't be hierarchical
		if ( 1 == $new_instance['selectedonly'] ) {
			$instance['hierarchical'] = 0;
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
		$instance = wp_parse_args( ( array ) $instance, array ('title' => '', 'rssimage' => '', 'depth' => 0 ) );

		// Prepare data for display
		$title = esc_attr( $instance['title'] );
		$selectedonly = ( bool ) $instance['selectedonly'];
		$count = ( bool ) $instance['count'];
		$hierarchical = ( bool ) $instance['hierarchical'];
		$depth = ( int ) $instance['depth'];
		$hide_empty = ( bool ) $instance['hide_empty'];
		$sort_id = ($instance['sort_column'] == 'ID') ? ' SELECTED' : '';
		$sort_name = ($instance['sort_column'] == 'name') ? ' SELECTED' : '';
		$sort_count = ($instance['sort_column'] == 'count') ? ' SELECTED' : '';
		$sort_order_a = ($instance['sort_order'] == 'asc') ? ' SELECTED' : '';
		$sort_order_d = ($instance['sort_order'] == 'desc') ? ' SELECTED' : '';
		$style_list = ($instance['style'] == 'list') ? ' SELECTED' : '';
		$style_drop = ($instance['style'] == 'drop') ? ' SELECTED' : '';
		$rssfeed = ( bool ) $instance['rssfeed'];
		$rssimage = esc_attr( $instance['rssimage'] );
		$selected_cats = ($instance['post_category'] != '') ? unserialize( $instance['post_category'] ) : false;
		$invert_included = ( bool ) $instance['invert_included'];

		if ( $depth < 0 || 11 < $depth ) {
			$depth = 0;
		}

		if ( 1 == $selectedonly ) {
			$hierarchical = 0;
		}
		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'title' ) . '">';
		_e( 'Title', 'avh-ec' );
		echo '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . $title . '" /> ';
		echo '</label>';
		echo '</p>';

		echo '<p>';

		echo '<label for="' . $this->get_field_id( 'selectedonly' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'selectedonly' ) . '"	name="' . $this->get_field_name( 'selectedonly' ) . '" ' . $this->core->isChecked( true, $selectedonly ) . ' /> ';
		_e( 'Show selected categories only', 'avh-ec' );
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'count' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'count' ) . '"	name="' . $this->get_field_name( 'count' ) . '" ' . $this->core->isChecked( true, $count ) . ' /> ';
		_e( 'Show post counts', 'avh-ec' );
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'hierarchical' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'hierarchical' ) . '" name="' . $this->get_field_name( 'hierarchical' ) . '" ' . $this->core->isChecked( true, $hierarchical ) . ' /> ';
		_e( 'Show hierarchy', 'avh-ec' );
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'depth' ) . '">';
		_e( 'How many levels to show', 'avh-ec' );
		echo '</label>';
		echo '<select id="' . $this->get_field_id( 'depth' ) . '" name="' . $this->get_field_name( 'depth' ) . '"> ';
		echo '<option value="0" ' . (0 == $depth ? "selected='selected'" : '') . '>' . __( 'All Levels', 'avh-ec' ) . '</option>';
		echo '<option value="1" ' . (1 == $depth ? "selected='selected'" : '') . '>' . __( 'Toplevel only', 'avh-ec' ) . '</option>';
		for ( $i = 2; $i <= 11; $i ++ ) {
			echo '<option value="' . $i . '" ' . ($i == $depth ? "selected='selected'" : '') . '>' . __( 'Child ', 'avh-ec' ) . ($i - 1) . '</option>';
		}
		echo '</select>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'hide_empty' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'hide_empty' ) . '"	name="' . $this->get_field_name( 'hide_empty' ) . '" ' . $this->core->isChecked( true, $hide_empty ) . '/> ';
		_e( 'Hide empty categories', 'avh-ec' );
		echo '</label>';
		echo '<br />';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'sort_column' ) . '">';
		_e( 'Sort by ', 'avh-ec' );
		echo '<select id="' . $this->get_field_id( 'sort_column' ) . '" name="' . $this->get_field_name( 'sort_column' ) . '"> ';
		echo '<option value="ID" ' . $sort_id . '>' . __( 'ID', 'avh-ec' ) . '</option>';
		echo '<option value="name" ' . $sort_name . '>' . __( 'Name', 'avh-ec' ) . '</option>';
		echo '<option value="count" ' . $sort_count . '>' . __( 'Count', 'avh-ec' ) . '</option>';
		echo '</select>';
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'sort_order' ) . '">';
		_e( 'Sort order ', 'avh-ec' );
		echo '<select id="' . $this->get_field_id( 'sort_order' ) . '"	name="' . $this->get_field_name( 'sort_order' ) . '"> ';
		echo '<option value="asc" ' . $sort_order_a . '>' . __( 'Ascending', 'avh-ec' ) . '</option>';
		echo '<option value="desc" ' . $sort_order_d . '>' . __( 'Descending', 'avh-ec' ) . '</option>';
		echo '</select>';
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'style' ) . '">';
		_e( 'Display style ', 'avh-ec' );
		echo '<select id="' . $this->get_field_id( 'style' ) . '" name="' . $this->get_field_name( 'style' ) . '"> ';
		echo '<option value="list" ' . $style_list . '>' . __( 'List', 'avh-ec' ) . '</option>';
		echo '<option value="drop" ' . $style_drop . '>' . __( 'Drop down', 'avh-ec' ) . '</option>';
		echo '</select>';
		echo '</label>';
		echo '<br />';
		echo '</p>';

		echo '<p>';

		echo '<label for="' . $this->get_field_id( 'rssfeed' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'rssfeed' ) . '" name="' . $this->get_field_name( 'rssfeed' ) . '" ' . $this->core->isChecked( true, $rssfeed ) . '/> ';
		_e( 'Show RSS Feed', 'avh-ec' );
		echo '</label>';
		echo '<br />';

		echo '<label for="">';
		_e( 'Path (URI) to RSS image', 'avh-ec' );
		echo '<input class="widefat" id="' . $this->get_field_id( 'rssimage' ) . '" name="' . $this->get_field_name( 'rssimage' ) . '" type="text" value="' . $rssimage . '" />';
		echo '</label>';
		echo '</p>';

		echo '<p>';
		echo '<b>' . __( 'Select categories', 'avh-ec' ) . '</b><hr />';
		echo '<ul id="categorychecklist" class="list:category categorychecklist form-no-clear" style="list-style-type: none; margin-left: 5px; padding-left: 0px; margin-bottom: 20px;">';
		echo '<li id="' . $this->get_field_id( 'category--1' ) . '" class="popular-category">';
		echo '<label for="' . $this->get_field_id( 'post_category' ) . '" class="selectit">';
		echo '<input value="all" id="' . $this->get_field_id( 'post_category' ) . '" name="' . $this->get_field_name( 'post_category' ) . '[all]" type="checkbox" ' . $this->core->isChecked( false, $selected_cats ) . '> ';
		_e( 'All Categories', 'avh-ec' );
		echo '</label>';
		echo '</li>';
		$this->avh_wp_category_checklist( 0, 0, $selected_cats, false, $this->number, 1 );
		echo '</ul>';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'invert_included' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'invert_included' ) . '"     name="' . $this->get_field_name( 'invert_included' ) . '" ' . $this->core->isChecked( true, $invert_included ) . '/> ';
		_e( 'Exclude the selected categories', 'avh-ec' );
		echo '</label>';
		echo '<br />';
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
	function avh_wp_category_checklist ( $post_id = 0, $descendants_and_self = 0, $selected_cats = false, $popular_cats = false, $number, $display = 1 )
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
		$all_categories = $categories;

		// Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
		$checked_categories = array ();
		for ( $i = 0; isset( $categories[$i] ); $i ++ ) {
			if ( in_array( $categories[$i]->term_id, $args['selected_cats'] ) ) {
				$checked_categories[] = $categories[$i];
				unset( $categories[$i] );
			}
		}

		if ( 1 == $display ) {
			// Put checked cats on top
			echo call_user_func_array( array (&$walker, 'walk' ), array ($checked_categories, 0, $args ) );
			// Then the rest of them
			echo call_user_func_array( array (&$walker, 'walk' ), array ($categories, 0, $args ) );
		} else {
			return ($all_categories);
		}
	}
}

/**
 * Widget Class for displaying the top categories
 *
 */
class WP_Widget_AVH_ExtendedCategories_Top extends WP_Widget
{
	var $core;

	/**
	 * PHP 5 Constructor
	 *
	 */
	function __construct ()
	{
		$this->core = & AVH_EC_Singleton::getInstance( 'AVH_EC_Core' );

		$widget_ops = array ('description' => __( "Shows the top categories.", 'avh-ec' ) );
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
	function widget ( $args, $instance )
	{

		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Categories', 'avh-ec' ) : $instance['title'] );
		$style = empty( $instance['style'] ) ? 'list' : $instance['style'];
		if ( ! $a = ( int ) $instance['amount'] ) {
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

		$options = $this->core->getOptions();
		$show_option_none = __( 'Select Category', 'avh-ec' );
		if ( $options['general']['selectcategory'] ) {
			$show_option_none = $options['general']['selectcategory'];
		}

		$top_cats = get_terms( 'category', array ('fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'number' => $a, 'hierarchical' => false ) );
		$included_cats = implode( ",", $top_cats );

		$cat_args = array ('include' => $included_cats, 'orderby' => $s, 'order' => $o, 'show_count' => $c, 'hide_empty' => 0, 'hierarchical' => 0, 'depth' => - 1, 'title_li' => '', 'show_option_none' => $show_option_none, 'feed' => $r, 'feed_image' => $i, 'name' => 'extended-categories-top-select-' . $this->number );
		echo $before_widget;
		echo $this->core->comment;
		echo $before_title . $title . $after_title;
		echo '<ul>';

		if ( $style == 'list' ) {
			wp_list_categories( $cat_args );
		} else {
			wp_dropdown_categories( $cat_args );
			echo '<script type=\'text/javascript\'>' . "\n";
			echo '/* <![CDATA[ */' . "\n";
			echo '            var ec_dropdown_top_' . $this->number . ' = document.getElementById("extended-categories-top-select-' . $this->number . '");' . "\n";
			echo '            function ec_top_onCatChange_' . $this->number . '() {' . "\n";
			echo '                if ( ec_dropdown_top_' . $this->number . '.options[ec_dropdown_top_' . $this->number . '.selectedIndex].value > 0 ) {' . "\n";
			echo '                    location.href = "' . get_option( 'home' ) . '/?cat="+ec_dropdown_top_' . $this->number . '.options[ec_dropdown_top_' . $this->number . '.selectedIndex].value;' . "\n";
			echo '                }' . "\n";
			echo '            }' . "\n";
			echo '            ec_dropdown_top_' . $this->number . '.onchange = ec_top_onCatChange_' . $this->number . ';' . "\n";
			echo '/* ]]> */' . "\n";
			echo '</script>' . "\n";
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

		$instance['title'] = strip_tags( stripslashes( $new_instance['title'] ) );
		$instance['amount'] = ( int ) $new_instance['amount'];
		$instance['count'] = $new_instance['count'] ? 1 : 0;
		$instance['sort_column'] = strip_tags( stripslashes( $new_instance['sort_column'] ) );
		$instance['sort_order'] = strip_tags( stripslashes( $new_instance['sort_order'] ) );
		$instance['style'] = strip_tags( stripslashes( $new_instance['style'] ) );
		$instance['rssfeed'] = $new_instance['rssfeed'] ? 1 : 0;
		$instance['rssimage'] = strip_tags( stripslashes( $new_instance['rssimage'] ) );

		return $instance;
	}

	/** Echo the settings update form
	 *
	 * @param array $instance Current settings
	 */
	function form ( $instance )
	{
		// displays the widget admin form
		$instance = wp_parse_args( ( array ) $instance, array ('title' => '', 'rssimage' => '' ) );

		// Prepare data for display
		$title = esc_attr( $instance['title'] );
		if ( ! $amount = ( int ) $instance['amount'] ) {
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
		$rssimage = esc_attr( $instance['rssimage'] );

		if ( $amount < 1 ) {
			$amount = 1;
		}
		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'title' ) . '">';
		_e( 'Title', 'avh-ec' );
		echo '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . $title . '" /> ';
		echo '</label>';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'amount' ) . '">';
		_e( 'How many categories to show', 'avh-ec' );
		echo '</label>';
		echo '<input class="widefat" id="' . $this->get_field_id( 'amount' ) . '" name="' . $this->get_field_name( 'amount' ) . '" type="text" value="' . $amount . '" /> ';
		echo '</select>';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'count' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'count' ) . '"	name="' . $this->get_field_name( 'count' ) . '" ' . $this->core->isChecked( true, $count ) . ' /> ';
		_e( 'Show post counts', 'avh-ec' );
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'sort_column' ) . '">';
		_e( 'Sort by ', 'avh-ec' );
		echo '<select id="' . $this->get_field_id( 'sort_column' ) . '" name="' . $this->get_field_name( 'sort_column' ) . '"> ';
		echo '<option value="ID" ' . $sort_id . '>' . __( 'ID', 'avh-ec' ) . '</option>';
		echo '<option value="name" ' . $sort_name . '>' . __( 'Name', 'avh-ec' ) . '</option>';
		echo '<option value="count" ' . $sort_count . '>' . __( 'Count', 'avh-ec' ) . '</option>';
		echo '</select>';
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'sort_order' ) . '">';
		_e( 'Sort order ', 'avh-ec' );
		echo '<select id="' . $this->get_field_id( 'sort_order' ) . '"	name="' . $this->get_field_name( 'sort_order' ) . '"> ';
		echo '<option value="asc" ' . $sort_order_a . '>' . __( 'Ascending', 'avh-ec' ) . '</option>';
		echo '<option value="desc" ' . $sort_order_d . '>' . __( 'Descending', 'avh-ec' ) . '</option>';
		echo '</select>';
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'style' ) . '">';
		_e( 'Display style ', 'avh-ec' );
		echo '<select id="' . $this->get_field_id( 'style' ) . '" name="' . $this->get_field_name( 'style' ) . '"> ';
		echo '<option value="list" ' . $style_list . '>' . __( 'List', 'avh-ec' ) . '</option>';
		echo '<option value="drop" ' . $style_drop . '>' . __( 'Drop down', 'avh-ec' ) . '</option>';
		echo '</select>';
		echo '</label>';
		echo '<br />';
		echo '</p>';

		echo '<p>';

		echo '<label for="' . $this->get_field_id( 'rssfeed' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'rssfeed' ) . '" name="' . $this->get_field_name( 'rssfeed' ) . '" ' . $this->core->isChecked( true, $rssfeed ) . '/> ';
		_e( 'Show RSS Feed', 'avh-ec' );
		echo '</label>';
		echo '<br />';

		echo '<label for="">';
		_e( 'Path (URI) to RSS image', 'avh-ec' );
		echo '<input class="widefat" id="' . $this->get_field_id( 'rssimage' ) . '" name="' . $this->get_field_name( 'rssimage' ) . '" type="text" value="' . $rssimage . '" />';
		echo '</label>';
		echo '</p>';

		echo '<input type="hidden" id="' . $this->get_field_id( 'submit' ) . '" name="' . $this->get_field_name( 'submit' ) . '" value="1" />';
	}

}

/**
 * Widget Class for displaying the grouped categories
 *
 */
class WP_Widget_AVH_ExtendedCategories_Grouped extends WP_Widget
{
	var $core;

	/**
	 * PHP 5 Constructor
	 *
	 */
	function __construct ()
	{
		$this->core = & AVH_EC_Singleton::getInstance( 'AVH_EC_Core' );

		$widget_ops = array ('description' => __( "Shows grouped categories.", 'avh-ec' ) );
		WP_Widget::__construct( false, __( 'AVH Extended Categories Grouped' ), $widget_ops );
	}

	function WP_Widget_AVH_ExtendedCategories_Grouped ()
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
		global $post;
		$catgrp = new AVH_EC_Category_Group();
		extract( $args );

		$c = $instance['count'] ? '1' : '0';
		$e = $instance['hide_empty'] ? '1' : '0';
		$s = $instance['sort_column'] ? $instance['sort_column'] : 'name';
		$o = $instance['sort_order'] ? $instance['sort_order'] : 'asc';
		$r = $instance['rssfeed'] ? 'RSS' : '';
		$i = $instance['rssimage'] ? $instance['rssimage'] : '';

		if ( empty( $r ) ) {
			$i = '';
		}

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Categories', 'avh-ec' ) : $instance['title'] );
		$style = empty( $instance['style'] ) ? 'list' : $instance['style'];

		$options = $this->core->getOptions();

		if ( is_home() ) {
			$row = get_term_by( 'name', 'home', $catgrp->taxonomy_name );
		} else {
			$row = wp_get_object_terms( $post->ID, $catgrp->taxonomy_name );
		}

		if ( empty( $row ) ) { // There is no group associated with the post
			$options = $this->core->options;
			$no_cat_group = $options['cat-group']['nogroup'];
			$row = get_term_by( 'id', $no_cat_group, $catgrp->taxonomy_name );
		}

		if ( ! ('none' == $row->name) ) {
			$groupid = $row->term_id;
			$cats = $catgrp->getCategoriesFromGroup( $groupid );
			$included_cats = implode( ',', $cats );

			$show_option_none = __( 'Select Category', 'avh-ec' );
			if ( $options['general']['selectcategory'] ) {
				$show_option_none = $options['general']['selectcategory'];
			}

			$cat_args = array ('include' => $included_cats, 'orderby' => $s, 'order' => $o, 'show_count' => $c, 'hide_empty' => $e, 'hierarchical' => false, 'title_li' => '', 'show_option_none' => $show_option_none, 'feed' => $r, 'feed_image' => $i, 'name' => 'extended-categories-select-' . $this->number );
			echo $before_widget;
			echo $this->core->comment;
			echo $before_title . $title . $after_title;

			if ( $style == 'list' ) {
				echo '<ul>';
				$this->core->avh_wp_list_categories( $cat_args, TRUE );
				echo '</ul>';
			} else {
				$this->core->avh_wp_dropdown_categories( $cat_args, TRUE );
				echo '<script type=\'text/javascript\'>' . "\n";
				echo '/* <![CDATA[ */' . "\n";
				echo '            var ec_dropdown_' . $this->number . ' = document.getElementById("extended-categories-select-' . $this->number . '");' . "\n";
				echo '            function ec_onCatChange_' . $this->number . '() {' . "\n";
				echo '                if ( ec_dropdown_' . $this->number . '.options[ec_dropdown_' . $this->number . '.selectedIndex].value > 0 ) {' . "\n";
				echo '                    location.href = "' . get_option( 'home' ) . '/?cat="+ec_dropdown_' . $this->number . '.options[ec_dropdown_' . $this->number . '.selectedIndex].value;' . "\n";
				echo '                }' . "\n";
				echo '            }' . "\n";
				echo '            ec_dropdown_' . $this->number . '.onchange = ec_onCatChange_' . $this->number . ';' . "\n";
				echo '/* ]]> */' . "\n";
				echo '</script>' . "\n";
			}
			echo $after_widget;
		}
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
		$instance['count'] = $new_instance['count'] ? 1 : 0;
		$instance['hide_empty'] = $new_instance['hide_empty'] ? 1 : 0;
		$instance['sort_column'] = strip_tags( stripslashes( $new_instance['sort_column'] ) );
		$instance['sort_order'] = strip_tags( stripslashes( $new_instance['sort_order'] ) );
		$instance['style'] = strip_tags( stripslashes( $new_instance['style'] ) );
		$instance['rssfeed'] = $new_instance['rssfeed'] ? 1 : 0;
		$instance['rssimage'] = strip_tags( stripslashes( $new_instance['rssimage'] ) );
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
		$instance = wp_parse_args( ( array ) $instance, array ('title' => '', 'rssimage' => '' ) );

		// Prepare data for display
		$title = esc_attr( $instance['title'] );
		$count = ( bool ) $instance['count'];
		$hide_empty = ( bool ) $instance['hide_empty'];
		$sort_id = ($instance['sort_column'] == 'ID') ? ' SELECTED' : '';
		$sort_name = ($instance['sort_column'] == 'name') ? ' SELECTED' : '';
		$sort_count = ($instance['sort_column'] == 'count') ? ' SELECTED' : '';
		$sort_order_a = ($instance['sort_order'] == 'asc') ? ' SELECTED' : '';
		$sort_order_d = ($instance['sort_order'] == 'desc') ? ' SELECTED' : '';
		$style_list = ($instance['style'] == 'list') ? ' SELECTED' : '';
		$style_drop = ($instance['style'] == 'drop') ? ' SELECTED' : '';
		$rssfeed = ( bool ) $instance['rssfeed'];
		$rssimage = esc_attr( $instance['rssimage'] );

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'title' ) . '">';
		_e( 'Title', 'avh-ec' );
		echo '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . $title . '" /> ';
		echo '</label>';
		echo '</p>';

		echo '<p>';

		echo '<label for="' . $this->get_field_id( 'count' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'count' ) . '"	name="' . $this->get_field_name( 'count' ) . '" ' . $this->core->isChecked( true, $count ) . ' /> ';
		_e( 'Show post counts', 'avh-ec' );
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'hierarchical' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'hierarchical' ) . '" name="' . $this->get_field_name( 'hierarchical' ) . '" ' . $this->core->isChecked( true, $hierarchical ) . ' /> ';
		_e( 'Show hierarchy', 'avh-ec' );
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'hide_empty' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'hide_empty' ) . '"	name="' . $this->get_field_name( 'hide_empty' ) . '" ' . $this->core->isChecked( true, $hide_empty ) . '/> ';
		_e( 'Hide empty categories', 'avh-ec' );
		echo '</label>';
		echo '<br />';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'sort_column' ) . '">';
		_e( 'Sort by ', 'avh-ec' );
		echo '<select id="' . $this->get_field_id( 'sort_column' ) . '" name="' . $this->get_field_name( 'sort_column' ) . '"> ';
		echo '<option value="ID" ' . $sort_id . '>' . __( 'ID', 'avh-ec' ) . '</option>';
		echo '<option value="name" ' . $sort_name . '>' . __( 'Name', 'avh-ec' ) . '</option>';
		echo '<option value="count" ' . $sort_count . '>' . __( 'Count', 'avh-ec' ) . '</option>';
		echo '</select>';
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'sort_order' ) . '">';
		_e( 'Sort order ', 'avh-ec' );
		echo '<select id="' . $this->get_field_id( 'sort_order' ) . '"	name="' . $this->get_field_name( 'sort_order' ) . '"> ';
		echo '<option value="asc" ' . $sort_order_a . '>' . __( 'Ascending', 'avh-ec' ) . '</option>';
		echo '<option value="desc" ' . $sort_order_d . '>' . __( 'Descending', 'avh-ec' ) . '</option>';
		echo '</select>';
		echo '</label>';
		echo '<br />';

		echo '<label for="' . $this->get_field_id( 'style' ) . '">';
		_e( 'Display style ', 'avh-ec' );
		echo '<select id="' . $this->get_field_id( 'style' ) . '" name="' . $this->get_field_name( 'style' ) . '"> ';
		echo '<option value="list" ' . $style_list . '>' . __( 'List', 'avh-ec' ) . '</option>';
		echo '<option value="drop" ' . $style_drop . '>' . __( 'Drop down', 'avh-ec' ) . '</option>';
		echo '</select>';
		echo '</label>';
		echo '<br />';
		echo '</p>';

		echo '<p>';

		echo '<label for="' . $this->get_field_id( 'rssfeed' ) . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'rssfeed' ) . '" name="' . $this->get_field_name( 'rssfeed' ) . '" ' . $this->core->isChecked( true, $rssfeed ) . '/> ';
		_e( 'Show RSS Feed', 'avh-ec' );
		echo '</label>';
		echo '<br />';

		echo '<label for="">';
		_e( 'Path (URI) to RSS image', 'avh-ec' );
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
		$output .= $indent . '<ul class="children">' . "\n";
	}

	function end_lvl ( &$output, $depth, $args )
	{
		$indent = str_repeat( "\t", $depth );
		$output .= $indent . '</ul>' . "\n";
	}

	function start_el ( &$output, $category, $depth, $args )
	{
		extract( $args );
		$this->input_id = $this->input_id . '-' . $category->term_id;
		$class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
		$output .= "\n" . '<li id="' . $this->li_id . '"' . $class . '>';
		$output .= '<label for="' . $this->input_id . '" class="selectit">';
		$output .= '<input value="' . $category->term_id . '" type="checkbox" name="' . $this->input_name . '[' . $category->term_id . ']" id="' . $this->input_id . '"' . (in_array( $category->term_id, $selected_cats ) ? ' checked="checked"' : "") . '/> ' . wp_specialchars( apply_filters( 'the_category', $category->name ) ) . '</label>';
	}

	function end_el ( &$output, $category, $depth, $args )
	{
		$output .= "</li>\n";
	}
}
?>
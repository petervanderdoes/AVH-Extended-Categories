<?php
class AVH_EC_Admin
{
	var $core;
	var $hooks = array ();
	var $message;

	function __construct ()
	{

		// Initialize the plugin
		$this->core = & AVH_EC_Singleton::getInstance('AVH_EC_Core');

		// Admin menu
		add_action( 'admin_menu', array (&$this, 'actionAdminMenu' ) );
		add_filter( 'plugin_action_links_extended-categories-widget/widget_extended_categories.php', array (&$this, 'filterPluginActions' ), 10, 2 );

		// Register Style and Scripts
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.dev' : '';
		wp_register_style( 'avhec-admin-css', $this->core->info['plugin_url'] . '/inc/avh-ec.admin.css', array (), $this->core->version, 'screen' );

		add_meta_box( 'groupcat_box_ID', __( 'Category Group', 'avh-ec' ), array (&$this, 'metaboxGroupCat' ), 'post', 'side', 'core' );
		add_meta_box( 'groupcat_box_ID', __( 'Category Group', 'avh-ec' ), array (&$this, 'metaboxGroupCat' ), 'page', 'side', 'core' );

		add_action( 'save_post', array (&$this, 'actionSaveGroupCatTaxonomy' ) );

		return;
	}

	function AVH_EC_Admin ()
	{
		$this->__construct();
	}

	// This function gets called in edit-form-advanced.php
	function metaboxGroupCat ( $post )
	{

		echo '<p id=\'avhec-groupcat\'';

		echo '<input type="hidden" name="avhec_groupcat_nonce" id="avhec_groupcat_nonce" value="' . wp_create_nonce( 'avhec_groupcat_nonce' ) . '" />';

		// Get all theme taxonomy terms
		$groupcats = get_terms( 'groupcat', 'hide_empty=0' );

		echo ' <select name=\'post_avhec_groupcat\' id=\'post_avhec_groupcat\' class=\'postform\'>';
		$current_groupcat = wp_get_object_terms( $post->ID, 'groupcat' );

		foreach ( $groupcats as $groupcat ) {
			$name = ucwords( $groupcat->name );
			if ( ! is_wp_error( $current_groupcat ) && ! empty( $current_groupcat ) && ! strcmp( $groupcat->name, $current_groupcat[0]->name ) ) {
				echo '<option value="' . $groupcat->name . '" selected=\'selected\'>' . $name . "</option>\n";
			} else {
				if ( empty( $current_groupcat ) && 'none' == $groupcat->name ) {
					echo '<option value="' . $groupcat->name . '" selected=\'selected\'>' . $name . "</option>\n";
				} else {
					echo '<option value="' . $groupcat->name . '">' . $name . "</option>\n";
				}
			}
		}
		echo '</select>';
		echo '</p>';
	}

	function actionSaveGroupCatTaxonomy ( $post_id )
	{
		if ( ! wp_verify_nonce( $_POST['avhec_groupcat_nonce'], 'avhec_groupcat_nonce' ) ) {
			return $post_id;
		}

		// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// Check permissions
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		// OK, we're authenticated: we need to find and save the data
		$groupcat = $_POST['post_avhec_groupcat'];
		wp_set_object_terms( $post_id, $groupcat, 'groupcat' );

		return $groupcat;

	}

	/**
	 * Add the Tools and Options to the Management and Options page repectively
	 *
	 * @WordPress Action admin_menu
	 *
	 */
	function actionAdminMenu ()
	{

		// Add menu system
		$folder = $this->core->getBaseDirectory( plugin_basename( $this->core->info['plugin_dir'] ) );
		add_menu_page( 'AVH Extended Categories', 'AVH Extended Categories', 'manage_options', $folder, array (&$this, 'doMenuOverview' ) );
		$this->hooks['avhec_menu_overview'] = add_submenu_page( $folder, 'AVH Extended Categories: ' . __( 'Overview', 'avh-ec' ), __( 'Overview', 'avh-ec' ), 'manage_options', $folder, array (&$this, 'doMenuOverview' ) );
		$this->hooks['avhec_menu_general'] = add_submenu_page( $folder, 'AVH Extended Categories: ' . __( 'General Options', 'avh-ec' ), __( 'General Options', 'avh-ec' ), 'manage_options', 'avhec-general', array (&$this, 'doMenuGeneral' ) );
		$this->hooks['avhec_menu_grouped'] = add_submenu_page( $folder, 'AVH Extended Categories: ' . __( 'Group Categories', 'avh-ec' ), __( 'Group Categories', 'avh-ec' ), 'manage_options', 'avhec-grouped', array (&$this, 'doMenuGrouped' ) );
		$this->hooks['avhec_menu_faq'] = add_submenu_page( $folder, 'AVH Extended Categories:' . __( 'F.A.Q', 'avh-ec' ), __( 'F.A.Q', 'avh-ec' ), 'manage_options', 'avhec-faq', array (&$this, 'doMenuFAQ' ) );

		// Add actions for menu pages
		add_action( 'load-' . $this->hooks['avhec_menu_overview'], array (&$this, 'actionLoadPageHook_Overview' ) );
		add_action( 'load-' . $this->hooks['avhec_menu_general'], array (&$this, 'actionLoadPageHook_General' ) );
		add_action( 'load-' . $this->hooks['avhec_menu_grouped'], array (&$this, 'actionLoadPageHook_Grouped' ) );
		add_action( 'load-' . $this->hooks['avhec_menu_faq'], array (&$this, 'actionLoadPageHook_faq' ) );
	}

	function actionLoadPageHook_Overview ()
	{
		// Add metaboxes


		add_filter( 'screen_layout_columns', array (&$this, 'filterScreenLayoutColumns' ), 10, 2 );

		// WordPress core Styles and Scripts
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
		wp_admin_css( 'css/dashboard' );

		// Plugin Style and Scripts
		wp_enqueue_style( 'avhec-admin-css' );
	}

	function doMenuOverview ()
	{
		global $screen_layout_columns;

		// This box can't be unselectd in the the Screen Options
		add_meta_box( 'avhecBoxDonations', 'Donations', array (&$this, 'metaboxDonations' ), $this->hooks['avhec_menu_overview'], 'side', 'core' );
		$hide2 = '';
		switch ( $screen_layout_columns )
		{
			case 2 :
				$width = 'width:49%;';
				break;
			default :
				$width = 'width:98%;';
				$hide2 = 'display:none;';
		}

		echo '<div class="wrap avhec-wrap">';
		echo $this->displayIcon( 'index' );
		echo '<h2>' . 'AVH Extended Categories - ' . __( 'Overview', 'avh-ec' ) . '</h2>';

		echo '	<div id="dashboard-widgets-wrap">';
		echo '		<div id="dashboard-widgets" class="metabox-holder">';
		echo '		<div class="postbox-container" style="' . $width . '">' . "\n";
		do_meta_boxes( $this->hooks['avhec_menu_overview'], 'normal', $data );
		echo "			</div>";
		echo '			<div class="postbox-container" style="' . $hide2 . $width . '">' . "\n";
		do_meta_boxes( $this->hooks['avhec_menu_overview'], 'side', $data );
		echo '			</div>';
		echo '		</div>';

		echo '<br class="clear"/>';
		echo '	</div>'; //dashboard-widgets-wrap
		echo '</div>'; // wrap


		$this->printMetaboxGeneralNonces();
		$this->printMetaboxJS( 'overview' );
		$this->printAdminFooter();
	}

	function actionLoadPageHook_General ()
	{
		// Add metaboxes
		add_meta_box( 'avhecBoxOptions', __( 'Options', 'avh-ec' ), array (&$this, 'metaboxOptions' ), $this->hooks['avhec_menu_general'], 'normal', 'core' );

		add_filter( 'screen_layout_columns', array (&$this, 'filterScreenLayoutColumns' ), 10, 2 );

		// WordPress core Styles and Scripts
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
		wp_admin_css( 'css/dashboard' );

		// Plugin Style and Scripts
		wp_enqueue_style( 'avhec-admin-css' );
	}

	function doMenuGeneral ()
	{
		global $screen_layout_columns;

		$options_general[] = array ('avhec[general][selectcategory]', '<em>Select Category</em> Alternative', 'text', 20, 'Alternative text for Select Category.' );
		if ( isset( $_POST['updateoptions'] ) ) {
			check_admin_referer( 'avh_ec_generaloptions' );

			$formoptions = $_POST['avhec'];
			$options = $this->core->getOptions();

			//$all_data = array_merge( $options_general );
			$all_data = $options_general;
			foreach ( $all_data as $option ) {
				$section = substr( $option[0], strpos( $option[0], '[' ) + 1 );
				$section = substr( $section, 0, strpos( $section, '][' ) );
				$option_key = rtrim( $option[0], ']' );
				$option_key = substr( $option_key, strpos( $option_key, '][' ) + 2 );

				switch ( $section )
				{
					case 'general' :
						$current_value = $options[$section][$option_key];
						break;
				}
				// Every field in a form is set except unchecked checkboxes. Set an unchecked checkbox to 0.
				$newval = (isset( $formoptions[$section][$option_key] ) ? attribute_escape( $formoptions[$section][$option_key] ) : 0);
				if ( $newval != $current_value ) { // Only process changed fields.
					switch ( $section )
					{
						case 'general' :
							$options[$section][$option_key] = $newval;
							break;
					}
				}
			}
			$this->core->saveOptions( $options );
			$this->message = __( 'Options saved', 'avh-ec' );
			$this->status = 'updated fade';

		}
		$this->displayMessage();

		$actual_options = $this->core->getOptions();

		$hide2 = '';
		switch ( $screen_layout_columns )
		{
			case 2 :
				$width = 'width:49%;';
				break;
			default :
				$width = 'width:98%;';
				$hide2 = 'display:none;';
		}
		$data['options_general'] = $options_general;
		$data['actual_options'] = $actual_options;

		// This box can't be unselectd in the the Screen Options
		add_meta_box( 'avhecBoxDonations', 'Donations', array (&$this, 'metaboxDonations' ), $this->hooks['avhec_menu_general'], 'side', 'core' );
		$hide2 = '';
		switch ( $screen_layout_columns )
		{
			case 2 :
				$width = 'width:49%;';
				break;
			default :
				$width = 'width:98%;';
				$hide2 = 'display:none;';
		}

		echo '<div class="wrap avhec-wrap">';
		echo $this->displayIcon( 'index' );
		echo '<h2>' . 'AVH Extended Categories - ' . __( 'General Options', 'avh-ec' ) . '</h2>';
		$admin_base_url = $this->core->info['siteurl'] . '/wp-admin/admin.php?page=';
		echo '<form name="avhec-generaloptions" id="avhec-generaloptions" method="POST" action="' . $admin_base_url . 'avhec_options' . '" accept-charset="utf-8" >';
		wp_nonce_field( 'avh_ec_generaloptions' );

		echo '	<div id="dashboard-widgets-wrap">';
		echo '		<div id="dashboard-widgets" class="metabox-holder">';
		echo '		<div class="postbox-container" style="' . $width . '">' . "\n";
		do_meta_boxes( $this->hooks['avhec_menu_general'], 'normal', $data );
		echo "			</div>";
		echo '			<div class="postbox-container" style="' . $hide2 . $width . '">' . "\n";
		do_meta_boxes( $this->hooks['avhec_menu_general'], 'side', $data );
		echo '			</div>';
		echo '		</div>';

		echo '<br class="clear"/>';
		echo '	</div>'; //dashboard-widgets-wrap
		echo '<p class="submit"><input	class="button-primary"	type="submit" name="updateoptions" value="' . __( 'Save Changes', 'avhf-ec' ) . '" /></p>';
		echo '</form>';

		echo '</div>'; // wrap


		$this->printMetaboxGeneralNonces();
		$this->printMetaboxJS( 'general' );
		$this->printAdminFooter();
	}

	function actionLoadPageHook_Grouped ()
	{
		// Add metaboxes
		add_meta_box( 'avhecBoxGroupedAdd', __( 'Add Group', 'avh-ec' ), array (&$this, 'metaboxGroupedAdd' ), $this->hooks['avhec_menu_grouped'], 'normal', 'core' );

		add_filter( 'screen_layout_columns', array (&$this, 'filterScreenLayoutColumns' ), 10, 2 );

		// WordPress core Styles and Scripts
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
		wp_admin_css( 'css/dashboard' );

		// Plugin Style and Scripts
		wp_enqueue_style( 'avhec-admin-css' );
	}

	function doMenuGrouped ()
	{
		global $screen_layout_columns;
		$cat_name_new = '';

		$options_add_group[] = array ('avhec_cat_groups[add][name]', ' Group Name', 'text', 20, 'Category group name.' );

		if ( isset( $_POST['addgroup'] ) ) {
			check_admin_referer( 'avh_ec_addgroup' );

			$formoptions = $_POST['avhec_cat_groups_add'];
			$cat_groups = $this->core->getCatGroups();

			$duplicate = false;
			$cat_name_new = strtolower( $formoptions['add']['name'] );
			foreach ( $cat_groups as $cat_group ) {
				if ( $cat_name_new == strtolower( $cat_group['name'] ) ) {
					$duplicate = true;
					break;
				}
			}

			if ( ! $duplicate ) {
				$this->core->setCatGroupRow( $cat_name_new, '' );
				$cat_groups[] = $this->core->cat_group_row;

				$this->core->saveCatGroups( $cat_groups );
				$this->message = __( 'Category group saved', 'avh-ec' );
				$this->status = 'updated fade';
				$cat_name_new = '';
			} else {
				$this->message = __( 'Category group already exists', 'avh-ec' );
				$this->status = 'error';
			}
			$this->displayMessage();
		}

		$hide2 = '';
		switch ( $screen_layout_columns )
		{
			case 2 :
				$width = 'width:49%;';
				break;
			default :
				$width = 'width:98%;';
				$hide2 = 'display:none;';
		}

		$data_add_group['add'] = array ('name' => $cat_name_new );
		$data['add'] = array ('form' => $options_add_group, 'data' => $data_add_group );

		// This box can't be unselectd in the the Screen Options
		add_meta_box( 'avhecBoxDonations', __( 'Donations', 'avh-ec' ), array (&$this, 'metaboxDonations' ), $this->hooks['avhec_menu_grouped'], 'side', 'core' );

		echo '<div class="wrap avhec-wrap">';
		echo $this->displayIcon( 'index' );
		echo '<h2>' . 'AVH Extended Categories - ' . __( 'Grouped Categories', 'avh-ec' ) . '</h2>';
		$admin_base_url = $this->core->info['siteurl'] . '/wp-admin/admin.php?page=';

		echo '	<div id="dashboard-widgets-wrap">';
		echo '		<div id="dashboard-widgets" class="metabox-holder">';
		echo '		<div class="postbox-container" style="' . $width . '">' . "\n";
		do_meta_boxes( $this->hooks['avhec_menu_grouped'], 'normal', $data );
		echo "			</div>";
		echo '			<div class="postbox-container" style="' . $hide2 . $width . '">' . "\n";
		do_meta_boxes( $this->hooks['avhec_menu_grouped'], 'side', $data );
		echo '			</div>';
		echo '		</div>';

		echo '<br class="clear"/>';
		echo '	</div>'; //dashboard-widgets-wrap


		echo '</div>'; // wrap


		$this->printMetaboxGeneralNonces();
		$this->printMetaboxJS( 'grouped' );
		$this->printAdminFooter();
	}

	function metaboxGroupedAdd ( $data )
	{
		echo '<form name="avhec-addgroup" id="avhec-addgroup" method="POST" action="" accept-charset="utf-8" >';
		wp_nonce_field( 'avh_ec_addgroup' );
		echo $this->printOptions( $data['add']['form'], $data['add']['data'] );
		echo '<p class="submit"><input	class="button-primary"	type="submit" name="addgroup" value="' . __( 'Save Changes', 'avhfdas' ) . '" /></p>';
		echo '</form>';
	}

	/**
	 * Setup everything needed for the FAQ page
	 *
	 */
	function actionLoadPageHook_faq ()
	{

		add_meta_box( 'avhecBoxFAQ', __( 'F.A.Q.', 'avh-ec' ), array (&$this, 'metaboxFAQ' ), $this->hooks['avhec_menu_faq'], 'normal', 'core' );
		add_meta_box( 'avhecBoxTranslation', __( 'Translation', 'avh-ec' ), array (&$this, 'metaboxTranslation' ), $this->hooks['avhec_menu_faq'], 'normal', 'core' );

		add_filter( 'screen_layout_columns', array (&$this, 'filterScreenLayoutColumns' ), 10, 2 );

		// WordPress core Styles and Scripts
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
		wp_admin_css( 'css/dashboard' );

		// Plugin Style and Scripts
		wp_enqueue_style( 'avhec-admin-css' );

	}

	/**
	 * Menu Page FAQ
	 *
	 * @return none
	 */
	function doMenuFAQ ()
	{
		global $screen_layout_columns;

		// This box can't be unselectd in the the Screen Options
		add_meta_box( 'avhecBoxDonations', __( 'Donations', 'avh-ec' ), array (&$this, 'metaboxDonations' ), $this->hooks['avhec_menu_faq'], 'side', 'core' );
		$hide2 = '';
		switch ( $screen_layout_columns )
		{
			case 2 :
				$width = 'width:49%;';
				break;
			default :
				$width = 'width:98%;';
				$hide2 = 'display:none;';
		}

		echo '<div class="wrap avhec-wrap">';
		echo $this->displayIcon( 'index' );
		echo '<h2>' . 'AVH Extended Categories - ' . __( 'F.A.Q', 'avh-ec' ) . '</h2>';
		echo '	<div id="dashboard-widgets-wrap">';
		echo '		<div id="dashboard-widgets" class="metabox-holder">';
		echo '			<div class="postbox-container" style="' . $width . '">' . "\n";
		do_meta_boxes( $this->hooks['avhec_menu_faq'], 'normal', '' );
		echo '			</div>';
		echo '			<div class="postbox-container" style="' . $hide2 . $width . '">' . "\n";
		do_meta_boxes( $this->hooks['avhec_menu_faq'], 'side', '' );
		echo '			</div>';
		echo '		</div>';
		echo '<br class="clear"/>';
		echo '	</div>'; //dashboard-widgets-wrap
		echo '</div>'; // wrap


		$this->printMetaboxGeneralNonces();
		$this->printMetaboxJS( 'faq' );
		$this->printAdminFooter();
	}

	/**
	 * Options Metabox
	 *
	 */
	function metaboxOptions ( $data )
	{
		echo $this->printOptions( $data['options_general'], $data['actual_options'] );
	}

	/**
	 * Translation Metabox
	 * @return unknown_type
	 */
	function metaboxTranslation ()
	{
		echo '<p>A language pack can be created for this plugin. The .pot file is included with the plugin and can be found in the directory extended-categories-widget/2.8/lang';
		echo 'If you have created a language pack you can send the .po, and if you have it the .mo file, to me and I will include the files with the plugin';
		echo 'More information about translating can found at http://codex.wordpress.org/Translating_WordPress . This page is dedicated for translating WordPress but the instructions are the same for this plugin.';
		echo '</p>';
		echo '<p>';
		echo 'I have also setup a project in Launchpad for translating the plugin. Just visit <a href="http://bit.ly/95WyJ" target="_blank" title="AVH Extended Categories Translation Project">http://bit.ly/95WyJ</a>';
		echo '</p>';
		echo '<p>';
		echo '<span class="b">Available Languages</span></p><p>';
		echo 'Czech - Čeština (cs_CZ)  in Launchpad - Dirty Mind - <a href="http://dirtymind.ic.cz" target="_blank">http://dirtymind.ic.cz</a><br />';
		echo 'Spanish - Español (es_ES) in Launchpad<br />';
		echo 'Italian - Italiano (it_IT) in Launchpad - Gianni Diurno - <a href="http://gidibao.net" target="_blank">http://gidibao.net</a><br />';
		echo '</p>';
	}

	/**
	 * Donation Metabox
	 * @return unknown_type
	 */
	function metaboxDonations ()
	{
		echo '<p>If you enjoy this plug-in please consider a donation. There are several ways you can show your appreciation</p>';
		echo '<p>';
		echo '<span class="b">Amazon Wish List</span><br />';
		echo 'You can send me something from my <a href="http://www.amazon.com/gp/registry/wishlist/1U3DTWZ72PI7W?tag=avh-donation-20">Amazon Wish List</a>';
		echo '</p>';
		echo '<p>';
		echo '<span class="b">Through Paypal.</span><br />';
		echo 'Click on the Donate button and you will be directed to Paypal where you can make your donation and you don\'t need to have a Paypal account to make a donation.';
		echo '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=S85FXJ9EBHAF2&lc=US&item_name=AVH%20Plugins&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted" target="_blank" title="Donate">';
		echo '<img src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" alt="Donate"/></a>';
		echo '</p>';
	}

	/***
	 * F.A.Q Metabox
	 * @return none
	 */
	function metaboxFAQ ()
	{

		echo '<p>';
		echo '<span class="b">What about support?</span><br />';
		echo 'I created a support site at http://forums.avirtualhome.com where you can ask questions or request features.<br />';
		echo '</p>';

		echo '<p>';
		echo '<span class="b">What is depth selection?</span><br />';
		echo 'Starting with version 2.0 and WordPress 2.8 you can select how many levels deep you want to show your categories. This option only works when you select Show Hierarchy as well.<br /><br />';
		echo 'Here is how it works: Say you have 5 top level categories and each top level has a number of children. You could manually select all the Top Level categories you want to show but now you can do the following:<br />';
		echo 'You select to display all categories, select to Show hierarchy and select how many levels you want to show, in this case Toplevel only.<br />';
		echo '</p>';

	}

	/**
	 * Sets the amount of columns wanted for a particuler screen
	 *
	 * @WordPress filter screen_meta_screen
	 * @param $screen
	 * @return strings
	 */

	function filterScreenLayoutColumns ( $columns, $screen )
	{
		switch ( $screen )
		{
			case $this->hooks['avhec_menu_overview'] :
				$columns[$this->hooks['avhec_menu_overview']] = 2;
				break;
			case $this->hooks['avhec_menu_general'] :
				$columns[$this->hooks['avhec_menu_general']] = 2;
				break;
			case $this->hooks['avhec_menu_grouped'] :
				$columns[$this->hooks['avhec_menu_grouped']] = 2;
				break;
			case $this->hooks['avhec_menu_faq'] :
				$columns[$this->hooks['avhec_menu_faq']] = 2;
				break;

		}
		return $columns;
	}

	/**
	 * Adds Settings next to the plugin actions
	 *
	 * @WordPress Filter plugin_action_links_avh-amazon/avh-amazon.php
	 *
	 */
	function filterPluginActions ( $links, $file )
	{
		static $this_plugin;

		if ( ! $this_plugin )
			$this_plugin = $this->core->getBaseDirectory( plugin_basename( $this->core->info['plugin_dir'] ) );
		if ( $file )
			$file = $this->core->getBaseDirectory( $file );
		if ( $file == $this_plugin ) {
			$settings_link = '<a href="admin.php?page=extended-categories-widget">' . __( 'Settings', 'avh-ec' ) . '</a>';
			array_unshift( $links, $settings_link ); // before other links
		//$links = array_merge ( array (	$settings_link ), $links ); // before other links
		}
		return $links;

	}

	############## Admin WP Helper ##############


	/**
	 * Prints the general nonces, used by the AJAX
	 */
	function printMetaboxGeneralNonces ()
	{
		echo '<form style="display:none" method="get" action="">';
		echo '<p>';
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
		echo '</p>';
		echo '</form>';

	}

	/**
	 * Print the Metabox JS for toggling closed and open
	 *
	 * @param $boxid
	 */
	function printMetaboxJS ( $boxid )
	{
		$a = $this->hooks['avhec_menu_' . $boxid];
		echo '<script type="text/javascript">' . "\n";
		echo '	//<![CDATA[' . "\n";
		echo '	jQuery(document).ready( function($) {' . "\n";
		echo '		$(\'.if-js-closed\').removeClass(\'if-js-closed\').addClass(\'closed\');' . "\n";
		echo '		// postboxes setup' . "\n";
		echo '		postboxes.add_postbox_toggles(\'' . $a . '\');' . "\n";
		echo '	});' . "\n";
		echo '	//]]>' . "\n";
		echo '</script>';

	}

	/**
	 * Display plugin Copyright
	 *
	 */
	function printAdminFooter ()
	{
		echo '<p class="footer_avhec">';
		printf( '&copy; Copyright 2009 <a href="http://blog.avirtualhome.com/" title="My Thoughts">Peter van der Does</a> | AVH Extended Categories Version %s', $this->core->version );
		echo '</p>';
	}

	/**
	 * Display WP alert
	 *
	 */
	function displayMessage ()
	{
		if ( $this->message != '' ) {
			$message = $this->message;
			$status = $this->status;
			$this->message = $this->status = ''; // Reset
		}
		if ( $message ) {
			$status = ($status != '') ? $status : 'updated fade';
			echo '<div id="message"	class="' . $status . '">';
			echo '<p><strong>' . $message . '</strong></p></div>';
		}
	}

	/**
	 * Ouput formatted options
	 *
	 * @param array $option_data
	 * @return string
	 */
	function printOptions ( $option_data, $option_actual )
	{
		// Generate output
		$output = '';
		$output .= "\n" . '<table class="form-table avhec-options">' . "\n";
		foreach ( $option_data as $option ) {
			$section = substr( $option[0], strpos( $option[0], '[' ) + 1 );
			$section = substr( $section, 0, strpos( $section, '][' ) );
			$option_key = rtrim( $option[0], ']' );
			$option_key = substr( $option_key, strpos( $option_key, '][' ) + 2 );
			// Helper
			if ( $option[2] == 'helper' ) {
				$output .= '<tr style="vertical-align: top;"><td class="helper" colspan="2">' . $option[4] . '</td></tr>' . "\n";
				continue;
			}
			switch ( $option[2] )
			{
				case 'checkbox' :
					$input_type = '<input type="checkbox" id="' . $option[0] . '" name="' . $option[0] . '" value="' . attribute_escape( $option[3] ) . '" ' . $this->isChecked( '1', $option_actual[$section][$option_key] ) . ' />' . "\n";
					$explanation = $option[4];
					break;
				case 'dropdown' :
					$selvalue = explode( '/', $option[3] );
					$seltext = explode( '/', $option[4] );
					$seldata = '';
					foreach ( ( array ) $selvalue as $key => $sel ) {
						$seldata .= '<option value="' . $sel . '" ' . (($option_actual[$section][$option_key] == $sel) ? 'selected="selected"' : '') . ' >' . ucfirst( $seltext[$key] ) . '</option>' . "\n";
					}
					$input_type = '<select id="' . $option[0] . '" name="' . $option[0] . '">' . $seldata . '</select>' . "\n";
					$explanation = $option[5];
					break;
				case 'text-color' :
					$input_type = '<input type="text" ' . (($option[3] > 50) ? ' style="width: 95%" ' : '') . 'id="' . $option[0] . '" name="' . $option[0] . '" value="' . attribute_escape( $option_actual[$section][$option_key] ) . '" size="' . $option[3] . '" /><div class="box_color ' . $option[0] . '"></div>' . "\n";
					$explanation = $option[4];
					break;
				case 'textarea' :
					$input_type = '<textarea rows="' . $option[5] . '" ' . (($option[3] > 50) ? ' style="width: 95%" ' : '') . 'id="' . $option[0] . '" name="' . $option[0] . '" size="' . $option[3] . '" />' . attribute_escape( $option_actual[$section][$option_key] ) . '</textarea>';
					$explanation = $option[4];
					break;
				case 'text' :
				default :
					$input_type = '<input type="text" ' . (($option[3] > 50) ? ' style="width: 95%" ' : '') . 'id="' . $option[0] . '" name="' . $option[0] . '" value="' . attribute_escape( $option_actual[$section][$option_key] ) . '" size="' . $option[3] . '" />' . "\n";
					$explanation = $option[4];
					break;
			}
			// Additional Information
			$extra = '';
			if ( $explanation ) {
				$extra = '<br /><span class="description">' . __( $explanation ) . '</span>' . "\n";
			}
			// Output
			$output .= '<tr style="vertical-align: top;"><th align="left" scope="row"><label for="' . $option[0] . '">' . __( $option[1] ) . '</label></th><td>' . $input_type . '	' . $extra . '</td></tr>' . "\n";
		}
		$output .= '</table>' . "\n";
		return $output;
	}

	/**
	 * Used in forms to set an option checked
	 *
	 * @param mixed $checked
	 * @param mixed $current
	 * @return strings
	 */
	function isChecked ( $checked, $current )
	{
		$return = '';
		if ( $checked == $current ) {
			$return = ' checked="checked"';
		}
		return $return;
	}

	function displayIcon ( $icon )
	{
		return ('<div class="icon32" id="icon-' . $icon . '"><br/></div>');
	}

}
?>
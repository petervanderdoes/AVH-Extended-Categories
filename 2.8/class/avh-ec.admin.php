<?php
class AVH_EC_Admin
{
	var $core;
	var $pagehoook_OptionsPage;

	function __construct ()
	{
		// Initialize the plugin
		$this->core = & AVHExtendedCategoriesCore::getInstance();

		// Admin menu
		add_action( 'admin_menu', array (&$this, 'actionAdminMenu' ) );

		add_filter( 'plugin_action_links_extended-categories-widget/widget_extended_categories.php', array (&$this, 'filterPluginActions' ), 10, 2 );
		return;
	}

	function AVH_EC_Admin ()
	{
		$this->__construct();
	}

	/**
	 * Add the Tools and Options to the Management and Options page repectively
	 *
	 * @WordPress Action admin_menu
	 *
	 */
	function actionAdminMenu ()
	{

		$this->pagehoook_OptionsPage = add_options_page( 'AVH Extended Categories', 'AVH Extended Categories', 'manage_options', 'avhec_options', array (&$this, 'doPageOptions' ) );
		add_action( 'load-' . $this->pagehoook_OptionsPage, array (&$this, 'actionLoadpagehoook_OptionsPage_doPageOptions' ) );

		add_filter( 'screen_layout_columns', array (&$this, 'filterScreenLayoutColumns' ), 10, 2 );

		wp_enqueue_style( 'avhecadmin', $this->core->info['plugin_url'] . '/inc/avh-ec.admin.css', array (), $this->core->version, 'screen' );
		wp_admin_css( 'css/dashboard' );
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
	}

	function actionLoadpagehoook_OptionsPage_doPageOptions ()
	{
		// Add metaboxes
		add_meta_box( 'avhecBoxTransalation', 'Translation', array (&$this, 'metaboxTranslation' ), $this->pagehoook_OptionsPage, 'normal', 'core' );


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
		if ( $screen == $this->pagehoook_OptionsPage ) {
			$columns[$this->pagehoook_OptionsPage] = 2;
		}
		return $columns;

	}

	function doPageOptions ()
	{
		global $screen_layout_columns;

		// This box can't be unselectd in the the Screen Options
		add_meta_box( 'avhecBoxDonations', 'Donations', array (&$this, 'metaboxDonations' ), $this->pagehoook_OptionsPage, 'normal', 'core' );
		$hide2 = '';
		switch ( $screen_layout_columns ) {
			case 2:
				$width = 'width:49%;';
				break;
			default:
				$width = 'width:98%;';
				$hide2 = 'display:none;';
		}

		echo '<div class="wrap avhfdas-wrap">';
		echo $this->displayIcon( 'index' );
		echo '<h2>' . 'AVH Extended Categories' . '</h2>';
		echo '	<div id="dashboard-widgets-wrap">';
		echo '		<div id="dashboard-widgets" class="metabox-holder">';
		echo "			<div class='postbox-container' style='$width'>\n";
		do_meta_boxes( $this->pagehoook_OptionsPage, 'normal', '' );
			echo "			</div>";
		echo "			<div class='postbox-container' style='{$hide2}$width'>\n";
		do_meta_boxes( $this->pagehoook_OptionsPage, 'side', '' );
		echo '			</div>';
		echo '		</div>';
		echo '<form style="display: none" method="get" action="">';
		echo '<p>';
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
		echo '</p>';
		echo '</form>';
		echo '<br class="clear"/>';
		echo '	</div>'; //dashboard-widgets-wrap
		echo '</div>'; // wrap

		echo '<script type="text/javascript">' . "\n";
		echo '	//<![CDATA[' . "\n";
		echo '	jQuery(document).ready( function($) {' . "\n";
		echo'		$(\'.if-js-closed\').removeClass(\'if-js-closed\').addClass(\'closed\');'."\n";
		echo '		// postboxes setup' . "\n";
		echo '		postboxes.add_postbox_toggles(\'avhfdas-menu-overview\');' . "\n";
		echo '	});' . "\n";
		echo '	//]]>' . "\n";
		echo '</script>';
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
		echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post"> <input name="cmd" type="hidden" value="_donations" /> <input name="business" type="hidden" value="paypal@avirtualhome.com" /> <input name="item_name" type="hidden" value="AVH Plugins" /> <input name="no_shipping" type="hidden" value="1" /> <input name="no_note" type="hidden" value="1" /> <input name="currency_code" type="hidden" value="USD" /> <input name="tax" type="hidden" value="0" /> <input name="lc" type="hidden" value="US" /> <input name="bn" type="hidden" value="PP-DonationsBF" /> <input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" type="image" /> </form>';
		echo '</p>';
	}

	/**
	 * Donation Metabox
	 * @return unknown_type
	 */
	function metaboxTranslation ()
	{
		echo '<p>A language pack can be created for this plugin. The .pot file is included with the plugin and can be found in the directory extended-categories-widget/2.8/lang</p>';
		echo '<div class="versions">';
		echo '<p>';
		echo 'If you have created a language pack you can send the .po, and if you have it the .mo file, to me and I will include the files with the plugin';
		echo '</p>';
		echo '<p>';
		echo 'More information about translating can found at http://codex.wordpress.org/Translating_WordPress . This page is dedicated for translating WordPress but the instructions are the same for this plugin.';
		echo '</p></div>';
		echo '<p>';
		echo '<span class=\'b\'>Available Languages</span>';
		echo 'Russian (ru_RU)<br />';
		echo 'Czech (cs_CZ)<br />';
		echo '</p>';
	}

	function displayIcon ( $icon )
	{
		return ('<div class="icon32" id="icon-' . $icon . '"><br/></div>');
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
			$settings_link = '<a href="options-general.php?page=avhec_options">' . __( 'Settings', 'avh-ec' ) . '</a>';
			array_unshift( $links, $settings_link ); // before other links
		//$links = array_merge ( array (	$settings_link ), $links ); // before other links
		}
		return $links;

	}

}
?>
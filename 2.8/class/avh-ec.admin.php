<?php
class AVH_EC_Admin
{
	var $core;

	function __construct ()
	{
		// Initialize the plugin
		$this->core = & AVHExtendedCategoriesCore::getInstance();

		// Admin menu
		add_action( 'admin_menu', array (&$this, 'actionAdminMenu' ) );

		/**
		 * Inject CSS and Javascript on the right pages
		 *
		 * Main Action: admin_print_styles-, admin_print-scripts-
		 * Top level page: toplevel_page_avh-first-defense-against-spam
		 * Sub menus: avh-f-d-a-s_page_avh-fdas-general
		 *
		 */
		add_action( 'admin_print_styles', array (&$this, 'actionInjectCSS' ) );
		add_action( 'admin_print_scripts', array (&$this, 'actionInjectJS' ) );

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

		add_options_page( 'AVH Extended Categories', 'AVH Extended Categories', 'manage_options', 'avhec_options', array (&$this, 'pageOptions' ) );
		add_filter( 'plugin_action_links_extended-categories-widget/widget_extended_categories.php', array (&$this, 'filterPluginActions' ), 10, 2 );
		// Add metaboxes
		add_meta_box( 'dashboard_right_now', 'Donations', array (&$this, 'metaboxMenuOverviewDonations' ), 'avhec-menu-donation', 'left', 'core' );
	}

	function pageOptions ()
	{
		echo '<div class="wrap avhfdas-wrap">';
		echo $this->displayIcon( 'index' );
		echo '<h2>' . 'AVH Extended Categories' . '</h2>';
		echo '<div id="dashboard-widgets-wrap" class="avhec-menu-donation">';
		echo '    <div id="dashboard-widgets" class="metabox-holder">';
		echo '		<div id="post-body">';
		echo '			<div id="dashboard-widgets-main-content">';
		echo '				<div class="postbox-container" style="width:49%;">';
		do_meta_boxes( 'avhec-menu-donation', 'left', '' );
		echo '				</div>';
		echo '			</div>';
		echo '		</div>';
		echo '    </div>';
		echo '</div>';
		echo '</div>';
		echo '<script type="text/javascript">' . "\n";
		echo '	//<![CDATA[' . "\n";
		echo '	jQuery(document).ready( function($) {' . "\n";
		echo '		// postboxes setup' . "\n";
		echo '		postboxes.add_postbox_toggles(\'avhec-menu-donation\');' . "\n";
		echo '	});' . "\n";
		echo '	//]]>' . "\n";
		echo '</script>';
	}

	/**
	 * Donation Metabox
	 * @return unknown_type
	 */
	function metaboxMenuOverviewDonations ()
	{
		echo '<p>If you enjoy this plug-in please consider a donation. There are several ways you can show your appreciation</p>';
		echo '<div class="versions">';
		echo '<p>';
		echo '<span class="b">Amazon Wish List</span><br />';
		echo 'You can send me something from my <a href="http://www.amazon.com/gp/registry/wishlist/1U3DTWZ72PI7W?tag=avh-donation-20">Amazon Wish List</a>';
		echo '</p>';
		echo '<p>';
		echo '<span class="b">Through Paypal.</span><br />';
		echo 'Click on the Donate button and you will be directed to Paypal where you can make your donation and you don\'t need to have a Paypal account to make a donation.';
		echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post"> <input name="cmd" type="hidden" value="_donations" /> <input name="business" type="hidden" value="paypal@avirtualhome.com" /> <input name="item_name" type="hidden" value="AVH Plugins" /> <input name="no_shipping" type="hidden" value="1" /> <input name="no_note" type="hidden" value="1" /> <input name="currency_code" type="hidden" value="USD" /> <input name="tax" type="hidden" value="0" /> <input name="lc" type="hidden" value="US" /> <input name="bn" type="hidden" value="PP-DonationsBF" /> <input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" type="image" /> </form>';
		echo '</p></div>';
	}

	function displayIcon ( $icon )
	{
		return ('<div class="icon32" id="icon-' . $icon . '"><br/></div>');
	}

	/**
	 * Insert link to CSS
	 *
	 */
	function actionInjectCSS ()
	{
		wp_admin_css( 'css/dashboard' );

	}

	/**
	 * Insert link to JS
	 *
	 */
	function actionInjectJS ()
	{
		wp_enqueue_script( 'postbox' );

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
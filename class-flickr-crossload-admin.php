<?php
/**
 * Admin class file.
 *
 * @package fork-river\crossload
 */

/**
 * Admin class.
 *
 * @since 1.0.0
 */
class FR_Flickr_Crossload_Admin {

	/**
	 * Class constructor.
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_setting' ) );
	}

	/**
	 * Adds the admin (sub)menu.
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function add_admin_menu() {
		add_submenu_page( 'options-general.php', 'flickr-crossload', 'Flickr Crossload', 'manage_options', 'flickr-crossload', array( $this, 'menu_cb' ) );
	}

	/**
	 * Admin menu callback.
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function menu_cb() {
		echo 'hi!';
	}

	/**
	 * Registers the setting.
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function register_setting() {
		// register_setting( $option_group, $option_name, $args = array );
	}
}

new FR_Flickr_Crossload_Admin;

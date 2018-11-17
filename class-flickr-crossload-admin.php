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
		add_submenu_page( 'options-general.php', 'Flickr Crossload', 'Flickr Crossload', 'manage_options', 'flickr-crossload', array( $this, 'menu_cb' ) );
	}

	/**
	 * Admin menu callback.
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function menu_cb() {
		echo '<h1>Flickr Crossload settings</h1>';
		echo '<form action="options.php" method="post">';
		settings_fields( 'flickr_crossload' );
		do_settings_sections( 'flickr_crossload' );
		submit_button();
		echo '</form>';
	}

	/**
	 * Registers the setting.
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function register_setting() {
		register_setting( 'flickr_crossload', FR_Flickr_Crossload::PREFIX . 'settings' );
		add_settings_section(
			FR_Flickr_Crossload::PREFIX . 'settings_section',
			esc_html__( 'Crossload Settings', 'frfc' ),
			array( $this, 'settings_section_cb' ),
			'flickr_crossload'
		);
		add_settings_field(
			'api_key',
			esc_html__( 'API Key', 'frfc' ),
			array( $this, 'api_key_field_cb' ),
			'flickr_crossload',
			FR_Flickr_Crossload::PREFIX . 'settings_section'
		);
		add_settings_field(
			'secret',
			esc_html__( 'API Secret', 'frfc' ),
			array( $this, 'secret_field_cb' ),
			'flickr_crossload',
			FR_Flickr_Crossload::PREFIX . 'settings_section'
		);
		add_settings_field(
			'username',
			esc_html__( 'Flickr Username', 'frfc' ),
			array( $this, 'username_field_cb' ),
			'flickr_crossload',
			FR_Flickr_Crossload::PREFIX . 'settings_section'
		);
	}

	/**
	 * Settings section callback.
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function settings_section_cb() {
		echo esc_html__( 'API Settings', 'frfc' );
	}

	/**
	 * API Secret field callback.
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function secret_field_cb() {
		$option = get_option( FR_Flickr_Crossload::PREFIX . 'settings', array() );
		if ( empty( $option ) || empty( $option['secret'] ) ) {
			$value = '';
		} else {
			$value = $option['secret'];
		}
		echo "<input type='text' size='40' name='" . FR_Flickr_Crossload::PREFIX . 'settings[secret]' . "' value='$value' />"; // wpcs: xss ok.
	}

	/**
	 * API Key field callback.
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function api_key_field_cb() {
		$option = get_option( FR_Flickr_Crossload::PREFIX . 'settings', array() );
		if ( empty( $option ) || empty( $option['api_key'] ) ) {
			$value = '';
		} else {
			$value = $option['api_key'];
		}
		echo "<input type='text' size='40' name='" . FR_Flickr_Crossload::PREFIX . 'settings[api_key]' . "' value='$value' />"; // wpcs: xss ok.
	}

	/**
	 * Flickr Username field callback.
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function username_field_cb() {
		$option = get_option( FR_Flickr_Crossload::PREFIX . 'settings', array() );
		if ( empty( $option ) || empty( $option['username'] ) ) {
			$value = '';
		} else {
			$value = $option['username'];
		}
		echo "<input type='text' size='40' name='" . FR_Flickr_Crossload::PREFIX . 'settings[username]' . "' value='$value' />"; // wpcs: xss ok.
	}
}

new FR_Flickr_Crossload_Admin;

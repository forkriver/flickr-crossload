<?php
/**
 * Admin class file.
 *
 * @package fork-river\crossload
 */

require_once 'vendor/autoload.php';
session_start();

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
		$settings = get_option( FR_Flickr_Crossload::PREFIX . 'settings', array() );
		echo '<h1>Flickr Crossload settings</h1>';
		echo '<form action="options.php" method="post">';
		settings_fields( 'flickr_crossload' );
		do_settings_sections( 'flickr_crossload' );
		submit_button();
		echo '</form>';

		// Flickr authentication part.
		// First, we check to see if the tokens are in the $_GET array.
		if ( ! empty( $settings ) && ! empty( $settings['api_key'] ) && ! empty( $settings['api_secret'] ) ) {
			$flickr = new \Samwilson\PhpFlickr\PhpFlickr( $settings['api_key'], $settings['api_secret'] );
			$storage = new \OAuth\Common\Storage\Session();
			$flickr->setOauthStorage( $storage );
			if ( ! empty( $_GET ) && ! empty( $_GET['oauth_token'] ) && !empty( $_GET['oauth_verifier'] ) ) {
				// Get the final token.
				$token = $flickr->retrieveAccessToken( $_GET['oauth_verifier'], $_GET['oauth_token'] );
				$settings['access_token'] = $token->getAccessToken();
				$settings['access_token_secret'] = $token->getAccessTokenSecret();
				update_option( FR_Flickr_Crossload::PREFIX . 'settings', $settings );
			}

			if ( empty( $settings['access_token'] ) || empty( $settings['access_token_secret'] ) ) {
				echo '<h2>Flickr Authentication</h2>';
				$perm = 'write';
				$callback_url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" ) . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
				$url = $flickr->getAuthUrl( $perm, $callback_url );
				echo "<a href='{$url}'>Click to authenticate</a>";
			} else {
				echo '@todo: De-authenticate!';
			}
		}

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
			'api_secret',
			esc_html__( 'API Secret', 'frfc' ),
			array( $this, 'api_secret_field_cb' ),
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
	function api_secret_field_cb() {
		$option = get_option( FR_Flickr_Crossload::PREFIX . 'settings', array() );
		if ( empty( $option ) || empty( $option['api_secret'] ) ) {
			$value = '';
		} else {
			$value = $option['api_secret'];
		}
		echo "<input type='text' size='40' name='" . FR_Flickr_Crossload::PREFIX . 'settings[api_secret]' . "' value='$value' />"; // wpcs: xss ok.
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

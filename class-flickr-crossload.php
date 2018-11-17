<?php
/**
 * Core class file.
 *
 * @package fork-river\flickr-crossload
 */

/**
 * Core class.
 *
 * @since 1.0.0
 */
class FR_Flickr_Crossload {

	const PREFIX = '_frfc_';

	const FLICKR_API_URL = 'https://api.flickr.com/services/rest';

	/**
	 * Class constructor.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function __construct() {
		add_filter( 'the_content', array( 'FR_Flickr_Crossload', 'flickr_api_magic' ) );
	}

	/**
	 * The workhorse.
	 *
	 * @param  string $content The content.
	 * @return string          The filtered content.
	 * @since  1.0.0
	 */
	public static function flickr_api_magic( $content ) {
		if ( is_page( 'flickr-test' ) ) {
			$body = array_merge( self::$api_defaults, array(
				'method' => 'flickr.people.findByUsername',
				'username' => 'pj',
			) );
			$args = array(
				'body' => $body,
			);
			$response = wp_safe_remote_get( FR_Flickr_Crossload::FLICKR_API_URL, $args );

			_dump( str_replace( [ '<', '>' ], [ '&lt;', '&gt;' ], $response['body'] ) );
		}
		return $content;
	}

	/* Helper monkeys. */

	/**
	 * Gets the Flickr API key.
	 *
	 * @return string The API key.
	 * @since  1.0.0
	 */
	function get_api_key() {
		return get_option( self::PREFIX . 'flickr_api_key', '' );
	}

	/**
	 * Gets the Flickr secret key.
	 *
	 * @return string The secret key.
	 * @since  1.0.0
	 */
	function get_secret() {
		return get_option( self::PREFIX . 'flickr_secret', '' );
	}

	/**
	 * Gets the API defaults.
	 *
	 * @return array The API defaults.
	 * @since  1.0.0
	 */
	function get_defaults() {
		$api_defaults = array(
			'api_key'        => self::get_api_key(),
			'secret'         => self::get_secret(),
			'format'         => 'json',
			'nojsoncallback' => '1',
		);
		return $api_defaults;
	}

}

new FR_Flickr_Crossload;

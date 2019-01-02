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
	 * Map:
	 *  - Get Flickr user ID from username (flickr.people.findByUsername).
	 *  - Get photos (flickr.people.getPhotos).
	 *  - Get photo sizes (ie, the URLs for the sizes) (flickr.photos.getSizes).
	 *  - Get photo info (flickr.photos.getInfo).
	 *  - Get photo comments (flickr.photos.comments.getList).
	 *  Once we have the necessary info, load the photo into the site's media gallery.
	 *  Use title, description, and tags from the photo info.
	 *  Also: This will need to be batched.
	 */

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
			$url = self::FLICKR_API_URL;
			$body = array_merge(
				self::get_defaults(),
				array(
					'method' => 'flickr.people.findByUsername',
				)
			);
			$args = array(
				'body' => $body,
			);
			$response = wp_safe_remote_get( $url, $args );
			if ( is_wp_error( $response ) ) {
				wp_die( 'An error occurred. ' . __FILE__ . ': ' . __LINE__ );
			}
			$data = json_decode( $response['body'] );
			$user_id = $data->user->nsid;
			$args['body']['method']  = 'flickr.people.getPhotos';
			$args['body']['user_id'] = $user_id;
			$response = wp_safe_remote_get( $url, $args );
			if ( is_wp_error( $response ) ) {
				wp_die( 'An error occurred. ' . __FILE__ . ': ' . __LINE__ );
			}
			$data = json_decode( $response['body'] );
			// @todo See list at top of page.
		}
		return $content;
	}

	/**
	 * Gets the Flickr API keys.
	 *
	 * @param  boolean $all Get all the keys, or just the ones for reading?
	 * @return array        An array containing the found API keys.
	 * @since  1.0.0
	 */
	public static function get_api_keys( $all = false ) {
		$key_names = array(
			'api_key',
			'access_token',
		);
		if ( true === $all ) {
			$key_names[] = 'api_secret';
			$key_names[] = 'access_token_secret';
		}
		$keys = array();
		$option = get_option( self::PREFIX . 'api_keys', array() );
		foreach( $key_names as $key_name ) {
			if ( ! empty( $option[ $key_name ] ) ) {
				$keys[ $key_name ] = $option[ $key_name ];
			}
		}
		return $keys;
	}

	/* Helper monkeys. */

	/**
	 * Gets the API defaults.
	 *
	 * @return array The API defaults.
	 * @since  1.0.0
	 */
	public static function get_defaults() {
		$settings = get_option( self::PREFIX . 'settings', array() );
		$api_defaults = array_merge(
			$settings,
			array(
				'format'         => 'json',
				'nojsoncallback' => '1',
			)
		);
		return $api_defaults;
	}

}

new FR_Flickr_Crossload;

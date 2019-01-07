<?php
/**
 * Core class file.
 *
 * @package fork-river\flickr-crossload
 */

require_once 'vendor/autoload.php';

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
					'username' => 'Patrick Johanneson',
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

			// OK, the above is probably unnecessary.
			$settings = get_option( FR_Flickr_Crossload::PREFIX . 'settings', array() );
			if ( ! empty( $settings ) && ! empty( $settings['access_token'] ) ) {

				$flickr = new \Samwilson\PhpFlickr\PhpFlickr( $settings['api_key'], $settings['api_secret'] );
				// Create storage.
				$storage = new \OAuth\Common\Storage\Memory();
				// Create the access token from the strings you acquired before.
				$token = new \OAuth\OAuth1\Token\StdOAuth1Token();
				$token->setAccessToken( $settings['access_token'] );
				$token->setAccessTokenSecret( $settings['access_token_secret'] );
				// Add the token to the storage.
				$storage->storeAccessToken( 'Flickr', $token );
				$flickr->setOauthStorage( $storage );

				// All the `getPhotos() params.
				// Docs: @link https://www.flickr.com/services/api/flickr.people.getPhotos.html
				$user_id         = 'me';
				$safe_search     = null;
				$min_upload_date = null;
				$max_upload_date = null;
				$min_taken_date  = null;
				$max_taken_date  = null;
				$content_type    = null;
				$privacy_filter  = null;
				$extras          = 'description, license, date_upload, date_taken, owner_name, icon_server, original_format, last_update, geo, tags, machine_tags, o_dims, views, media, path_alias, url_sq, url_t, url_s, url_q, url_m, url_n, url_z, url_c, url_l, url_o';
				$per_page        = 100;
				$page            = 1;
				if ( ! empty( $_GET['photo_page'] ) ) {
					$page = absint( $_GET['photo_page'] );
				}

				$photos = $flickr->people()->getPhotos(
					$user_id,
					$safe_search,
					$min_upload_date,
					$max_upload_date,
					$min_taken_date,
					$max_taken_date,
					$content_type,
					$privacy_filter,
					$extras,
					$per_page,
					$page
				);
				_dump( $photos );
			}
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
		$option = get_option( self::PREFIX . 'settings', array() );
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

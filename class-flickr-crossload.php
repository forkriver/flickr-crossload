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

	// @todo Use the Settings API to enter the API keys.
	const FLICKR_API_KEY = 'eecaaa8988e695d830c14c2fe0b05b68';
	const FLICKR_SECRET  = '547ea54436867736';

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
			$url = 'https://api.flickr.com/services/rest';
			$args = array(
				'httpversion' => '1.0',
				'body' => array(
					'method'         => 'flickr.people.findByUsername',
					'api_key'        => FR_Flickr_Crossload::FLICKR_API_KEY,
					'format'         => 'json',
					'nojsoncallback' => '1',
					'username'       => 'pj',
				),

			);
			$response = wp_safe_remote_get( $url, $args );

			_dump( str_replace( [ '<', '>' ], [ '&lt;', '&gt;' ], $response['body'] ) );
		}
		return $content;
	}

}

new FR_Flickr_Crossload;

<?php
/**
 * Plugin loader file.
 *
 * @package fork-river\flickr-crossload
 * @link https://www.flickr.com/services/api/
 * @link https://github.com/samwilson/phpflickr
 */

/**
 * Plugin Name: Flickr Crossload
 * Description: Copy my photos from Flickr to WordPress.
 * Author Name: Patrick Johanneson
 * Version:     1.0.0
 * License:     GPL v3 or higher
 */

require 'class-flickr-crossload.php';
if ( is_admin() ) {
	require 'class-flickr-crossload-admin.php';
}

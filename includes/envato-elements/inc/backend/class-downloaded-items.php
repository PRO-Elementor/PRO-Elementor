<?php
/**
 * Envato Elements: DownloadedItems
 *
 * DownloadedItems management
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements\Backend;

use Envato_Elements\Utils\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * DownloadedItems management.
 *
 * @since 0.0.2
 */
class Downloaded_Items extends Base {

	const DOWNLOADED_ITEMS_OPTION = 'downloaded_items';

	/**
	 * This records a successful Elements download event in the database.
	 * This is so we can update the UI with "imported" flags.
	 *
	 * @param string $elements_humane_id
	 * @param int $imported_id
	 */
	public function record_download_event( $elements_humane_id, $imported_id ) {
		$downloaded_items = Options::get_instance()->get( self::DOWNLOADED_ITEMS_OPTION, [] );
		$imported_id      = (int) $imported_id;
		if ( $imported_id > 0 ) {
			$downloaded_items[ $elements_humane_id ] = $imported_id;

			// We record a "humane ID" against this imported kit so our cleanup function in:
			// wp-content/plugins/elements-for-wordpress/inc/backend/class-template-kits.php:39
			// can clean up the "record downloaded event" above.
			update_post_meta( $imported_id, 'envato_elements_download_event', $elements_humane_id );
		}
		Options::get_instance()->set( self::DOWNLOADED_ITEMS_OPTION, $downloaded_items );
	}

	/**
	 * This removes a download event, if we've deleted a photo or similar.
	 *
	 * @param string $elements_humane_id
	 */
	public function remove_download_event( $elements_humane_id ) {
		$downloaded_items = Options::get_instance()->get( self::DOWNLOADED_ITEMS_OPTION, [] );
		unset($downloaded_items[ $elements_humane_id ]);
		Options::get_instance()->set( self::DOWNLOADED_ITEMS_OPTION, $downloaded_items );
	}

	/**
	 * This finds if an item has already been downloaded based on the Elements Himane ID
	 *
	 * @param string $elements_humane_id
	 *
	 * @return bool|string
	 */
	public function find_downloaded_id( $elements_humane_id ) {
		$downloaded_items = Options::get_instance()->get( self::DOWNLOADED_ITEMS_OPTION, [] );

		return isset( $downloaded_items[ $elements_humane_id ] ) ? $downloaded_items[ $elements_humane_id ] : false;
	}

	/**
	 * Get a list of downloaded items for use in the UI.
	 *
	 * @return array
	 */
	public function get_downloaded_items() {
		return Options::get_instance()->get( self::DOWNLOADED_ITEMS_OPTION, [] );
	}
}

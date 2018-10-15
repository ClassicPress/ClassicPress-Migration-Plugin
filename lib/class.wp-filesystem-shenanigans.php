<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

class WP_Filesystem_Shenanigans extends WP_Filesystem_Direct {
	/**
	 *
	 * @param string $source
	 * @param string $destination
	 * @param bool   $overwrite
	 * @param int    $mode
	 * @return bool
	 */
	public function copy( $source, $destination, $overwrite = false, $mode = false ) {
		// In Core_Upgrader::upgrade, the WP upgrade logic copies the file
		// `/wordpress/wp-admin/includes/update-core.php` from the new package,
		// then executes it.
		//
		// In ClassicPress releases, this file doesn't live at this location,
		// so override the copy operation with the correct path.
		if ( preg_match(
			'#^(.*/)wordpress(/wp-admin/includes/update-core\.php)$#',
			$source,
			$matches
		) ) {
			$root = $matches[1];
			$file = $matches[2];
			$entries = array_values( $this->dirlist( $root ) );
			if (
				count( $entries ) === 1 &&
				substr( $entries[0]['name'], 0, 13 ) === 'ClassicPress-' &&
				$entries[0]['type'] === 'd'
			) {
				$source = $root . $entries[0]['name'] . $file;
				classicpress_show_message(
					'Successfully hooked into WordPress upgrade routine.'
				);
			} else {
				classicpress_show_message( 'Failed to override copy during upgrade!' );
				// Something unexpected happened, so return `false`.  If we
				// happened to be trying to install a WP upgrade, for example,
				// this will abort the installation.
				return false;
			}
		}
		return parent::copy( $source, $destination, $overwrite, $mode );
	}
}

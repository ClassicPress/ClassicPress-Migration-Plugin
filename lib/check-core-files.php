<?php

/**
 * Check WP core files and return a list of modified files.
 *
 * @since 0.2.0
 *
 * @see Core_Upgrader::check_files()
 *
 * @param string $locale The locale for which to download checksums.
 *
 * @return array|bool `false` if an error occurred, or an array of modified
 *                    filenames (empty if none were modified).
 */
function classicpress_check_core_files( $locale = 'en_US' ) {
	global $wp_version;

	$checksums = get_core_checksums( $wp_version, $locale );
	if ( ! is_array( $checksums ) ) {
		return false;
	}

	$modified = array();
	foreach ( $checksums as $file => $checksum ) {
		// Skip plugins, themes, etc.
		if ( 'wp-content' == substr( $file, 0, 10 ) ) {
			continue;
		}
		if (
			! file_exists( ABSPATH . $file ) ||
			md5_file( ABSPATH . $file ) !== $checksum
		) {
			$modified[] = $file;
		}
	}
	return $modified;
}

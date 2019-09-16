<?php

$lando_info = json_decode( file_get_contents( 'php://stdin' ), true );

if ( empty( $lando_info ) || empty( $argv[1] ) ) {
	die( "Usage: lando info --format=json | $argv[0] url-pattern\n" );
}
$match = $argv[1];

foreach ( $lando_info as $service ) {
	if ( $service['service'] === 'appserver' ) {
		foreach ( $service['urls'] as $url ) {
			if ( strstr( $url, $match ) !== false ) {
				print "$url\n";
				exit( 0 );
			}
		}
	}
}

fwrite( STDERR, "ERROR: No matching URL found!\n" );
exit( 1 );

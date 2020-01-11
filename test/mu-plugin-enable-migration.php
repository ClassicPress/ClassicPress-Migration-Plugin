<?php

# Copy this file to wp-content/mu-plugins/force-enable-migration.php

add_filter( 'classicpress_ignore_wp_version', '__return_true' );

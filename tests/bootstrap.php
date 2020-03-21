<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require '/tmp/wordpress/wp-content/plugins/AffiliateWP/affiliate-wp.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
require '/tmp/wordpress/wp-content/plugins/AffiliateWP/tests/phpunit/affwp-testcase.php';

activate_plugin( 'AffiliateWP/affiliate-wp.php' );

// Install AffiliateWP
affiliate_wp_install();

require dirname( __FILE__ ) . '/../affiliatewp-external-referral-links.php';

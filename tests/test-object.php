<?php

namespace Affwp_External_Referral_Links\Object;

use AffiliateWP_External_Referral_Links_Admin;
use AffWP\Tests\UnitTestCase;

/**
 * Tests for AffiliateWP_PayPal_Payouts_Referrals_Admin class
 *
 * @covers AffiliateWP_PayPal_Payouts_Referrals_Admin
 */
class Test extends UnitTestCase {

	public static function wpSetUpBeforeClass() {
		require_once \affiliatewp_external_referral_links::$plugin_dir . 'includes/admin.php';
		$settings = new AffiliateWP_External_Referral_Links_Admin;
		$settings->maybe_set_default_settings();
	}

	/**
	 * @covers affiliatewp_external_referral_links::get_option
	 */
	function test_object_get_option_should_return_null_if_option_value_is_not_set() {
		$option = affiliatewp_external_referral_links()->get_option( 'unset_option_key' );

		$this->assertFalse( $option );
	}

	/**
	 * @covers affiliatewp_external_referral_links::get_option
	 */
	function test_object_get_option_should_return_specified_option_if_value_is_set() {
		update_option( 'affiliatewp_external_referral_links', array(
				'cookie_expiration' => '1',
		) );

		$option = affiliatewp_external_referral_links()->get_option( 'cookie_expiration' );

		$this->assertSame( $option, '1' );
	}

	/**
	 * @covers affiliatewp_external_referral_links::get_option
	 */
	function test_object_get_option_should_return_null_if_specified_option_is_not_string() {
		update_option( 'affiliatewp_external_referral_links', array(
				'cookie_expiration' => '1',
		) );

		$option = affiliatewp_external_referral_links()->get_option( array( 'cookie_expiration' ) );

		$this->assertFalse( $option );
	}

	/**
	 * @covers affiliatewp_external_referral_links::load_scripts
	 */
	function test_load_scripts_should_not_enqueue_scripts_if_url_option_is_not_set() {
		update_option( 'affiliatewp_external_referral_links', array(
				'url' => null,
		) );

		$scripts_loaded = affiliatewp_external_referral_links()->load_scripts();

		$this->assertFalse( $scripts_loaded );
	}

	/**
	 * @covers affiliatewp_external_referral_links::load_scripts
	 */
	function test_load_scripts_should_enqueue_scripts_if_url_option_is_set() {
		update_option( 'affiliatewp_external_referral_links', array(
				'url'               => 'http://www.example.org',
				'cookie_expiration' => '1',
				'referral_variable' => 'ref',
		) );

		$scripts_loaded = affiliatewp_external_referral_links()->load_scripts();

		$this->assertTrue( $scripts_loaded );
	}

	/**
	 * @covers affiliatewp_external_referral_links::get_expiration_time
	 */
	function test_get_expiration_time_should_return_cookie_expiration_value_when_string_value_can_be_converted() {
		update_option( 'affiliatewp_external_referral_links', array(
				'cookie_expiration' => '10',
		) );

		$cookie_expiration = affiliatewp_external_referral_links()->get_expiration_time();

		$this->assertSame( $cookie_expiration, 10 );
	}

	/**
	 * @covers affiliatewp_external_referral_links::get_expiration_time
	 */
	function test_get_expiration_time_should_return_cookie_expiration_value_when_int_is_passed() {
		update_option( 'affiliatewp_external_referral_links', array(
				'cookie_expiration' => 10,
		) );

		$cookie_expiration = affiliatewp_external_referral_links()->get_expiration_time();

		$this->assertSame( $cookie_expiration, 10 );
	}

	/**
	 * @covers affiliatewp_external_referral_links::get_expiration_time
	 */
	function test_get_expiration_time_should_return_zero_if_cookie_expiration_is_not_set() {
		update_option( 'affiliatewp_external_referral_links', array(
				'cookie_expiration' => null,
		) );

		$cookie_expiration = affiliatewp_external_referral_links()->get_expiration_time();

		$this->assertSame( $cookie_expiration, 0 );
	}

	/**
	 * @covers affiliatewp_external_referral_links::get_expiration_time
	 */
	function test_get_expiration_time_should_return_zero_if_cookie_expiration_is_not_an_integer() {
		update_option( 'affiliatewp_external_referral_links', array(
				'cookie_expiration' => 'this_is_not_an_int',
		) );

		$cookie_expiration = affiliatewp_external_referral_links()->get_expiration_time();

		$this->assertSame( $cookie_expiration, 0 );
	}

	/**
	 * @covers affiliatewp_external_referral_links::get_expiration_time
	 */
	function test_get_expiration_time_should_return_filtered_value() {
		update_option( 'affiliatewp_external_referral_links', array(
				'cookie_expiration' => 10,
		) );
		add_filter( 'affwp_erl_cookie_expiration', function() {
			return 20;
		} );

		$cookie_expiration = affiliatewp_external_referral_links()->get_expiration_time();

		$this->assertSame( $cookie_expiration, 20 );
	}

	/**
	 * @covers affiliatewp_external_referral_links::get_expiration_time
	 */
	function test_get_expiration_time_should_return_zero_if_expiration_time_is_negative() {
		update_option( 'affiliatewp_external_referral_links', array(
				'cookie_expiration' => -10,
		) );

		$cookie_expiration = affiliatewp_external_referral_links()->get_expiration_time();

		$this->assertSame( $cookie_expiration, 0 );
	}

	/**
	 * @covers affiliatewp_external_referral_links::get_expiration_time
	 */
	function test_get_expiration_time_should_return_zero_if_expiration_time_is_negative_string() {
		update_option( 'affiliatewp_external_referral_links', array(
				'cookie_expiration' => "-10",
		) );

		$cookie_expiration = affiliatewp_external_referral_links()->get_expiration_time();

		$this->assertSame( $cookie_expiration, 0 );
	}

	/**
	 * @covers affiliatewp_external_referral_links::plugin_meta
	 */
	function test_plugin_meta_should_add_new_meta_link_when_file_is_plugin_file() {
		$plugin_file = \affiliatewp_external_referral_links::$plugin_dir . 'affiliatewp-external-referral-links.php';
		$links       = affiliatewp_external_referral_links()->plugin_meta( array(), plugin_basename( $plugin_file ) );

		$this->assertNotEmpty( $links );
	}

	/**
	 * @covers affiliatewp_external_referral_links::plugin_meta
	 */
	function test_plugin_meta_should_not_add_new_meta_link_when_file_is_not_plugin_file() {
		$links = affiliatewp_external_referral_links()->plugin_meta( array(), 'file-that-is-not-current-plugin' );

		$this->assertEmpty( $links );
	}

}
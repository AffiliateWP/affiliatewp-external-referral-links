<?php
/**
 * Core: Plugin Bootstrap
 *
 * @package     AffiliateWP External Referral Links
 * @subpackage  Core
 * @copyright   Copyright (c) 2021, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main plugin bootstrap.
 *
 * @since 1.0
 */
final class AffiliateWP_External_Referral_Links {

	/**
	 * Main plugin instance.
	 *
	 * @since 1.0
	 * @var   \AffiliateWP_External_Referral_Links
	 */
	private static $instance;

	/**
	 * Plugin loader file.
	 *
	 * @since 1.1
	 * @var   string
	 */
	private $file = '';

	/**
	 * The version number.
	 *
	 * @since 1.0
	 * @var   string
	 */
	private $version = '1.1';

	/**
	 * Main AffiliateWP_External_Referral_Links Instance
	 *
	 * Insures that only one instance of AffiliateWP_External_Referral_Links exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 *
	 * @param string $file Path to the main plugin file.
	 * @return \AffiliateWP_External_Referral_Links The one true bootstrap instance.
	 */
	public static function instance( $file = '' ) {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_External_Referral_Links ) ) {

			self::$instance = new AffiliateWP_External_Referral_Links;
			self::$instance->file = $file;

			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->hooks();

		}

		return self::$instance;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-external-referral-links' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-external-referral-links' ), '1.0' );
	}

	/**
	 * Sets up plugin constants.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	private function setup_constants() {
		// Plugin version.
		if ( ! defined( 'AFFWP_ERL_VERSION' ) ) {
			define( 'AFFWP_ERL_VERSION', $this->version );
		}

		// Plugin Folder Path.
		if ( ! defined( 'AFFWP_ERL_PLUGIN_DIR' ) ) {
			define( 'AFFWP_ERL_PLUGIN_DIR', plugin_dir_path( $this->file ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'AFFWP_ERL_PLUGIN_URL' ) ) {
			define( 'AFFWP_ERL_PLUGIN_URL', plugin_dir_url( $this->file ) );
		}

		// Plugin Root File.
		if ( ! defined( 'AFFWP_ERL_PLUGIN_FILE' ) ) {
			define( 'AFFWP_ERL_PLUGIN_FILE', $this->file );
		}
	}

	/**
	 * Include necessary files
	 *
	 * @access      private
	 * @since       1.0.0
	 * @return      void
	 */
	private function includes() {
		if ( is_admin() ) {
			// admin page
			require_once AFFWP_ERL_PLUGIN_DIR . 'includes/admin.php';
		}
	}

	/**
	 * Setup the default hooks and actions
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	private function hooks() {

		// load scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

		// plugin meta.
		add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), null, 2 );

		// filter the custom cookie name.
		add_filter( 'affwp_get_cookie_name', array( $this, 'set_cookie_name' ), 10, 2 );

	}

	/**
	 * Get options
	 *
	 * @since 1.0
	 */
	private function get_option( $option = '' ) {
		$options = get_option( 'affiliatewp_external_referral_links' );

		if ( ! isset( $option ) )
			return;

		return $options[$option];
	}


	/**
	 * Get the cookie expiration time in days
	 *
	 * @since 1.0
	 */
	public function get_expiration_time() {
		return apply_filters( 'affwp_erl_cookie_expiration', $this->get_option( 'cookie_expiration' ) );
	}

	/**
	 * Load JS files
	 *
	 * @since 1.0
	 */
	public function load_scripts() {

		// return if no URL is set
		if ( ! $this->get_option('url') ) {
			return;
		}

		wp_enqueue_script( 'affwp-erl', AFFWP_ERL_PLUGIN_URL . 'assets/js/affwp-external-referral-links.min.js', array( 'jquery' ), $this->version );

		// get cookie name.
		$affwp_version = defined( 'AFFILIATEWP_VERSION' ) ? AFFILIATEWP_VERSION : 'undefined';
		if ( version_compare( $affwp_version, '2.7.1', '>=' ) ) {
			$cookie = affiliate_wp()->tracking->get_cookie_name( 'erl-affiliate' );
		} else {
			$cookie = 'affwp_erl_id';
		}

		wp_localize_script( 'affwp-erl', 'affwp_erl_vars', array(
			'cookie_expiration' => $this->get_expiration_time(),
			'referral_variable' => $this->get_option( 'referral_variable' ),
			'url'               => $this->get_option( 'url' ),
			'cookie'            => $cookie,
		));

	}

	/**
	 * Sets the custom cookie name
	 *
	 * @param string $cookie_name The cookie name.
	 * @param string $cookie_type The cookie type.
	 * @return string The final cookie name.
	 */
	public function set_cookie_name( $cookie_name, $cookie_type ) {
		if ( 'erl-affiliate' === $cookie_type ) {
			$cookie_name = 'affwp_erl_id';
		}

		return $cookie_name;
	}

	/**
	 * Modify plugin metalinks
	 *
	 * @access      public
	 * @since       1.0
	 * @param       array $links The current links array
	 * @param       string $file A specific plugin table entry
	 * @return      array $links The modified links array
	 */
	public function plugin_meta( $links, $file ) {
	    if ( $file == plugin_basename( $this->file ) ) {
	        $plugins_link = array(
	            '<a title="' . __( 'Get more add-ons for AffiliateWP', 'affiliatewp-external-referral-links' ) . '" href="http://affiliatewp.com/addons/" target="_blank">' . __( 'Get add-ons', 'affiliatewp-external-referral-links' ) . '</a>'
	        );

	        $links = array_merge( $links, $plugins_link );
	    }

	    return $links;
	}
}

/**
 * The main function responsible for returning the one true AffiliateWP_External_Referral_Links
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $affiliatewp_external_referral_links = affiliatewp_external_referral_links(); ?>
 *
 * @since 1.0
 * @return object The one true AffiliateWP_External_Referral_Links Instance
 */
function affiliatewp_external_referral_links() {
     return AffiliateWP_External_Referral_Links::instance();
}

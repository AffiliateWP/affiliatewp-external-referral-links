<?php
/**
 * Plugin Name: AffiliateWP - External Referral Links
 * Plugin URI: https://affiliatewp.com
 * Description: Allows you to promote external landing pages/sites with the affiliate ID or username appended to the URLs.
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * Version: 1.0.2
 * Text Domain: affiliatewp-external-referral-links
 * Domain Path: languages
 *
 * AffiliateWP is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * AffiliateWP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AffiliateWP. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

final class AffiliateWP_External_Referral_Links {

	/** Singleton *************************************************************/

	/**
	 * @var AffiliateWP_External_Referral_Links The one true AffiliateWP_External_Referral_Links
	 * @since 1.0
	 */
	private static $instance;

	public static  $plugin_dir;
	public static  $plugin_url;
	private static $version;
	private        $expiration_time;

	/**
	 * Main AffiliateWP_External_Referral_Links Instance
	 *
	 * Insures that only one instance of AffiliateWP_External_Referral_Links exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @return The one true AffiliateWP_External_Referral_Links
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_External_Referral_Links ) ) {

			self::$instance   = new AffiliateWP_External_Referral_Links;

			self::$plugin_dir = plugin_dir_path( __FILE__ );
			self::$plugin_url = plugin_dir_url( __FILE__ );
			self::$version    = '1.0.2';

			self::$instance->load_textdomain();
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
	 * Loads the plugin language files
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function load_textdomain() {

		// Set filter for plugin's languages directory
		$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
		$lang_dir = apply_filters( 'aff_wp_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale   = apply_filters( 'plugin_locale',  get_locale(), 'affiliatewp-external-referral-links' );
		$mofile   = sprintf( '%1$s-%2$s.mo', 'affiliatewp-external-referral-links', $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/affiliatewp-external-referral-links/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/affiliatewp-external-referral-links/ folder
			load_textdomain( 'affiliatewp-external-referral-links', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/affiliatewp-external-referral-links/languages/ folder
			load_textdomain( 'affiliatewp-external-referral-links', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'affiliatewp-external-referral-links', false, $lang_dir );
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
			require_once self::$plugin_dir . 'includes/admin.php';
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

		// load scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

		// plugin meta
		add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), null, 2 );

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

		wp_enqueue_script( 'affwp-erl', self::$plugin_url . 'assets/js/affwp-external-referral-links.min.js', array( 'jquery' ), self::$version );

		wp_localize_script( 'affwp-erl', 'affwp_erl_vars', array(
			'cookie_expiration' => $this->get_expiration_time(),
			'referral_variable' => $this->get_option( 'referral_variable' ),
			'url'               => $this->get_option( 'url' )
		));

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
	    if ( $file == plugin_basename( __FILE__ ) ) {
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
add_action( 'plugins_loaded', 'affiliatewp_external_referral_links', 100 );

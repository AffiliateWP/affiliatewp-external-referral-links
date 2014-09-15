<?php
/**
 * Plugin Name: AffiliateWP - Add Referral Links
 * Plugin URI: 
 * Description: Adds Referral links on all links pointing to your ecommerce site
 * Author: Pippin Williamson and Andrew Munro
 * Author URI: http://affiliatewp.com
 * Version: 1.0
 * Text Domain: affiliatewp-add-referral-links
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
 *
 * @package Add Referral Links
 * @category Core
 * @author Andrew Munro
 * @version 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

final class AffiliateWP_Add_Referral_Links {

	/** Singleton *************************************************************/

	/**
	 * @var AffiliateWP_Add_Referral_Links The one true AffiliateWP_Add_Referral_Links
	 * @since 1.0
	 */
	private static $instance;

	public static  $plugin_dir;
	public static  $plugin_url;
	private static $version;
	private        $expiration_time;

	

	/**
	 * Main AffiliateWP_Add_Referral_Links Instance
	 *
	 * Insures that only one instance of AffiliateWP_Add_Referral_Links exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @return The one true AffiliateWP_Add_Referral_Links
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Add_Referral_Links ) ) {
			self::$instance   = new AffiliateWP_Add_Referral_Links;

			self::$plugin_dir = plugin_dir_path( __FILE__ );
			self::$plugin_url = plugin_dir_url( __FILE__ );
			self::$version    = '1.0';

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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-add-referral-links' ), '1.0' );
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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-add-referral-links' ), '1.0' );
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
		$lang_dir = apply_filters( 'affiliatewp_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale   = apply_filters( 'plugin_locale',  get_locale(), 'affiliatewp-add-referral-links' );
		$mofile   = sprintf( '%1$s-%2$s.mo', 'affiliatewp-add-referral-links', $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/affiliatewp-add-referral-links/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/affiliatewp-add-referral-links/ folder
			load_textdomain( 'affiliatewp-add-referral-links', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/affiliatewp-add-referral-links/languages/ folder
			load_textdomain( 'affiliatewp-add-referral-links', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'affiliatewp-add-referral-links', false, $lang_dir );
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

		add_action( 'wp_head', array( $this, 'header_scripts' ) );

		// load scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

		// plugin meta
		add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), null, 2 );

	}

	/**
	 * Output header scripts
	 *
	 * @since 1.0
	 */
	public function header_scripts() {
		
		// return if no URL is set
		if ( ! $this->get_option('url') ) {
			return;
		}

?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {

			// eg "ref"
			var referral_variable = "<?php echo $this->get_option('referral_variable'); ?>";

			// get the cookie value
			var cookie = $.cookie( 'affwp_aff_id' );

			// get the value of the referral variable from the query string
			var ref    = affiliatewp_arl_get_query_vars()[referral_variable];

			// if ref exists but cookie doesn't, set cookie with value of ref
			if ( ref && ! cookie ) {
				var cookie_value = ref;

				// Set the cookie and expire it after 24 hours
				$.cookie( 'affwp_aff_id', cookie_value, { expires: <?php echo $this->get_expiration_time(); ?>, path: '/' } );
			}
			
			// split up the query string and return the parts
			function affiliatewp_arl_get_query_vars() {
				var vars = [], hash;
				var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
				for (var i = 0; i < hashes.length; i++) {
					hash = hashes[i].split('=');
					vars.push(hash[0]);
					vars[hash[0]] = hash[1];
				}
				return vars;
			}

			// the affiliate ID will usually be the value of the cookie, but on first page load we'll grab it from the query string
			if ( cookie ) {
				affiliate_id = cookie;
			} else {
				affiliate_id = ref;
			}

			if ( affiliate_id ) {
				// get all the targeted URLs on the page that start with the specific URL
				var target_urls = $("a[href^='<?php echo $this->get_option('url'); ?>']");

				// modify each target URL on the page
				$(target_urls).each( function() {
					
					// get the current href of the link
					current_url = $(this).attr('href');
					
					// append a slash to the URL if it doesn't exist
					current_url = current_url.replace(/\/?$/, '/');

					// modify the anchor's href to include our query string
					$(this).attr('href', current_url + '?' + referral_variable + '=' + affiliate_id );

				});

			}
			
		});
		</script>
<?php
	}
	
	/**
	 * Get options
	 *
	 * @since 1.0
	 */
	private function get_option( $option = '' ) {
		$options = get_option( 'affiliatewp_add_referral_links' );

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
		return apply_filters( 'affiliatewp_add_referral_links_cookie_expiration', $this->get_option('cookie_expiration') );
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

		wp_enqueue_script( 'jquery-cookie', self::$plugin_url . 'assets/js/jquery.cookie.js', array( 'jquery' ), '1.4.0' );
	}	

	/**
	 * Modify plugin metalinks
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       array $links The current links array
	 * @param       string $file A specific plugin table entry
	 * @return      array $links The modified links array
	 */
	public function plugin_meta( $links, $file ) {
	    if ( $file == plugin_basename( __FILE__ ) ) {
	        $plugins_link = array(
	            '<a title="' . __( 'Get more add-ons for AffiliateWP', 'affiliatewp-add-referral-links' ) . '" href="http://affiliatewp.com/addons/" target="_blank">' . __( 'Get add-ons', 'affiliatewp-add-referral-links' ) . '</a>'
	        );

	        $links = array_merge( $links, $plugins_link );
	    }

	    return $links;
	}
}

/**
 * The main function responsible for returning the one true AffiliateWP_Add_Referral_Links
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $affiliatewp_add_referral_links = affiliatewp_add_referral_links(); ?>
 *
 * @since 1.0
 * @return object The one true AffiliateWP_Add_Referral_Links Instance
 */
function affiliatewp_add_referral_links() {
     return AffiliateWP_Add_Referral_Links::instance();
}
add_action( 'plugins_loaded', 'affiliatewp_add_referral_links', 100 );
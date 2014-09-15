<?php

class AffiliateWP_Add_Referral_Links_Admin {
	
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'settings' ) );
		add_filter( 'affiliatewp_add_referral_links_sanitize', array( $this, 'sanitize_url_field' ), 10, 2 );
	}

	public function register_menu() {
		add_options_page( __( 'Add Referral Links', 'affiliatewp-add-referral-links' ), __( 'Add Referral Links', 'affiliatewp-add-referral-links' ), 'manage_options', 'add-referral-links', array( $this, 'admin_page' ) );	
	}

	public function admin_page() { ?>
    <div class="wrap">
    	 <?php screen_icon( 'plugins' ); ?>
        <h2><?php _e( 'AffiliateWP - Add Referral Links', 'affiliatewp-add-referral-links' ); ?></h2>

        <form action="options.php" method="POST">
            <?php 
	            settings_fields( 'affiliatewp_add_referral_links' );
	            do_settings_sections( 'affiliatewp_add_referral_links' );
            ?>

            <?php submit_button(); ?>
        </form>

    </div>
	<?php }

	/**
	 * Default values
	 */
	public function default_options() {
		
		$defaults = array(
			'cookie_expiration'	=> '1',
			'referral_variable' => 'ref',
			'url'               => ''
		);
		
		return apply_filters( 'affiliatewp_add_referral_links_default_options', $defaults );
		
	}

	public function settings() {

		if ( false == get_option( 'affiliatewp_add_referral_links' ) ) {	
			add_option( 'affiliatewp_add_referral_links', $this->default_options() );
		}

		add_settings_section(
			'input_examples_section',
			'',
			'',
			'affiliatewp_add_referral_links'
		);
		
		// Cookie Expiration
		add_settings_field(	
			'Cookie Expiration',						
			__( 'Cookie Expiration', 'affiliatewp_add_referral_links' ),							
			array( $this, 'callback_number_input' ),	
			'affiliatewp_add_referral_links',	
			'input_examples_section',
			array( 'name' => 'cookie_expiration', 'id' => 'cookie-expiration', 'description' => __( 'How many days should the referral tracking cookie be valid for?', 'affiliatewp-add-referral-links' ) )			
		);
		
		// Referral Variable
		// Must match the referral variable used on your ecommerce site where AffiliateWP is installed
		add_settings_field(	
			'Referral Variable',						
			__( 'Referral Variable', 'affiliatewp_add_referral_links' ),							
			array( $this, 'callback_input' ),	
			'affiliatewp_add_referral_links',	
			'input_examples_section',
			array( 'name' => 'referral_variable', 'id' => 'referral-variable', 'description' => __( 'The referral variable you have set in AffiliateWP.', 'affiliatewp-add-referral-links' ) )		
		);

		// URL to search for
		add_settings_field(	
			'Store URL',						
			__( 'Store URL', 'affiliatewp_add_referral_links' ),							
			array( $this, 'callback_input' ),	
			'affiliatewp_add_referral_links',	
			'input_examples_section',
			array( 'name' => 'url', 'id' => 'url', 'description' => __( 'The URL where AffiliateWP and your ecommerce system are installed.', 'affiliatewp-add-referral-links' ) )		
		);

		register_setting(
			'affiliatewp_add_referral_links',
			'affiliatewp_add_referral_links',
			array( $this, 'sanitize' )
		);

	}

	/**
	 * Input field callback
	 */
	public function callback_input( $args ) {
		
		$options = get_option( 'affiliatewp_add_referral_links' );
		$value = isset( $options[$args['name']] ) ? $options[$args['name']] : '';
	?>
		<input type="text" id="<?php echo $args['id']; ?>" name="affiliatewp_add_referral_links[<?php echo $args['name']; ?>]" value="<?php echo $value; ?>" />

		<?php if ( isset( $args['description'] ) ) : ?>
			<p class="description"><?php echo $args['description']; ?></p>
		<?php endif; ?>
		<?php
		
	}

	/**
	 * Number Input field callback
	 */
	public function callback_number_input( $args ) {
		
		$options = get_option( 'affiliatewp_add_referral_links' );
		$value = isset( $options[$args['name']] ) ? $options[$args['name']] : '';
	?>
		<input type="number" id="<?php echo $args['id']; ?>" name="affiliatewp_add_referral_links[<?php echo $args['name']; ?>]" value="<?php echo $value; ?>" class="small-text" min="0" max="999999" step="1" />

		<?php if ( isset( $args['description'] ) ) : ?>
			<p class="description"><?php echo $args['description']; ?></p>
		<?php endif; ?>
		<?php
		
	}

	/**
	 * Sanitization callback
	 */
	public function sanitize( $input ) {

		// Create our array for storing the validated options
		$output = array();
		
		// Loop through each of the incoming options
		foreach ( $input as $key => $value ) {
			
			// Check to see if the current option has a value. If so, process it.
			if ( isset( $input[$key] ) ) {
			
				// Strip all HTML and PHP tags and properly handle quoted strings
				$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );
				
			} 
			
		} 
		
		// Return the array processing any additional functions filtered by this action
		return apply_filters( 'affiliatewp_add_referral_links_sanitize', $output, $input );

	}

	public function sanitize_url_field( $output, $input ) {

		// remove the trailing slash if present and sanitize URL
		if ( isset( $input['url'] ) ) {
			$output['url'] = untrailingslashit( esc_url_raw( $output['url'] ) );
		}

		return $output;

	}
	
}
$affiliatewp_menu = new AffiliateWP_Add_Referral_Links_Admin;
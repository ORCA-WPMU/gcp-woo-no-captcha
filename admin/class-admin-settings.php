<?php 
/**
 * Defines the functionality required to render the content within the admin settings panel
 * to which this display belongs.
 */
namespace GcpWooNoCaptcha\Admin;

defined( 'ABSPATH' ) or die( 'not found' );

/**
* Admin Settings Page
*/
class Admin_Settings
{
    private static $_options = false;
    private static $_default = array(
        'site_key' => '',
        'secret_key' => '',
        'error_message' => 'Please retry reCAPTCHA',
    );
    private static $_instance = false;
    
    // Page Slug ID
    const PAGEID = 'gcp-woo-no-captcha-settings';
 	
 	// the main settings group ID	
 	const API_SETTING_SECTION = 'gwnc_api_setting_section';
 	const DISPLAY_SETTING_SECTION = 'gwnc_display_setting_section';

 	// the options settings post array name
 	const SETTINGS_OPTION_NAME = 'gwnc_api_settings';

    /**
     * Constructon adds hooks that load the admin page.
     *
     * @return void
     **/
    private function __construct()
    {        
        // Add Admin Area
        add_action('admin_init', [ $this, 'page_init' ] ); 
        add_action('admin_menu', [ $this, 'add_plugin_page' ] );
        
        $plugin = plugin_basename( GWNC_PLUGIN_FILE );
        add_filter("plugin_action_links_$plugin", [ $this, 'settings_link' ] );

        self::$_options = get_option( self::SETTINGS_OPTION_NAME, $_default );
    }

    /**
     * option getter
     *
     * @return option
     **/
    public static function get_option($key, $default = '')
    {   
        if ( empty( self::$_options ) )
            self::$_options = get_option( self::SETTINGS_OPTION_NAME );

        return isset( self::$_options[$key] ) ? self::$_options[$key] : $default;
    }

    // Add settings link on plugin page
    public static function settings_link($links) { 
        $settings_link = '<a href="options-general.php?page='.self::PAGEID.'">'.__('Settings','gcp-woo-no-captcha').'</a>'; 
        array_unshift($links, $settings_link); 
        return $links; 
    }

    /**
     * Singlton
     *
     * @return instance
     * @static 
     **/
    public static function init()
    {
        if ( ! self::$_instance )
            self::$_instance = new Admin_Settings;
        return self::$_instance;
    }

    /**
     * 'admin_menu' Action callback fn registers plugin settings 
     *
     * @return void
     **/
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            __('No CAPTCHA Settings', 'gcp-woo-no-captcha' ), 
            __('No CAPTCHA', 'gcp-woo-no-captcha' ),
            'manage_options', 
            self::PAGEID, 
            [$this, 'settings_page']
        );
    }
    public static function page_init()
    {
        self::$_options = get_option( self::SETTINGS_OPTION_NAME );
        
        // Register the Option Group for our settings and attach our input validator.
        register_setting(
            'gcp-woo-no-captcha-option-group', // Option group
            self::SETTINGS_OPTION_NAME, // Option name
            [$this,'sanitize']  // Sanitize
        );

        add_settings_section(
            self::API_SETTING_SECTION, // ID
            __('Google NoCAPTCHA API Keys', 'gcp-woo-no-captcha'), // Title
            [ $this, 'print_api_section_text' ], // Callback
            self::PAGEID // Page
        );  
        add_settings_field(
            'site_key', // ID
            'Public / Site Key', // Title 
            [$this,'input_field_callback'], // Callback
            self::PAGEID, // Page
            self::API_SETTING_SECTION, // Section         
            ['key' => 'site_key']
        );
        add_settings_field(
            'secret_key', // ID
            'Secret Key', // Title 
            [$this,'input_field_callback'], // Callback
            self::PAGEID, // Page
            self::API_SETTING_SECTION, // Section         
            ['key' => 'secret_key']
        ); 

        add_settings_section(
            self::DISPLAY_SETTING_SECTION, // ID
            __('Display Option', 'gcp-woo-no-captcha'), // Title
            function () {}, // Callback
            self::PAGEID // Page
        ); 
        add_settings_field(
            'error_message', // ID
            'Error Message', // Title 
            [$this,'input_field_callback'], // Callback
            self::PAGEID, // Page
            self::DISPLAY_SETTING_SECTION, // Section 
            ['key' => 'error_message', 'description' =>  __( 'Message or text to display when CAPTCHA is ignored or the test is failed.', 'wc-no-captcha' )]          
        );
        // add_settings_field(
        //     'link_text', // ID
        //     'Link Text', // Title 
        //     [$this,'link_text_callback'], // Callback
        //     self::PAGEID, // Page
        //     self::API_SETTING_SECTION // Section           
        // );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     * @return array Stantized input fields.
     */
    public static function sanitize( $input )
    {
        $new_input = array();
  
        if( isset( $input['site_key'] ) )
            $new_input['site_key'] = sanitize_text_field( $input['site_key'] );
        
        if( isset( $input['secret_key'] ) )
            $new_input['secret_key'] = sanitize_text_field( $input['secret_key'] );
        
        if( isset( $input['login_url'] ) )
            $new_input['login_url'] = sanitize_text_field( $input['login_url'] );
        
        if( isset( $input['error_message'] ) )
            $new_input['error_message'] = htmlentities(stripslashes( $input['error_message'] ) );
        
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public static function print_api_section_text()
    {
    	_e( 'Public and Private keys used to displaying the CAPTCHA using Googles NoCAPTCHA API. signup <a href="https://www.google.com/recaptcha/admin" target="_blank">Here</a>', 'gcp-woo-no-captcha');
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public static function input_field_callback( $args )
    {
    	$description = isset( $args['description'] ) ? $args['description'] : NULL;
    	$this->print_input_field( $args['key'], $description );
    }

    /**
     * Renders (prints) an input field for the an option key
     *
     * @var string $key the option array key
     * @var string $descripiton (optional) the field help text
     **/
    private function print_input_field( $key, $description = null )
    {
        printf(
            '<input class="regular-text code" type="text" id="%s" name="%s[%s]" value="%s" />',
            $key, //ID
            self::SETTINGS_OPTION_NAME, //name array
            $key, // name key
            isset( self::$_options[$key] ) ? esc_attr( self::$_options[$key] ) : '' // value
        );
        if ( isset($description) ) 
	        print "<p class=\"description\">{$description}</p>";
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public static function link_text_callback()
    {
        print "<p class=\"description\">The text to be displayed as the html link to zimbra</p>";
        ?><p>
		<textarea name="zimbrapreauth[link_text]" rows="10" cols="50" id="link_text" class="large-text code"><?php 
			print html_entity_decode( 
				isset( self::$_options['link_text'] ) ? self::$_options['link_text'] : self::$_default['link_text'] ); 
		?></textarea>
		</p><?php
    }

    /**
     * Renders the Admin Settings Page
     *
     * @return void
     **/
    public static function settings_page()
    { ?>
        <div class="wrap">
            <h2><?php _e('No Captcha Settings', 'gcp-woo-no-captcha') ?></h2>
            <form method="post" action="options.php"> 
            <?php settings_fields( 'gcp-woo-no-captcha-option-group' ); ?>
            <?php do_settings_sections( self::PAGEID ); ?>
            <?php submit_button(); ?>
            </form>
        </div>
    <?php }
}
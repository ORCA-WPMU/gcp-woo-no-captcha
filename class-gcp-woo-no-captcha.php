<?php 
/**
  * GcpWooNoCaptcha Main Plugin Class and entry point
  *
  * @package GcpWooNoCaptcha
  **/
namespace GcpWooNoCaptcha;

defined( 'ABSPATH' ) or die( 'not found' );

use GcpWooNoCaptcha\Admin\Admin_Settings as Settings;
use GcpWooNoCaptcha\View\Login;
use GcpWooNoCaptcha\View\Register;
use GcpWooNoCaptcha\View\ResetPassword;

class GCP_Woo_No_Captcha
{	
	private static $_instance = null;

	/**
     * Singlton
     *
     * @return instance
     * @static 
     **/
	public static function init()
	{
		if ( ! self::$_instance )
        	self::$_instance = new GCP_Woo_No_Captcha();
    	return self::$_instance;
	}

	 /**
     * Constructon adds hooks and init's the plugin functionality.
     * 
     * @return void
     **/
	private function __construct()
	{
		add_action( 'plugins_loaded', [ $this, 'load_plugin_textdomain' ] );

		add_action('wp_loaded', [ $this, 'loaded'] );

		// Fudge fix to add 'async auto' attributed to the script include whilst still using enqueue api
		add_filter( 'clean_url', [ __CLASS__, 'async_scripts' ], 11, 1 );

		$l = new Login();
		$r = new Register();
		$p = new ResetPassword();
	}

	/**
	 * Inits plugin parts once wordpress & plugins have loaded fully
	 *
	 * @return void
	 **/
	public function loaded()
	{	
		if( is_admin() )
			Settings::init();

		if ( !is_admin() AND !is_user_logged_in() ) 
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts'] );
	}

	/**
	 * Load Text Domain and translations
	 *
	 * @return void
	 **/
	public function load_plugin_textdomain()
	{
		load_plugin_textdomain( 'gcp-woo-no-captcha', false, basename( dirname( GWNC_PLUGIN_FILE ) ) . '/lang/' );
	}

	/**
	 * registers and enqueues google recaptcha js script
	 *
	 * @return void
	 **/
	public function enqueue_scripts()
	{
		wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js?onload=reCaptchaRender&render=explicit#gcpasync', [], null, false );

		$script = "var reCaptchaRender = function() {".
			"
			var loginWidget = document.getElementById('login-recaptcha');  
			if( typeof(loginWidget) != 'undefined' && loginWidget != null )
			{
			  	loginWidget = grecaptcha.render('login-recaptcha', { 'sitekey' : '{".Settings::get_option('site_key')."}' });
			}".
			"
			var registerWidget = document.getElementById('register-recaptcha');  
			if( typeof(registerWidget) != 'undefined' && registerWidget != null )
			{
			  	registerWidget = grecaptcha.render('register-recaptcha', { 'sitekey' : '{".Settings::get_option('site_key')."}' });
			}".
			"
			var restpwWidget = document.getElementById('resetpw-recaptcha');  
			if( typeof(restpwWidget) != 'undefined' && restpwWidget != null )
			{
			  	restpwWidget = grecaptcha.render('resetpw-recaptcha', { 'sitekey' : '{".Settings::get_option('site_key')."}' });
			}".
    		"};";


		wp_add_inline_script( 'google-recaptcha', $script );
	}

	/**
	 * Fudge to filter and add add defer and async tags to enqueues script encluded with the id #gcpasync added to the url
	 *
	 * @param string $url the url to be filtered.
	 * @return string $url returns the modifed url with defer and async tags added where needed 
	 **/
	public static function async_scripts( $url )
	{
	    if ( strpos( $url, '#gcpasync') === false )
	        return $url;
	    else if ( is_admin() )
	        return str_replace( '#gcpasync', '', $url );
	    else
		return str_replace( '#gcpasync', '', $url )."' defer async='async"; 
	}

} // END class GCP_Woo_No_Captcha ?>
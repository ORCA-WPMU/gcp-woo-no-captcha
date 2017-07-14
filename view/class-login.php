<?php 
/**
 * Attached / Renders / Processes Captcha for Login Screens
 *
 * @package GcpWooNoCaptcha
 **/
namespace GcpWooNoCaptcha\View;

defined('ABSPATH') or die('not found');

class Login extends View
{	
	protected $captcha_id = 'login-recaptcha';

	function __construct()
	{
		// adds the captcha to the login form
		add_filter( 'woocommerce_login_form', [ $this, 'display_captcha' ] );

		// authenticate the captcha answer
		add_action( 'woocommerce_process_login_errors', [ $this, 'validate_captcha' ], 10, 2 );	
	}
} // END class Login extends View ?>
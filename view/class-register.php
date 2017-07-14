<?php 
/**
 * Attached / Renders / Processes Captcha for Register Screens
 *
 * @package GcpWooNoCaptcha
 **/
namespace GcpWooNoCaptcha\View;

defined('ABSPATH') or die('not found');

class Register extends View
{	
	protected $captcha_id = 'register-recaptcha';

	function __construct()
	{
		// adds the captcha to the login form
		add_filter( 'woocommerce_register_form', [ $this, 'display_captcha' ] );

		// authenticate the captcha answer
		add_action( 'woocommerce_registration_errors', [ $this, 'validate_captcha' ], 10, 2 );	
	}
} // END class Login extends View ?>
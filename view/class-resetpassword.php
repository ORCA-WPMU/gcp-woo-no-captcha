<?php 
/**
 * Attached / Renders / Processes Captcha for Login Screens
 *
 * @package GcpWooNoCaptcha
 **/
namespace GcpWooNoCaptcha\View;

defined('ABSPATH') or die('not found');

class ResetPassword extends View
{	
	protected $captcha_id = 'resetpw-recaptcha';

	function __construct()
	{
		// adds the captcha to the login form
		add_filter( 'woocommerce_lostpassword_form', [ $this, 'display_captcha' ] );

		// authenticate the captcha answer
		add_action( 'allow_password_reset', [ $this, 'validate_captcha' ], 10, 2 );	
	}
} // END class Login extends View ?>
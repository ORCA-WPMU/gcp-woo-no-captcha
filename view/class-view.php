<?php 
/**
  * undocumented class
  *
  * @package GcpWooNoCaptcha
  **/
namespace GcpWooNoCaptcha\View;

use GcpWooNoCaptcha\Admin\Admin_Settings as Settings;

defined('ABSPATH') or die('not found');

 Abstract class View
 {
 	protected $captcha_id = 'g-recaptcha';

 	public function display_captcha()
 	{
 		printf( "<div id='{$this->captcha_id}' data-sitekey='%s'></div>", Settings::get_option('site_key') );
 	}

 	public function validate_captcha( $user, $password )
 	{
 		if ( !is_checkout() && ( !isset( $_POST['g-recaptcha-response'] ) || !$this->captcha_verification() ) ) {
			return new \WP_Error( 'empty_captcha', 'Please retry reCAPTCHA'); //Settings::get_option('error_message') );
		}
		return $user;
 	}

 	public function captcha_verification()
 	{
 		$remote_ip = $_SERVER["REMOTE_ADDR"];
 		$response = isset( $_POST['g-recaptcha-response'] ) ? esc_attr( $_POST['g-recaptcha-response'] ) : '';

		// make a GET request to the Google reCAPTCHA Server
		$request = wp_remote_get(
			sprintf( "https://www.google.com/recaptcha/api/siteverify?secret=%s&response=%s&remoteip=%s",
				Settings::get_option('secret_key'), $response, $remote_ip )
		);

		$result = json_decode( wp_remote_retrieve_body( $request ), true );
		return  $result['success'];
 	}
 } // END class View ?>
<?php 
/**
 * The plugin bootstrap file
 * @package GcpWooNoCaptcha
 *
 * Plugin Name: WooCoomerce NoCaptura for WPMU
 * Plugin URI: https://github.com/geraintp/gcp-woo-no-captcha
 * Description: Adds the No CAPTCHA reCAPTCHA to WooCommerce login and registration form, tested for WPMU and register on checkout support
 * Version: 0.1.0
 * Author: @geraintp
 * Author URI: http://geraint.co
 * License: GPL2
 * Text Domain: gcp-woo-no-captcha
 * Domain Path: /lang/
 */
namespace GcpWooNoCaptcha;

defined( 'ABSPATH' ) or die( 'not found' );

define( 'GWNC_MINIMUM_WP_VERSION', '4.8' );
define( 'GWNC_VERSION', '0.1.0' );
define( 'GWNC_PLUGIN_FILE', __FILE__ );
define( 'GWNC_PLUGIN_DIR',  plugin_dir_path( GWNC_PLUGIN_FILE ) );

// Include the autoloader so we can dynamically include the rest of the classes.
require_once( trailingslashit( dirname( __FILE__ ) ) . 'inc/autoloader.php' );

Gcp_Woo_No_Captcha::init();

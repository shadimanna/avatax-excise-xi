<?php
/**
 * Plugin Name: Avatax for WooCommerce
 * Plugin URI: https://github.com/shadimanna/avatax-excise-xi
 * Description:
 * Author: Progressus
 * Author URI: #
 * Tested up to: 6.2
 * WC requires at least: 4.0
 * WC tested up to: 7.6
 *
 * @package AvataxWooCommerce
 */

namespace AvataxWooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'AVATAX_WC_PLUGIN_FILE' ) ) {
	define( 'AVATAX_WC_PLUGIN_FILE', __FILE__ );
}

require_once ( plugin_dir_path( AVATAX_WC_PLUGIN_FILE ) . '/vendor/autoload.php' );

/**
 * Main Avatax for WooCommerce.
 *
 * @return Main instance.
 */
function avatax() {
	return Main::instance();
}
add_action( 'plugins_loaded', 'AvataxWooCommerce\avatax' );
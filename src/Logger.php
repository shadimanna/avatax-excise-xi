<?php
/**
 * Class Logger file.
 *
 * @package AvataxWooCommerce
 */

namespace AvataxWooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Logger
 *
 * @package AvataxWooCommerce
 */
class Logger {

	/**
	 * Debug flag.
	 */
	private $debug;

	/**
	 * Logger constructor.
	 *
	 * @param  boolean  $debug  debug flag.
	 */
	public function __construct( $debug ) {
		$this->debug = $debug;
	}

	/**
	 * Check if logging is enabled.
	 *
	 * @return bool.
	 */
	public function is_enabled() {
		return empty( $this->debug ) || false === is_bool( $this->debug ) ? false : $this->debug;
	}

	/**
	 * Write the message to log.
	 *
	 * @param  Mixed  $message  Message to be written in log.
	 */
	public function write( $message ) {
		// Check if enabled.
		if ( ! $this->is_enabled() ) {
			return;
		}

		$message = apply_filters( 'avatax_logger_write_message', $message );

		if ( is_array( $message ) || is_object( $message ) ) {
			$message = print_r( $message, true );
		}

		// Logger object.
		$wc_logger = new \WC_Logger();

		// Add to logger.
		$wc_logger->add( 'AvataxWooCommerce', $message );
	}

	/**
	 * Get log URL.
	 */
	public static function get_log_url() {
		return admin_url( 'admin.php?page=wc-status&tab=logs' );
	}

}
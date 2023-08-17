<?php
/**
 * Class Rest_API\Transactions\Void_Transaction file.
 *
 * @package AvataxWooCommerce\Rest_API\Transaction
 */

namespace AvataxWooCommerce\Rest_API\Transaction;

use AvataxWooCommerce\Main;
use AvataxWooCommerce\Rest_API\Base;
use AvataxWooCommerce\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Void_Transaction
 *
 * @package AvataxWooCommerce\Rest_API\Transaction
 */
class Void_Transaction extends Base {
	/**
	 * API Endpoint.
	 *
	 * @var string
	 */
	public $endpoint = '/api/v1/AvaTaxExcise/transactions/{ID}/void';

	/**
	 * Class constructor.
	 *
	 */
	public function __construct( $avatax_id ) {
		$this->settings = Settings::get_instance();
		$this->logger   = ( new Main )->get_logger();

		$this->endpoint =  str_replace('{ID}', $avatax_id, $this->endpoint );
		$this->check_api_mode();
		$this->set_api_url();
	}
}

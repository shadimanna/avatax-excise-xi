<?php
/**
 * Class Rest_API\Transactions\Commit file.
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
 * Class Commit
 *
 * @package AvataxWooCommerce\Rest_API\Transaction
 */
class Commit extends Base {
	/**
	 * API Endpoint.
	 *
	 * @var string
	 */
	public $endpoint = '/api/v1/AvaTaxExcise/transactions/{ID}/commit';

	/**
	 * Class constructor.
	 *
	 */
	public function __construct( $avatax_id ) {
		$this->settings = Settings::get_instance();
		$this->logger   = ( new Main )->get_logger();

		$this->endpoint =  str_replace('{ID}', $avatax_id,$this->endpoint );;
		$this->check_api_mode();
		$this->set_api_url();
	}
}

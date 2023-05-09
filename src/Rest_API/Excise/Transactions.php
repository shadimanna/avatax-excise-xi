<?php
/**
 * Class Rest_API\Excise\Transactions file.
 *
 * @package AvataxWooCommerce\Rest_API\Excise
 */

namespace AvataxWooCommerce\Rest_API\Excise;

use AvataxWooCommerce\Rest_API\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Client
 *
 * @package AvataxWooCommerce\Rest_API\Barcode
 */
class Transactions extends Base {

	/**
	 * API Endpoint.
	 *
	 * @var string
	 */
	public $endpoint = '/api/v1/AvaTaxExcise/transactions/create';

	/**
	 * Function for composing API request in the URL for GET request.
	 *
	 * @return array.
	 */
	public function compose_url_params() {
		$current_time = current_time( 'Y-m-d H:i:s' );

		return array(
			'TransactionLines' => array(),
			'EffectiveDate'    => $current_time,
			'InvoiceDate'      => $current_time,
			'InvoiceNumber'    => uniqid(), //random cart id
		);
	}
}

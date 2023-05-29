<?php
/**
 * Class Rest_API\Excise\Transactions file.
 *
 * @package AvataxWooCommerce\Rest_API\Transaction
 */

namespace AvataxWooCommerce\Rest_API\Transaction;

use AvataxWooCommerce\Rest_API\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Client
 *
 * @package AvataxWooCommerce\Rest_API\Transaction
 */
class Create extends Base {

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
	public function compose_body_request() {
		$current_time = current_time( 'Y-m-d H:i:s' );

		return array(
			'TransactionLines' => $this->item_info->transaction_lines,
			'TransactionType'  => apply_filters( 'avatax_transaction_type', 'WHOLESALE' ),
			'EffectiveDate'    => $current_time,
			'InvoiceDate'      => $current_time,
			'InvoiceNumber'    => substr( uniqid(), 0, 10 ), // Random cart id
		);
	}
}

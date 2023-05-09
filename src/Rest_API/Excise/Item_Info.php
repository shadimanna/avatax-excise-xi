<?php
/**
 * Class Rest_API\Excise\Item_Info file.
 *
 * @package AvataxWooCommerce\Rest_API\Excise
 */

namespace AvataxWooCommerce\Rest_API\Excise;

use AvataxWooCommerce\Rest_API\Base_Info;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Item_Info
 *
 * @package AvataxWooCommerce\Rest_API\Excise
 */
class Item_Info extends Base_Info {
	/**
	 * API args.
	 *
	 * @var array.
	 */
	protected $api_args;

	/**
	 * Transaction Lines data.
	 *
	 * @var array.
	 */
	public $transaction_lines;

	/**
	 * Parses the arguments and sets the instance's properties.
	 *
	 * @throws \Exception If some data in $args did not pass validation.
	 */
	protected function parse_args() {
		$this->transaction_lines = array(); //replace with parser
	}


	/**
	 * Method to convert cart data to API args.
	 *
	 * @param  array  $data.
	 */
	public function convert_data_to_args( $data ) {

		// convert Cart data to api args.
		$this->api_args = array(
			'line_items' => array(),
		);
	}

	/**
	 * Retrieves the args scheme to use with for parsing cart line items.
	 *
	 * @return array.
	 */
	protected function get_line_items_schema() {
		// Closures in PHP 5.3 do not inherit class context
		// So we need to copy $this into a lexical variable and pass it to closures manually.
		$self = $this;

		return array(
			'qty'   => array(
				'rename' => 'InvoiceLine',
			),
		);
	}
}

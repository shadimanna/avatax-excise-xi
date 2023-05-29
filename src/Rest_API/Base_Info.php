<?php
/**
 * Class Rest_API\Base_Info file.
 *
 * @package AvataxWooCommerce\Rest_API
 */

namespace AvataxWooCommerce\Rest_API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Base_Info
 *
 * @package AvataxWooCommerce\Rest_API
 */
abstract class Base_Info {
	/**
	 * API args.
	 *
	 * @var array.
	 */
	protected array $api_args;

	/**
	 * Class constructor.
	 *
	 * @param  array  $data  Set of API arguments.
	 *
	 * @throws \Exception
	 */
	public function __construct( $data ) {
		$this->convert_data_to_args( $data );
		$this->set_extra_data_to_api_args();
		$this->set_store_address_data();
		$this->parse_args();
	}

	/**
	 * Parses the arguments and sets the instance's properties.
	 *
	 * @throws \Exception If some data in $args did not pass validation.
	 */
	abstract protected function parse_args();

	/**
	 * Method to convert the data array to API args.
	 *
	 * @param  array  $data.
	 */
	abstract public function convert_data_to_args( $data );

	/**
	 * Set extra API args.
	 */
	public function set_extra_data_to_api_args() {
	}

	/**
	 * Set API args with store address data from WC settings.
	 */
	public function set_store_address_data() {
		$this->api_args['store_address'] = array(
			'city'      => WC()->countries->get_base_city(),
			'state'     => WC()->countries->get_base_state(),
			'country'   => WC()->countries->get_base_country(),
			'postcode'  => WC()->countries->get_base_postcode(),
		);
	}
}

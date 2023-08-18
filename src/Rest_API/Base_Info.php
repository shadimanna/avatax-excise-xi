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
	public array $api_args;

	/**
	 * Class constructor.
	 *
	 * @param  array  $data  Set of API arguments.
	 *
	 * @throws \Exception
	 */
	public function __construct( $data ) {
		$this->set_store_address_data();
		$this->convert_data_to_args( $data );
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
	 * Set API args with store address data from WC settings.
	 */
	public function set_store_address_data() {
		$this->api_args['store_address'] = array(
			'city'     => WC()->countries->get_base_city(),
			'state'    => WC()->countries->get_base_state(),
			'country'  => WC()->countries->get_base_country(),
			'postcode' => WC()->countries->get_base_postcode(),
		);


		// Sale Address
		if ( 'yes' !== get_option( 'avatax_use_store_address_as_sale' ) ) {
			$this->api_args['sale_address'] = $this->api_args['store_address'];
		} else {
			$this->api_args['sale_address'] = array(
				'city'     => get_option( 'avatax_sale_address_city' ),
				'state'    => get_option( 'avatax_sale_address_state' ),
				'country'  => get_option( 'avatax_sale_address_country' ),
				'postcode' => get_option( 'avatax_sale_address_postcode' ),
			);
		}
	}
}

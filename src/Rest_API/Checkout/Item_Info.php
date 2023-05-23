<?php
/**
 * Class Rest_API\Checkout\Item_Info file.
 *
 * @package AvataxWooCommerce\Rest_API\Checkout
 */

namespace AvataxWooCommerce\Rest_API\Checkout;

use AvataxWooCommerce\Address_Utils;
use AvataxWooCommerce\Rest_API\Base_Info;
use AvataxWooCommerce\billing_Method\Settings;
use AvataxWooCommerce\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Item_Info
 *
 * @package AvataxWooCommerce\Rest_API\Checkout
 */
class Item_Info extends Base_Info {
	/**
	 * API args.
	 *
	 * @var api_args
	 */
	protected $api_args;

	/**
	 * Settings class instance.
	 *
	 * @var AvataxWooCommerce\billing_Method\Settings
	 */
	protected $settings;

	/**
	 * Body of the item info.
	 *
	 * @var body
	 */
	public $body;

	/**
	 * Shipper data of the item info.
	 *
	 * @var shipper
	 */
	public $shipper;

	/**
	 * Receiver data of the item info.
	 *
	 * @var receiver
	 */
	public $receiver;



	/**
	 * Method to convert the post data to API args.
	 *
	 * @param Array $post_data Data from post variable in checkout page.
	 */
	public function convert_data_to_args( $post_data ) {
		$post_data = Address_Utils::set_post_data_address( $post_data );

		$this->api_args['billing_address'] = array(
			'first_name' => ( ! empty( $post_data['billing_first_name'] ) ) ? $post_data['billing_first_name'] : '',
			'last_name'  => ( ! empty( $post_data['billing_last_name'] ) ) ? $post_data['billing_last_name'] : '',
			'company'    => ( ! empty( $post_data['billing_company'] ) ) ? $post_data['billing_company'] : '',
			'address_1'  => ( ! empty( $post_data['billing_address_1'] ) ) ? $post_data['billing_address_1'] : '',
			'address_2'  => ( ! empty( $post_data['billing_address_2'] ) ) ? $post_data['billing_address_2'] : '',
			'city'       => ( ! empty( $post_data['billing_city'] ) ) ? $post_data['billing_city'] : '',
			'state'      => ( ! empty( $post_data['billing_state'] ) ) ? $post_data['billing_state'] : '',
			'country'    => ( ! empty( $post_data['billing_country'] ) ) ? $post_data['billing_country'] : '',
			'postcode'   => ( ! empty( $post_data['billing_postcode'] ) ) ? $post_data['billing_postcode'] : '',
		);
	}


}

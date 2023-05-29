<?php
/**
 * Class Frontend/Cart file.
 *
 * @package AvataxWooCommerce\Frontend
 */

namespace AvataxWooCommerce\Frontend;

use AvataxWooCommerce\Rest_API\Transaction;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cart {
	/**
	 * avatax_tax field name.
	 *
	 * @var float
	 */
	private $total_tax_amount;

	/**
	 * Initializes the Cart class and hooks into the integration.
	 */
	public function __construct() {
		//add_filter( 'woocommerce_calc_tax', array( $this, 'override_tax_calculation' ), 10, 3 );
		add_action( 'woocommerce_review_order_after_shipping', array( $this, 'send_woocommerce_checkout_data' ) );
	}

	/**
	 * Overrides the tax calculation for WooCommerce.
	 *
	 * @param array $taxes The calculated taxes.
	 * @param float $price The price.
	 * @param array $rates The tax rates.
	 *
	 * @return array         The modified taxes.
	 */
	public function override_tax_calculation( $taxes, $price, $rates ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return $taxes;
		}

		$avatax_tax = $this->total_tax_amount;

		$new_taxes = array();
		foreach ( $taxes as $key => $tax ) {
			$new_taxes[ $key ] = (float) $avatax_tax * 100;  // Set each tax to the fixed tax amount
		}

		return $new_taxes;
	}

	public function get_cart_tax(){
		try{
			$post_data = $this->get_checkout_post_data();

			if ( empty( $post_data ) ) {
				return;
			}

			$item_info = new Transaction\Item_Info( $post_data );
			$api_call = new Transaction\Create( $item_info );

			$response = $api_call->send_request();

			error_log(print_r($response,true));
		} catch ( \Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
		}
	}

	/**
	 * Get checkout $_POST['post_data'].
	 *
	 * @return array
	 */
	public function get_checkout_post_data() {
		if ( empty( $_REQUEST['post_data'] ) ) {
			return array();
		}

		$post_data = array();

		parse_str( sanitize_text_field( wp_unslash( urldecode( $_REQUEST['post_data'] ) ) ), $post_data );

		return $post_data;
	}
}
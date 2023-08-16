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
		add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_cart_fees' ), 10 );
	}

	/**
	 * Overrides the tax calculation for WooCommerce.
	 *
	 * @param $cart
	 *
	 */
	public function add_cart_fees() {
		global $woocommerce;

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		$fee_name = __( 'Avatax Tax', 'avatax-excise-xi' );

		// Reset Avatax taxes
		$fees = $woocommerce->cart->get_fees();
		foreach ( $fees as $key => $fee ) {
			if ( $fees[ $key ]->name == $fee_name ) {
				unset( $fees[ $key ] );
			}
		}
		$woocommerce->cart->fees_api()->set_fees( $fees );

		$avatax_tax_amount = $this->get_cart_tax();

		if ( ! empty( $avatax_tax_amount ) ) {
			$woocommerce->cart->add_fee( $fee_name, $avatax_tax_amount, false, 'tax' );
		}
	}

	/**
	 * Get cart tax value.
	 *
	 * @return float|void
	 */
	public function get_cart_tax() {
		try {
			$post_data = $this->get_checkout_post_data();

			if ( empty( $post_data ) ) {
				return;
			}

			$item_info = new Transaction\Item_Info( $post_data );
			$api_call  = new Transaction\Create( $item_info );

			$response = $api_call->send_request();

			return $response['TotalTaxAmount'] ?? 0;

		} catch ( \Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
			return;
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
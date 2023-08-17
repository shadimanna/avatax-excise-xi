<?php
/**
 * Class Frontend/Cart file.
 *
 * @package AvataxWooCommerce\Frontend
 */

namespace AvataxWooCommerce\Frontend;

use AvataxWooCommerce\Rest_API\Transaction;
use AvataxWooCommerce\Rest_API\Transaction\Void_Transaction;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cart {

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

		$post_data = $this->get_checkout_post_data();
		if ( ! empty( $post_data ) ) {
			// Reset Avatax taxes
			$fees = $woocommerce->cart->get_fees();
			foreach ( $fees as $key => $fee ) {
				if ( $fees[ $key ]->name == $fee_name ) {
					unset( $fees[ $key ] );
				}
			}
			$woocommerce->cart->fees_api()->set_fees( $fees );

			$avatax_tax = WC()->session->get( 'avatax_cart_tax' );
			if ( ! empty( $avatax_tax['id'] ) ) {
				$api_call = new Void_Transaction( $avatax_tax['id'] );
				$api_call->send_request();

				WC()->session->set( 'avatax_cart_tax', array() );
			}

		}

		$avatax_tax_amount = $this->get_cart_tax();
		if ( isset( $avatax_tax_amount['value'] ) && $avatax_tax_amount['value'] > 0 ) {
			$woocommerce->cart->add_fee( $fee_name, $avatax_tax_amount['value'], false, 'tax' );
			WC()->session->set( 'avatax_cart_tax', $avatax_tax_amount );
		}
	}

	/**
	 * Get cart tax value.
	 *
	 * @return array
	 */
	public function get_cart_tax() {
		try {
			$post_data = $this->get_checkout_post_data();

			if ( empty( $post_data ) ) {
				return array();
			}

			$item_info = new Transaction\Item_Info( $post_data );
			$api_call  = new Transaction\Create( $item_info );

			$response = $api_call->send_request();

			return isset( $response['TotalTaxAmount'] ) ? array( 'id' => $response['UserTranId'], 'value' => $response['TotalTaxAmount'], ) : array();

		} catch ( \Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
			return array();
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
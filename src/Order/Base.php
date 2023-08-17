<?php
/**
 * Class Order/Base file.
 *
 * @package AvataxWooCommerce\Order
 */

namespace AvataxWooCommerce\Order;

use AvataxWooCommerce\Rest_API\Transaction\Commit;
use AvataxWooCommerce\Rest_API\Transaction\Void_Transaction;
use AvataxWooCommerce\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Base
 *
 * @package AvataxWooCommerce\Order
 */
class Base {
	/**
	 * Init and hook in the integration.
	 */
	public function __construct() {
		$this->settings = Settings::get_instance();

		$this->init_hooks();
	}

	/**
	 * Collection of hooks when initiation.
	 */
	public function init_hooks() {
		add_action( 'woocommerce_checkout_create_order', array( $this, 'calculate_taxes' ), 10, 1 );
		add_action( 'woocommerce_checkout_create_order', array( $this, 'save_tax_data' ), 10, 1 );
		add_action( 'woocommerce_order_status_processing', array( $this, 'commit_invoice' ), 10, 1 );

		add_action( 'woocommerce_order_status_cancelled', array( $this, 'void_invoice' ), 10, 1 );
		add_action( 'woocommerce_order_status_refunded', array( $this, 'void_invoice' ), 10, 1 );
		add_action( 'woocommerce_order_status_failed', array( $this, 'void_invoice' ), 10, 1 );
	}

	/**
	 * Calculate order taxes.
	 *
	 * @param $order \WC_order
	 *
	 * @return void
	 * @throws \WC_Data_Exception
	 */
	public function calculate_taxes( $order ) {
		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}

		$avatax_tax = WC()->session->get( 'avatax_cart_tax' );

		if ( ! empty( $avatax_tax ) ) {
			$tax = new \WC_Order_Item_Tax();
			$tax->set_order_id( $order->get_id() );

			$tax->set_name( 'avatax' );
			$tax->set_tax_total( $avatax_tax['value'] );
			$tax->save();
			$order->add_item( $tax );
			$order->set_total( $order->get_total() + $avatax_tax['value'] );
			$order->save();

			WC()->session->set( 'avatax_cart_tax', array() );
		}
	}

	/**
	 * @param $order \WC_order
	 *
	 * @return void
	 */
	public function save_tax_data( $order ) {
		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}

		$avatax_tax = WC()->session->get( 'avatax_cart_tax' );
		if ( ! empty( $avatax_tax ) ) {
			$order->add_meta_data( '_avatax_id', $avatax_tax['id'] );
			$order->add_order_note( __( 'Avatax ID : ', 'avatax-excise-xi' ) . $avatax_tax['id'], false );
			$order->save();
		}
	}

	/**
	 * Void Avatax invoice.
	 *
	 * @param $order_id int Order ID.
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function commit_invoice( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}

		$avatax_id = $order->get_meta( '_avatax_id' );
		if ( ! empty( $avatax_id ) ) {
			if ( $this->settings->is_committing_enabled() ) {
				$api_call = new Commit( $avatax_id );
				$api_call->send_request();

				$order->add_order_note( __( 'Avatax invoice committed.', 'avatax-excise-xi' ), false );
			}
		}
	}

	/**
	 * Commit Avatax invoice.
	 *
	 * @param $order_id int Order ID.
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function void_invoice( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}

		$avatax_id = $order->get_meta( '_avatax_id' );
		if ( ! empty( $avatax_id ) ) {
			$api_call = new Void_Transaction( $avatax_id );
			$api_call->send_request();

			$order->add_order_note( __( 'Avatax invoice voided.', 'avatax-excise-xi' ), false );
		}
	}
}

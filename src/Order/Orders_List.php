<?php
/**
 * Class Order\Orders_List file.
 *
 * @package AvataxWooCommerce\Order
 */

namespace AvataxWooCommerce\Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Orders_List
 *
 * @package AvataxWooCommerce\Order
 */
class Orders_List {
	/**
	 * Init and hook in the integration.
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Collection of hooks when initiation.
	 */
	public function init_hooks() {
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_order_avatax_column_header' ), 31 );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'add_order_avatax_column_content' ), 10, 2 );
	}

	/**
	 * Add Avatax status column header.
	 *
	 * @param $columns .
	 *
	 * @return array.
	 */
	public function add_order_avatax_column_header( $columns ) {
		$wc_actions = $columns['wc_actions'];
		unset( $columns['wc_actions'] );

		$columns['avatax_status'] = esc_html__( 'Avatax status', 'postnl-for-woocommerce' );
		$columns['wc_actions']    = $wc_actions;

		return $columns;
	}

	/**
	 * Add Avatax status column content.
	 *
	 * @param $column .
	 * @param $order_id .
	 *
	 * @return void.
	 */
	public function add_order_avatax_column_content( $column, $order_id ) {
		if ( 'avatax_status' !== $column ) {
			return;
		}

		if ( empty( $order_id ) ) {
			return;
		}

		$order = wc_get_order( $order_id );

		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}

		if ( ! empty( $order->get_meta( '_avatax_id' ) ) ) {
			echo '<img style="max-height:25px; max-width: 25px;" src="' . AVATAX_WC_PLUGIN_DIR_URL . '/assets/img/avatax.png' . '"/>';
		}
	}
}
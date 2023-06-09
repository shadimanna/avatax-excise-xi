<?php
/**
 * Class Rest_API\Excise\Item_Info file.
 *
 * @package AvataxWooCommerce\Rest_API\Transaction
 */

namespace AvataxWooCommerce\Rest_API\Transaction;

use AvataxWooCommerce\Rest_API\Base_Info;
use AvataxWooCommerce\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Item_Info
 *
 * @package AvataxWooCommerce\Rest_API\Transaction
 */
class Item_Info extends Base_Info {
	/**
	 * API args.
	 *
	 * @var array.
	 */
	protected array $api_args;

	/**
	 * Transaction data.
	 *
	 * @var array.
	 */
	public $body;

	/**
	 * Transaction Lines data.
	 *
	 * @var array.
	 */
	public array $transaction_lines;

	/**
	 * Parses the arguments and sets the instance's properties.
	 *
	 * @throws \Exception If some data in $args did not pass validation.
	 */
	protected function parse_args() {
		foreach ( $this->api_args['cart']['items'] as $item ) {
			$this->transaction_lines[] = Utils::parse_args( $item, $this->get_transaction_line_schema() );
		}
	}


	/**
	 * Method to convert cart data to API args.
	 *
	 * @param  array  $data  .
	 */
	public function convert_data_to_args( $data ) {
		$this->api_args['shipping_address'] = array(
			'city'     => ( ! empty( $data['shipping_city'] ) ) ? $data['shipping_city'] : '',
			'state'    => ( ! empty( $data['shipping_state'] ) ) ? $data['shipping_state'] : '',
			'country'  => ( ! empty( $data['shipping_country'] ) ) ? $data['shipping_country'] : '',
			'postcode' => ( ! empty( $data['shipping_postcode'] ) ) ? $data['shipping_postcode'] : '',
		);


		$this->api_args['cart']['items'] = array();
		$cart_contents                   = WC()->cart->get_cart_contents();
		$item_invoice_line               = 1;

		foreach ( $cart_contents as $cart_item ) {
			$product                = $cart_item['data'];
			$unit_of_measure        = $product->get_meta( '_avatax_unit_of_measure' );
			$qty_unit_of_measure    = $product->get_meta( '_avatax_qty_unit_of_measure' );
			$unit_volume            = $product->get_meta( '_avatax_unit_volume' );
			$volume_unit_of_measure = $product->get_meta( '_avatax_volume_unit_of_measure' );


			$item = array(
				'InvoiceLine'            => $item_invoice_line,
				'ProductCode'            => $product->get_sku(),
				'UnitPrice'              => $cart_item['line_subtotal'],
				'BilledUnits'            => $cart_item['quantity'],
				'UnitOfMeasure'          => $unit_of_measure,
				'DestinationCountryCode' => $this->api_args['shipping_address']['country'],
				'DestinationCity'        => $this->api_args['shipping_address']['city'],
				'DestinationPostalCode'  => $this->api_args['shipping_address']['postcode'],
				'SaleCountryCode'        => $this->api_args['store_address']['country'],
				'SaleCity'               => $this->api_args['store_address']['city'],
				'SalePostalCode'         => $this->api_args['store_address']['postcode'],
			);

			if ( ! empty( $unit_volume ) ) {
				$item['UnitVolume']              = $unit_volume;
				$item['UnitVolumeUnitOfMeasure'] = $volume_unit_of_measure;
			} else {
				$item['UnitWeight']              = $product->get_weight();
				$item['UnitWeightUnitOfMeasure'] = 'KG';
			}

			$this->api_args['cart']['items'][] = $item;
			$item_invoice_line ++;
		}
	}

	/**
	 * Retrieves the args scheme to use with for parsing cart line items.
	 *
	 * @return array.
	 */
	protected function get_transaction_line_schema() {
		return array(
			'InvoiceLine'             => array(),
			'ProductCode'             => array(),
			'UnitPrice'               => array(),
			'Currency'                => array(
				'default' => get_woocommerce_currency()
			),
			'NetUnits'                => array(),
			'UnitOfMeasure'           => array(),
			'DestinationCountryCode'  => array(),
			'DestinationCity'         => array(),
			'DestinationPostalCode'   => array(),
			'SaleCountryCode'         => array( 'default' => '' ),
			'SaleCity'                => array( 'default' => '' ),
			'SalePostalCode'          => array( 'default' => '' ),
			'UnitWeight'              => array(),
			'UnitWeightUnitOfMeasure' => array(),
		);
	}
}

<?php
/**
 * Class Frontend/Cart file.
 *
 * @package AvataxWooCommerce\Frontend
 */

namespace AvataxWooCommerce\Frontend;

use AvataxWooCommerce\Utils;
use AvataxWooCommerce\Rest_API\Checkout\Client;
use AvataxWooCommerce\Rest_API\Checkout\Item_Info;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cart {

	/**
	 * Template file name.
	 *
	 * @var string
	 */
	public $template_file;

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
		add_filter( 'woocommerce_calc_tax', array( $this, 'override_tax_calculation' ), 10, 3 );
		add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'send_woocommerce_checkout_data' ) );
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

	/**
	 * Sends WooCommerce checkout data to the Avatax API.
	 */
	public function send_woocommerce_checkout_data() {
		// Get the current cart
		$cart = WC()->cart;

		// Get the billing address from the cart
		$billing_address = array(
			'country'  => $cart->get_customer()->get_billing_country(),
			'state'    => $cart->get_customer()->get_billing_state(),
			'city'     => $cart->get_customer()->get_billing_city(),
			'postcode' => $cart->get_customer()->get_billing_postcode(),
		);

		// Get the product information from the cart
		$items = $cart->get_cart_contents();
		$item  = current( $items ); // Assuming there is only one item in the cart

		// Prepare the transaction data
		$transaction_data = array(
			'TransactionLines'       => array(
				array(
					'InvoiceLine'             => 10,
					'ProductCode'             => $item['product_id'],
					'UnitPrice'               => $item['line_subtotal'],
					'GrossUnits'              => $item['quantity'],
					'BilledUnits'             => $item['quantity'],
					'BillOfLadingNumber'      => 'CertTest_1',
					'BillOfLadingDate'        => date( 'c' ),
					'DestinationCountryCode'  => $billing_address['country'],
					'DestinationJurisdiction' => $billing_address['state'],
					'DestinationCounty'       => '',
					'DestinationCity'         => $billing_address['city'],
					'DestinationPostalCode'   => $billing_address['postcode'],
					'SaleCountryCode'         => $billing_address['country'],
					'SaleJurisdiction'        => '',
					'SaleCounty'              => '',
					'SaleCity'                => '',
					'SalePostalCode'          => '',
					'Currency'                => get_woocommerce_currency(),
				),
			),
			'EffectiveDate'          => date( 'c' ),
			'InvoiceDate'            => date( 'c' ),
			'InvoiceNumber'          => 'CertTest_1',
			'TitleTransferCode'      => 'DEST',
			'TransactionType'        => 'ABOVE',
			'TransportationModeCode' => 'PL',
			'UserTranId'             => 'CertTest_1',
			'SourceSystem'           => 'AvataxWooCommerce',
		);

		// Convert the transaction data to JSON
		$json_data = json_encode( array( $transaction_data ) );
		// Set up the API request
		$curl = curl_init();

		curl_setopt_array( $curl, array(
			CURLOPT_URL            => 'https://excisesbx.avalara.com/api/v1/AvaTaxExcise/ProcessTransactions',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => '',
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => 'POST',
			CURLOPT_POSTFIELDS     => $json_data,
			CURLOPT_HTTPHEADER     => array(
				'x-company-id: 5001686',
				'X-Avalara-Client: a0o5a000007lz9IAAQ;Progressus; [Machine Name]',
				'Accept: application/json',
				'Content-Type: application/json',
				'Authorization: Basic cHJvZ3Jlc3N1czI6QFMzaEJOU2QqbkhpMzNI',
			),
		) );

		// Send the API request
		$response = curl_exec( $curl );

		// Handle the API response
		if ( $response === false ) {
			// Error occurred
			$error_message = curl_error( $curl );
			// Handle the error appropriately
		} else {
			// API request successful
			// Process the API response as needed
			$api_response = json_decode( $response, true );

			if ( isset( $api_response['TransactionResults'][0]['TotalTaxAmount'] ) ) {
				$this->total_tax_amount = (float) $api_response['TransactionResults'][0]['TotalTaxAmount'];
			}
		}

		// Close the cURL request
		curl_close( $curl );
	}
}

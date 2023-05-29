<?php
/**
 * Class Rest_API\Base file.
 *
 * @package AvataxWooCommerce\Rest_API
 */

namespace AvataxWooCommerce\Rest_API;

use AvataxWooCommerce\Rest_API\Transaction\Item_Info;
use AvataxWooCommerce\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Base
 *
 * @package AvataxWooCommerce\Rest_API
 */
class Base {
	/**
	 * Settings class instance.
	 *
	 * @var Settings.
	 */
	protected $settings;

	/**
	 * Avatax API URL.
	 *
	 * @var string
	 */
	public $api_url;

	/**
	 * API Request Method.
	 *
	 * @var string
	 */
	public $method = 'POST';

	/**
	 * Item Info.
	 *
	 * @var Item_Info.
	 */
	public $item_info;

	/**
	 * Avalara account username from the settings.
	 *
	 * @var string.
	 */
	public $auth_header;

	/**
	 * API Endpoint.
	 *
	 * @var string
	 */
	public $endpoint;

	/**
	 * Sandbox mode or not.
	 *
	 * @var boolean
	 */
	public $is_sandbox;

	/**
	 * Class constructor.
	 *
	 * @param  Base_Info  $item_info  Set of Item_Info.
	 */
	public function __construct( $item_info ) {
		$this->settings = Settings::get_instance();

		$this->set_item_info( $item_info );
		$this->check_api_mode();
		$this->set_api_url();
	}

	/**
	 * Method to set API args to an item info.
	 *
	 * @param  array  $item_info  Set of API arguments.
	 */
	public function set_item_info( $item_info ) {
		$this->item_info = $item_info;
	}

	/**
	 * Check the API mode from the settings.
	 */
	public function check_api_mode() {
		$this->is_sandbox = $this->settings->is_sandbox();
	}

	/**
	 * Set API Environment value.
	 */
	public function set_api_url() {
		$this->api_url = ( true === $this->is_sandbox ) ? AVATAX_WC_SANDBOX_API_URL : AVATAX_WC_PROD_API_URL;
		$this->api_url .= $this->endpoint;

		if ( ! empty( $this->compose_url_params() ) && is_array( $this->compose_url_params() ) ) {
			$this->api_url = add_query_arg(
				$this->compose_url_params(),
				$this->api_url
			);
		}
	}

	/**
	 * Get API Environment value.
	 *
	 * @return string.
	 */
	public function get_api_url() {
		if ( empty( $this->api_url ) ) {
			$this->set_api_url();
		}

		return $this->api_url;
	}

	/**
	 * Set API key value.
	 */
	public function set_api_auth() {
		$this->auth_header = base64_encode( $this->settings->get_account_username() . ':' . $this->settings->get_account_password() );
	}

	/**
	 * Get API Key value.
	 *
	 * @return string.
	 */
	public function get_api_auth() {
		if ( empty( $this->auth_header ) ) {
			$this->set_api_auth();
		}

		return $this->auth_header;
	}

	/**
	 * Get basic headers args for REST request.
	 *
	 * @return array.
	 */
	public function get_basic_headers_args() {
		return array(
			'Authorization'    => 'Basic ' . $this->get_api_auth(),
			'X-Avalara-Client' => 'a0o5a000007lz9IAAQ;Progressus;',
			'X-Company-Id'     => $this->settings->get_avalara_company_id(),
			'accept'           => 'application/json',
			'Content-Type'     => 'application/json',
		);
	}

	/**
	 * Get headers args for REST request.
	 * We can manipulate this in child class if some class has different needs for API headers.
	 *
	 * @return array.
	 */
	public function get_headers_args() {
		return $this->get_basic_headers_args();
	}

	/**
	 * Function for composing API parameter in the URL for GET request.
	 */
	public function compose_url_params() {
		return array();
	}

	/**
	 * Function for composing API request.
	 */
	public function compose_body_request() {
		return array();
	}

	/**
	 * Send API request to Avatax Rest API.
	 *
	 * @throws \Exception Throw error if response has WP_Error.
	 */
	public function send_request() {
		$api_url      = $this->get_api_url();
		$request_args = array(
			'method'  => $this->method,
			'headers' => $this->get_headers_args(),
		);

		if ( ! empty( $this->compose_body_request() ) && is_array( $this->compose_body_request() ) ) {
			$request_args['body'] = wp_json_encode( $this->compose_body_request() );
		}

		for ( $i = 1; $i <= 5; $i ++ ) {
			$response = wp_remote_request( $api_url, $request_args );

			if ( ! is_wp_error( $response ) ) {
				break;
			}
		}

		if ( is_wp_error( $response ) ) {
			throw new \Exception( $response->get_error_message() );
		}

		$body_response = wp_remote_retrieve_body( $response );

		$this->check_response_error( $response );

		return json_decode( $body_response, true );
	}

	/**
	 * Check if the response value has error or not.
	 *
	 * @param  Mixed  $response  response value from the API call.
	 *
	 * @throws \Exception Error when response has error.
	 */
	public function check_response_error( $response ) {

		$status_code = wp_remote_retrieve_response_code( $response );
		if ( 401 === $status_code ) {
			throw new \Exception( __( 'Unauthorized !', 'avatax-excise-xi' ) );
		}

		$body_response = wp_remote_retrieve_body( $response );
		if ( ! is_array( $body_response ) ) {
			$body_response = json_decode( $body_response, true );
		}

		if ( ! empty( $body_response['Message'] ) ) {
			throw new \Exception( $body_response['Message'] );
		}
	}
}
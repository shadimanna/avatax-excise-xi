<?php
/**
 * Class Rest_API\Transactions\Commit file.
 *
 * @package AvataxWooCommerce\Rest_API\Transaction
 */

namespace AvataxWooCommerce\Rest_API;

use AvataxWooCommerce\Main;
use AvataxWooCommerce\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Commit
 *
 * @package AvataxWooCommerce\Rest_API\Transaction
 */
class Ping extends Base {
	/**
	 * API Endpoint.
	 *
	 * @var string
	 */
	public $endpoint = '/api/v1/Utilities/Ping';

	/**
	 * API Request Method.
	 *
	 * @var string
	 */
	public $method = 'GET';

	/**
	 * Class constructor.
	 *
	 */
	public function __construct() {
		$this->settings = Settings::get_instance();
		$this->logger   = ( new Main )->get_logger();
		$this->check_api_mode();
		$this->set_api_url();
	}
}
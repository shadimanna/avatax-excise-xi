<?php
/**
 * Class AvataxWooCommerce/Settings file.
 *
 * @package AvataxWooCommerce\Settings
 */

namespace AvataxWooCommerce\Settings;

use AvataxWooCommerce\Rest_API\Ping;
use AvataxWooCommerce\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Settings
 *
 * @package AvataxWooCommerce\Settings
 */
class Settings {
	/**
	 * The unique instance of the class.
	 *
	 * @var Settings.
	 */
	private static $instance;

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
		add_filter( 'woocommerce_get_sections_tax', array( $this, 'add_setting_section' ) );
		add_action( 'woocommerce_get_settings_tax', array( $this, 'setting_fields' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts') );
		add_action( 'wp_ajax_avatax_test_connection', array( $this, 'avatax_test_connection_callback' ) );
	}

	/**
	 * Gets an instance of the settings.
	 *
	 * @return Settings
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 *
	 * @param $sections
	 *
	 * @return array.
	 */
	function add_setting_section( $sections ) {
		$sections[ AVATAX_SETTINGS_ID ] = esc_html__( 'Avatax', 'avatax-for-woocommerce' );

		return $sections;
	}

	/**
	 * Get all setting fields.
	 *
	 * @return array.
	 */
	public function setting_fields( $settings, $current_section ) {

		if ( $current_section !== AVATAX_SETTINGS_ID ) {
			return $settings;
		}

		return array(
			array(
				'id'   => 'avatax_settings',
				'name' => esc_html__( 'Avatax Settings', 'text-domain' ),
				'type' => 'title',
				'desc' => esc_html__( 'The following options are used to configure Avatax', 'text-domain' ),
			),
			// Account Settings.
			array(
				'id'    => 'avatax_account_settings_title',
				'title' => esc_html__( 'Account Settings', 'avatax-excise-xi' ),
				'type'  => 'title',
			),
			array(
				'id'          => 'avatax_environment_mode',
				'title'       => esc_html__( 'Environment Mode', 'avatax-excise-xi' ),
				'type'        => 'select',
				'desc'        => sprintf(
				// translators: %1$s is anchor opener tag and %2$s is anchor closer tag.
					esc_html__( '%1$sTest Connection%2$s', 'avatax-excise-xi' ),
					'<button id="avatax_test_connection" type="button">',
					'</button>'
				),
				'options'     => array(
					'production' => esc_html__( 'Production', 'avatax-excise-xi' ),
					'sandbox'    => esc_html__( 'Sandbox', 'avatax-excise-xi' ),
				),
				'class'       => 'wc-enhanced-select',
				'default'     => 'production',
				'placeholder' => '',
			),
			array(
				'id'          => 'avatax_account_username',
				'title'       => esc_html__( 'Account username', 'avatax-excise-xi' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'default'     => '',
				'placeholder' => '',
			),
			array(
				'id'    => 'avatax_account_password',
				'title' => esc_html__( 'Account password', 'avatax-excise-xi' ),
				'type'  => 'password',
			),
			array(
				'id'          => 'avatax_avalara_company_id',
				'title'       => esc_html__( 'Avalara Company ID', 'avatax-excise-xi' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'placeholder' => '11223344',
			),
			array(
				'id'          => 'avatax_shipping_product_code',
				'title'       => esc_html__( 'Shipping product code', 'avatax-excise-xi' ),
				'type'        => 'text',
				'placeholder' => '11223344',
			),
			array(
				'id'      => 'avatax_entity_use_code',
				'title'   => esc_html__( 'Entity Use Codes', 'avatax-excise-xi' ),
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'default' => 'Resale',
				'options' => Utils::get_entity_use_codes(),
			),
			array(
				'id'    => 'avatax_enable_logging',
				'title' => esc_html__( 'Logging', 'avatax-excise-xi' ),
				'type'  => 'checkbox',
				'desc'  => sprintf(
				// translators: %1$s is anchor opener tag and %2$s is anchor closer tag.
					esc_html__( 'A log file containing the communication to the Avatax server will be maintained if this option is checked. This can be used in case of technical issues and can be found %1$shere%2$s.', 'avatax-excise-xi' ),
					'<a href="' . esc_url( Utils::get_log_url() ) . '" target="_blank">',
					'</a>'
				),
				'label' => esc_html__( 'Enable', 'avatax-excise-xi' ),
			),
			array(
				'id'    => 'avatax_enable_committing',
				'title' => esc_html__( 'Committing', 'avatax-excise-xi' ),
				'type'  => 'checkbox',
				'desc'  => esc_html__( 'Commit tax liabilities for the order entries.', 'avatax-excise-xi' ),
				'label' => esc_html__( 'Enable', 'avatax-excise-xi' ),
			),
			array(
				'id'   => 'avatax_settings',
				'type' => 'sectionend',
			),
			array(
				'id'    => 'avatax_sale_address',
				'title' => esc_html__( 'Sale Address', 'postnl-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => __( 'you can keep it empty to use store address as a sale address', 'avatax-excise-xi' ),
			),
			array(
				'id'    => 'avatax_use_store_address_as_sale',
				'title' => esc_html__( 'Use Store Address as a Sale Address', 'avatax-excise-xi' ),
				'type'  => 'checkbox',
				'desc'  => esc_html__( 'No need to fill the sale address after checking this, and it will use the store address for it.', 'avatax-excise-xi' ),
			),
			array(
				'id'      => 'avatax_sale_address_country',
				'title'   => esc_html__( 'Country', 'avatax-excise-xi' ),
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => WC()->countries->get_countries(),
			),
			array(
				'id'    => 'avatax_sale_address_city',
				'title' => esc_html__( 'City', 'avatax-excise-xi' ),
				'type'  => 'text',
			),
			array(
				'id'    => 'avatax_sale_address_state',
				'title' => esc_html__( 'State', 'avatax-excise-xi' ),
				'type'  => 'text',
			),
			array(
				'id'    => 'avatax_sale_address_postcode',
				'title' => esc_html__( 'Postal code', 'avatax-excise-xi' ),
				'type'  => 'text',
			),
			array(
				'id'   => 'avatax_sale_address',
				'type' => 'sectionend',
			),
		);
	}

	/**
	 * Return true if sandbox mode is ticked.
	 *
	 * @return string.
	 */
	public function get_environment_mode() {
		return get_option( 'avatax_environment_mode' );
	}

	/**
	 * Get Avatax account username.
	 *
	 * @return string.
	 */
	public function get_account_username() {
		return get_option( 'avatax_account_username' );
	}

	/**
	 * Get Avatax account password.
	 *
	 * @return string.
	 */
	public function get_account_password() {
		return get_option( 'avatax_account_password' );
	}

	/**
	 * Get Avatax account password.
	 *
	 * @return string.
	 */
	public function get_avalara_company_id() {
		return get_option( 'avatax_avalara_company_id' );
	}

	/**
	 * Return true if sandbox mode is ticked.
	 *
	 * @return bool.
	 */
	public function is_sandbox() {
		return ( 'sandbox' === $this->get_environment_mode() );
	}

	/**
	 * Get shipping product code.
	 *
	 * @return string.
	 */
	public function get_shipping_product_code() {
		return get_option( 'avatax_shipping_product_code' );
	}

	/**
	 * Get enable logging value from the settings.
	 *
	 * @return string.
	 */
	public function get_enable_logging() {
		return get_option( 'avatax_enable_logging' );
	}

	/**
	 * Return true if enable logging field is ticked.
	 *
	 * @return bool.
	 */
	public function is_logging_enabled() {
		return ( 'yes' === $this->get_enable_logging() );
	}

	/**
	 * Get enable committing value from the settings.
	 *
	 * @return string.
	 */
	public function get_enable_committing() {
		return get_option( 'avatax_enable_committing' );
	}

	/**
	 * Return true if enable committing field is ticked.
	 *
	 * @return bool.
	 */
	public function is_committing_enabled() {
		return ( 'yes' === $this->get_enable_committing() );
	}

	/**
	 * Get customer Entity Use Codes if set, or default value.
	 *
	 * @return string.
	 */
	public function get_entity_use_code() {
		$customer_entity_use_code = get_user_meta( get_current_user_id(), '_avatax_entity_use_code', true );
		if ( $customer_entity_use_code && '' !== $customer_entity_use_code ) {
			return $customer_entity_use_code;
		}

		$default_entity_use_code = get_option( 'avatax_entity_use_code' );
		if ( $default_entity_use_code && '' !== $default_entity_use_code ) {
			return $default_entity_use_code;
		}

		return 'Resale';
	}

	/**
	 * Enqueue Scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Enqueue Scripts
		$screen = get_current_screen();

		if ( 'woocommerce_page_wc-settings' === ( $screen ? $screen->id : '' ) ) {
			$test_con_data = array(
				'ajax_url'       => admin_url( 'admin-ajax.php' ),
				'loader_image'   => admin_url( 'images/loading.gif' ),
				'test_con_nonce' => wp_create_nonce( 'avatax-check-connection' ),
			);

			wp_enqueue_script(
				'avatax-test-connection',
				AVATAX_WC_PLUGIN_DIR_URL . '/assets/js/avatax-test-connection.js',
				array( 'jquery' ),
				AVATAX_WC_VERSION,
				true,
			);

			wp_localize_script( 'avatax-test-connection', 'avatax_test_connection', $test_con_data );
		}
	}

	/**
	 * Test Connection Ajax Request.
	 *
	 * @return void
	 */
	public function avatax_test_connection_callback() {
		check_ajax_referer( 'avatax-check-connection', 'test_con_nonce' );

		try {
			$api_call = new Ping();
			$response = $api_call->send_request();

			error_log(print_r($response,true));
			if ( $response['authenticated'] ) {
				wp_send_json( array(
					'connection_success' => true,
					'status_text'        => 'Authenticated',
				) );
			} else {
				wp_send_json( array(
					'connection_success' => false,
					'status_text'        => 'Unauthenticated !',
				) );
			}
		} catch ( \Exception $e ) {
			error_log($e->getMessage());
			wp_send_json( array(
				'connection_success' => false,
				'status_text'        => 'Connection Failed',
			) );
		}

		wp_die();
	}
}

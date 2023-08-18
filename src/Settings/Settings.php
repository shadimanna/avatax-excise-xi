<?php
/**
 * Class AvataxWooCommerce/Settings file.
 *
 * @package AvataxWooCommerce\Settings
 */

namespace AvataxWooCommerce\Settings;

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
				'description' => __( 'Choose the environment mode.', 'avatax-excise-xi' ),
				'desc_tip'    => true,
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
				'description' => '',
				'desc_tip'    => true,
				'default'     => '',
				'placeholder' => '',
			),
			array(
				'id'          => 'avatax_account_password',
				'title'       => esc_html__( 'Account password', 'avatax-excise-xi' ),
				'type'        => 'password',
				'description' => '',
				'desc_tip'    => true,
				'default'     => '',
				'placeholder' => '',
			),
			array(
				'id'          => 'avatax_avalara_company_id',
				'title'       => esc_html__( 'Avalara Company ID', 'avatax-excise-xi' ),
				'type'        => 'text',
				'description' => '',
				'desc_tip'    => true,
				'default'     => '',
				'placeholder' => '11223344',
			),
			array(
				'id'          => 'avatax_shipping_product_code',
				'title'       => esc_html__( 'Shipping product code', 'avatax-excise-xi' ),
				'type'        => 'text',
				'description' => '',
				'desc_tip'    => true,
				'default'     => '',
				'placeholder' => '11223344',
			),
			array(
				'id'          => 'avatax_entity_use_code',
				'title'       => esc_html__( 'Entity Use Codes', 'avatax-excise-xi' ),
				'type'        => 'select',
				'class'       => 'wc-enhanced-select',
				'description' => '',
				'desc_tip'    => true,
				'default'     => 'Resale',
				'options'     => Utils::get_entity_use_codes(),
			),
			array(
				'id'          => 'avatax_enable_logging',
				'title'       => esc_html__( 'Logging', 'avatax-excise-xi' ),
				'type'        => 'checkbox',
				'description' => sprintf(
				// translators: %1$s is anchor opener tag and %2$s is anchor closer tag.
					esc_html__( 'A log file containing the communication to the Avatax server will be maintained if this option is checked. This can be used in case of technical issues and can be found %1$shere%2$s.', 'avatax-excise-xi' ),
					'<a href="' . esc_url( Utils::get_log_url() ) . '" target="_blank">',
					'</a>'
				),
				'label'       => esc_html__( 'Enable', 'avatax-excise-xi' ),
			),
			array(
				'id'          => 'avatax_enable_committing',
				'title'       => esc_html__( 'Committing', 'avatax-excise-xi' ),
				'type'        => 'checkbox',
				'description' => esc_html__( 'Commit tax liabilities for the order entries.', 'avatax-excise-xi' ),
				'label'       => esc_html__( 'Enable', 'avatax-excise-xi' ),
				'desc_tip'    => true,
			),
			'sale_address_settings_title' => array(
				'title'       => esc_html__( 'Sale Address', 'postnl-for-woocommerce' ),
				'type'        => 'title',
				'description' => __( 'you can keep it empty to use store address as a sale address', 'avatax-excise-xi' ),
			),
			array(
				'id'          => 'sale_address_country',
				'title'       => esc_html__( 'Country', 'avatax-excise-xi' ),
				'type'        => 'select',
				'class'       => 'wc-enhanced-select',
				'description' => '',
				'desc_tip'    => true,
				'default'     => '',
				'options'     => WC()->countries->get_countries(),
			),
			array(
				'id'          => 'sale_address_city',
				'title'       => esc_html__( 'City', 'avatax-excise-xi' ),
				'type'        => 'text',
				'description' => '',
				'desc_tip'    => true,
				'default'     => '',
			),
			array(
				'id'          => 'sale_address_state',
				'title'       => esc_html__( 'State', 'avatax-excise-xi' ),
				'type'        => 'text',
				'description' => '',
				'desc_tip'    => true,
				'default'     => '',
			),
			array(
				'id'          => 'sale_address_postcode',
				'title'       => esc_html__( 'Postal code', 'avatax-excise-xi' ),
				'type'        => 'text',
				'description' => '',
				'desc_tip'    => true,
				'default'     => '',
			),
			array(
				'id'   => 'avatax_settings',
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
}

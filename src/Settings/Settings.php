<?php
/**
 * Class AvataxWooCommerce/Settings file.
 *
 * @package AvataxWooCommerce\Settings
 */

namespace AvataxWooCommerce\Settings;

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
				'type'        => 'text',
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
}

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
				'id'    => 'account_settings_title',
				'title' => esc_html__( 'Account Settings', 'avatax-excise-xi' ),
				'type'  => 'title',
			),
			array(
				'id'          => 'environment_mode',
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
				'id'          => 'account_username',
				'title'       => esc_html__( 'Account username', 'avatax-excise-xi' ),
				'type'        => 'text',
				'description' => '',
				'desc_tip'    => true,
				'default'     => '',
				'placeholder' => '',
			),
			array(
				'id'          => 'account_password',
				'title'       => esc_html__( 'Account password', 'avatax-excise-xi' ),
				'type'        => 'text',
				'description' => '',
				'desc_tip'    => true,
				'default'     => '',
				'placeholder' => '',
			),
			array(
				'id'          => 'avalara_client',
				'title'       => esc_html__( 'Avalara Client', 'avatax-excise-xi' ),
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
	 * @return String
	 */
	public function get_environment_mode() {
		return get_option( 'environment_mode' );
	}

	/**
	 * Return true if sandbox mode is ticked.
	 *
	 * @return Bool
	 */
	public function is_sandbox() {
		return ( 'sandbox' === $this->get_environment_mode() );
	}
}

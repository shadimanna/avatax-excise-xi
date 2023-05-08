<?php
/**
 * Class Utils file.
 *
 * @package AvataxWooCommerce
 */

namespace AvataxWooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Utils
 *
 * @package AvataxWooCommerce
 */
class Utils {
	/**
	 * Check if WooCommerce plugin is active.
	 *
	 * @return bool.
	 */
	public static function WooCommerce_is_active() {
		// Test to see if WooCommerce is active (including network activated).
		$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';

		return in_array( $plugin_path, wp_get_active_and_valid_plugins() ) || in_array( $plugin_path, wp_get_active_network_plugins() );
	}

	/**
	 * Generating meta box fields.
	 *
	 * @param  array  $fields  list of fields.
	 */
	public static function fields_generator( $fields ) {
		foreach ( $fields as $field ) {
			if ( empty( $field['id'] ) ) {
				continue;
			}

			switch ( $field['type'] ) {
				case 'select':
					woocommerce_wp_select( $field );
					break;

				case 'checkbox':
					woocommerce_wp_checkbox( $field );
					break;

				case 'hidden':
					woocommerce_wp_hidden_input( $field );
					break;

				case 'radio':
					woocommerce_wp_radio( $field );
					break;

				case 'textarea':
					woocommerce_wp_textarea_input( $field );
					break;

				case 'text':
				case 'number':
				default:
					woocommerce_wp_text_input( $field );
					break;
			}
		}
	}

	/**
	 * Unit Of Measure.
	 *
	 * @return array.
	 */
	public static function get_units_of_measure() {
		return array(
			'ECH' => esc_html__( 'ECH', 'avatax-excise-xi' ),
			'GLL' => esc_html__( 'GLL', 'avatax-excise-xi' ),
			'PAK' => esc_html__( 'PAK', 'avatax-excise-xi' ),
			'GRM' => esc_html__( 'GRM', 'avatax-excise-xi' ),
			'CIG' => esc_html__( 'CIG', 'avatax-excise-xi' ),
			'ML'  => esc_html__( 'ML', 'avatax-excise-xi' ),
			'OZ'  => esc_html__( 'OZ', 'avatax-excise-xi' ),
		);
	}

	/**
	 * Unit of Measure for sub-units.
	 *
	 * @return array.
	 */
	public static function get_sub_units_of_measure() {
		$uom       = self::get_units_of_measure();
		$uom['EA'] = esc_html__( 'EA', 'avatax-excise-xi' );

		return $uom;
	}
}
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

	/**
	 * Parsers a given array of arguments using a specific scheme.
	 *
	 * The scheme is a `key => array` associative array, where the `key` represents the argument key and the `array`
	 * represents the scheme for that single argument. Each scheme may have the following:
	 * * `default` - the default value to use if the arg is not given
	 * * `error` - the message of the exception if the arg is not given and no `default` is in the scheme
	 * * `validate` - a validation callback that receives the arg, the args array and the scheme as arguments.
	 * * `sanitize` - a sanitization callback similar to `validate` but should return the sanitized value.
	 * * `rename` - an optional new name for the argument key.
	 *
	 * @param  array  $args  The arguments to parse.
	 * @param  array  $scheme  The scheme to parse with, or a fixed scalar value.
	 *
	 * @return array The parsed arguments.
	 *
	 * @throws \Exception If an argument does not exist in $args and has no `default` in the $scheme.
	 * @since [*next-version*]
	 *
	 */
	public static function parse_args( $args, $scheme ) {
		$final_args = array();

		foreach ( $scheme as $key => $s_scheme ) {
			// If not an array, just use it as a value.
			if ( ! is_array( $s_scheme ) ) {
				$final_args[ $key ] = $s_scheme;
				continue;
			}

			// Rename the key if "rename" was specified.
			$new_key = empty( $s_scheme['rename'] ) ? $key : $s_scheme['rename'];

			// Recurse for array values and nested schemes.
			if ( ! empty( $args[ $key ] ) && isset( $s_scheme[0] ) && is_array( $s_scheme[0] ) ) {
				$final_args[ $new_key ] = static::parse_args( $args[ $key ], $s_scheme );
				continue;
			}

			// If the key is not set in the args.
			if ( ! isset( $args[ $key ] ) ) {
				// If no default value is given, throw.
				if ( ! isset( $s_scheme['default'] ) ) {
					// If no default value is specified, throw an exception.
					$message = ! isset( $s_scheme['error'] )
						// translators: %s is a field argument.
						? sprintf( __( 'Please specify a "%s" argument', 'postnl-for-woocommerce' ), $key )
						: $s_scheme['error'];

					throw new \Exception( $message );
				}
				// If a default value is specified, use that as the value.
				$value = $s_scheme['default'];
			} else {
				$value = $args[ $key ];
			}

			// Call the validation function.
			if ( ! empty( $s_scheme['validate'] ) && is_callable( $s_scheme['validate'] ) ) {
				call_user_func_array( $s_scheme['validate'], array( $value, $args, $scheme ) );
			}

			// Call the sanitization function and get the sanitized value.
			if ( ! empty( $s_scheme['sanitize'] ) && is_callable( $s_scheme['sanitize'] ) ) {
				$value = call_user_func_array( $s_scheme['sanitize'], array( $value, $args, $scheme ) );
			}

			$final_args[ $new_key ] = $value;
		}

		return $final_args;
	}

	/**
	 * Check if the string is JSON or not.
	 *
	 * @param Mixed $value String or value that will be validated.
	 *
	 * @return boolean.
	 */
	public static function is_json( $value ) {
		if ( is_array( $value ) || is_object( $value ) ) {
			return false;
		}

		json_decode( $value );
		return json_last_error() === JSON_ERROR_NONE;
	}

	/**
	 * Get log URL in the admin.
	 *
	 * @return string.
	 */
	public static function get_log_url() {
		return Logger::get_log_url();
	}

	/**
	 * Set shipping address based on post data from checkout page.
	 *
	 * @param array $post_data Post data from checkout page.
	 *
	 * @return array
	 */
	public static function set_post_data_address( $post_data ) {
		if ( empty( $post_data['ship_to_different_address'] ) ) {
			$post_data['shipping_first_name'] = $post_data['billing_first_name'] ?? '';
			$post_data['shipping_last_name']  = $post_data['billing_last_name'] ?? '';
			$post_data['shipping_company']    = $post_data['billing_company'] ?? '';
			$post_data['shipping_address_1']  = $post_data['billing_address_1'] ?? '';
			$post_data['shipping_address_2']  = $post_data['billing_address_2'] ?? '';
			$post_data['shipping_city']       = $post_data['billing_city'] ?? '';
			$post_data['shipping_state']      = $post_data['billing_state'] ?? '';
			$post_data['shipping_country']    = $post_data['billing_country'] ?? '';
			$post_data['shipping_postcode']   = $post_data['billing_postcode'] ?? '';
		}

		return $post_data;
	}
}
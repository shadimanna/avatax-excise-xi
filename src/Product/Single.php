<?php
/**
 * Class Product\Single file.
 *
 * @package AvataxWooCommerce\Product
 */

namespace AvataxWooCommerce\Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AvataxWooCommerce\Utils;

/**
 * Class Single.
 *
 * @package AvataxWooCommerce\Product
 */
class Single {

	/**
	 * Init and hook in the integration.
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Mapping the product field.
	 *
	 * @return array.
	 */
	public static function product_field_maps() {
		return array(
			array(
				'id'          => '_avatax_product_code',
				'type'        => 'text',
				// translators: %s will be replaced by plugin name.
				'label'       => sprintf( esc_html__( 'Product Code (%s)', 'avatax-for-woocommerce' ), AVATAX_PLUGIN_NAME ),
				'description' => esc_html__( 'ProductCode that ATE recognizes, or any alternate mapped value.', 'avatax-excise-xi' ),
				'desc_tip'    => 'true',
				'placeholder' => 'Product Code',
			),
			array(
				'id'          => '_avatax_unit_of_measure',
				'type'        => 'select',
				'label'       => sprintf( esc_html__( 'Unit Of Measure (%s)', 'avatax-for-woocommerce' ), AVATAX_PLUGIN_NAME ),
				'description' => esc_html__( 'Unit Of Measure.', ' avatax-excise-xi' ),
				'desc_tip'    => 'true',
				'options'     => Utils::get_units_of_measure(),
			),
			array(
				'id'          => '_avatax_qty_unit_of_measure',
				'type'        => 'select',
				'label'       => sprintf( esc_html__( 'Unit of Measure for sub-units (%s)', 'avatax-for-woocommerce' ), AVATAX_PLUGIN_NAME ),
				'description' => esc_html__( 'Unit of Measure for sub-units.', 'avatax-excise-xi' ),
				'desc_tip'    => 'true',
				'options'     => Utils::get_sub_units_of_measure(),
				'default'     => 'EA',
			),
		);
	}

	/**
	 * Collection of hooks when initiation.
	 */
	public function init_hooks() {
		add_action( 'woocommerce_product_options_pricing', array( $this, 'additional_product_options' ), 8 );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_additional_product_parent_options' ) );
		add_action( 'woocommerce_variation_options_pricing', array( $this, 'additional_product_variation_options' ), 10, 3 );
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_additional_product_variation_options' ), 10, 2 );
	}

	/**
	 * Add the product fields.
	 *
	 * @access public
	 */
	public function additional_product_options() {
		$fields = self::product_field_maps();
		Utils::fields_generator( $fields );
	}

	/**
	 * Add the fields to the product variation.
	 *
	 * @param  int  $loop  Iteration of the product variations.
	 * @param  array  $variation_data  Variation data.
	 * @param  array  $variation  Variation object.
	 *
	 * @access public
	 */
	public function additional_product_variation_options( $loop, $variation_data, $variation ) {
		if ( empty( $variation->ID ) ) {
			return;
		}

		$product = wc_get_product( $variation->ID );

		if ( empty( $product ) ) {
			return;
		}

		$fields           = self::product_field_maps();
		$variation_fields = array();

		foreach ( $fields as $field ) {
			$field['value']     = $product->get_meta( $field['id'] );
			$field['id']        = $field['id'] . '[' . $loop . ']';
			$variation_fields[] = $field;
		}

		Utils::fields_generator( $variation_fields );
	}

	/**
	 * Saving fields values on product admin page.
	 *
	 * @param  int  $product_id  Product post ID.
	 * @param  int  $i  Iteration of product variations.
	 */
	public function save_additional_product_options( $product_id, $i = '' ) {
		$product = wc_get_product( $product_id );

		if ( empty( $product ) ) {
			\WC_Admin_Meta_Boxes::add_error( esc_html__( 'Product ID does not exists!', 'avatax-excise-xi' ) );

			return;
		}

		$fields = self::product_field_maps();

		foreach ( $fields as $field ) {
			if ( empty( $i ) && ! is_array( $_POST[ $field['id'] ] ) && 0 === $product->get_parent_id() ) {
				$product->update_meta_data( $field['id'], sanitize_text_field( wp_unslash( $_POST[ $field['id'] ] ) ) );
			} elseif ( ! empty( $i ) && 0 !== $product->get_parent_id() ) {
				$field_value = ! empty( $_POST[ $field['id'] ][ $i ] ) ? $_POST[ $field['id'] ][ $i ] : '';
				$product->update_meta_data( $field['id'], sanitize_text_field( wp_unslash( $field_value ) ) );
			}
		}

		$product->save();
	}

	/**
	 * Saving fields values on product admin page.
	 *
	 * @param  int  $product_id  Product post ID.
	 */
	public function save_additional_product_parent_options( $product_id ) {
		$this->save_additional_product_options( $product_id );
	}

	/**
	 * Saving fields values on product admin page.
	 *
	 * @param  int  $product_id  Product post ID.
	 * @param  int  $i  Iteration of product variations.
	 */
	public function save_additional_product_variation_options( $product_id, $i ) {
		$this->save_additional_product_options( $product_id, $i );
	}
}

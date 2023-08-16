<?php
/**
 * Class AvataxWooCommerce/User file.
 *
 * @package AvataxWooCommerce\Settings
 */

namespace AvataxWooCommerce\Settings;

use AvataxWooCommerce\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class User
 *
 * @package AvataxWooCommerce\Settings
 */

Class User {
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
		add_filter( 'show_user_profile', array( $this, 'user_profile_fields' ), 20 );
		add_action( 'edit_user_profile', array( $this, 'user_profile_fields' ), 20 );

		add_action( 'personal_options_update', array( $this, 'save_user_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_user_profile_fields' ) );
	}

	/**
	 * Add user profile custom fields.
	 *
	 * @param $user.
	 *
	 * @return void.
	 */
	public function user_profile_fields( $user ) {
		?>
		<h3><?php _e( 'Avatax', 'avatax-for-woocommerce' ); ?></h3>

		<table class="form-table">
			<tr>
				<th>
					<label for="avatax_entity_use_code"><?php _e( 'Entity Use Codes:' ); ?></label>
				</th>
				<td>
					<select name="avatax_entity_use_code" id="avatax_entity_use_code">
						<option value="">-</option>
						<?php
						$codes = Utils::get_entity_use_codes();
						$customer_entity_use_code = get_user_meta( $user->ID, '_avatax_entity_use_code', true );

						foreach ( $codes as $key => $code ) {
							?>
							<option value="<?php echo $key; ?>" <?php if ( $customer_entity_use_code === $key ) {echo "selected='selected' ";} ?> >
								<?php echo $code; ?>
							</option>
							<?php
						}
						?>
					</select>
				</td>
			</tr>
		</table>
	<?php
	}

	/**
	 * Save user profile custom fields.
	 *
	 * @param $user_id.
	 *
	 * @return void.
	 */
	public function save_user_profile_fields( $user_id ) {
		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		if ( isset( $_POST['avatax_entity_use_code'] ) ) {
			update_user_meta( $user_id, '_avatax_entity_use_code', sanitize_text_field( $_POST['avatax_entity_use_code'] ) );
		}
	}
}
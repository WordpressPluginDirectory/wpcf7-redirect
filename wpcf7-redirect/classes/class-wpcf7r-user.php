<?php
/**
 * Class WPCF7R_user - Parent class that handles all redirect actions.
 *
 * @package wpcf7-redirect
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WPCF7R_User
 */
class WPCF7R_User {

	/**
	 * Reference to the user fields.
	 *
	 * @var [type]
	 */
	private $fields;

	/**
	 * WPCF7R_User constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'show_user_profile', array( $this, 'additional_profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'additional_profile_fields' ) );

		add_action( 'personal_options_update', array( $this, 'save_custom_user_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_custom_user_fields' ) );
	}

	/**
	 * Get all the fields from all the forms.
	 *
	 * @return array $fields All the fields from all the forms.
	 */
	private function get_all_forms_fields() {
		$this->fields = array();

		$cf7_forms = $this->get_cf7_forms();

		if ( $cf7_forms ) {
			foreach ( $cf7_forms as $cf7_form ) {
				$wpcf7r_form = get_cf7r_form( $cf7_form->ID );

				$actions = $wpcf7r_form->get_actions( 'default' );

				foreach ( $actions as $action ) {
					$action_type = $action->get( 'action_type' );
					if ( 'register' === $action_type ) {
						$fields_mapping = maybe_unserialize( $action->get( 'user_fields' ) );

						if ( $fields_mapping ) {
							$this->fields = array_merge( $fields_mapping, $this->fields );
						}
					}
				}
			}
		}

		return $this->fields;
	}

	/**
	 * Get all the CF7 forms.
	 *
	 * @return array $cf7_forms All the CF7 forms.
	 */
	private function get_cf7_forms() {
		$args = array(
			'post_type'      => 'wpcf7_contact_form',
			'posts_per_page' => -1,
		);

		$cf7_forms = get_posts( $args );

		return $cf7_forms;
	}

	/**
	 * Add new fields above 'Update' button.
	 *
	 * @param int $user_id - User ID.
	 */
	public function save_custom_user_fields( $user_id ) {

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		$fields = $this->get_all_forms_fields();

		$black_listed_fields = array(
			'wp_capabilities',
			'wp_user_level',
			'session_tokens',
		);

		if ( ! $fields ) {
			return;
		}

		foreach ( $fields as $field ) {
			$user_field_key = $field['user_field_key'];

			if ( in_array( $user_field_key, $black_listed_fields ) || ! $user_field_key ) {
				continue;
			}

			$value = isset( $_POST[ $user_field_key ] ) ? $_POST[ $user_field_key ] : '';
			update_user_meta( $user_id, $user_field_key, $value );
		}
	}

	/**
	 * Add new fields above 'Update' button.
	 *
	 * @param WP_User $user User object.
	 */
	public function additional_profile_fields( $user ) {

		$fields = $this->get_all_forms_fields();

		if ( ! $fields ) {
			return;
		}

		?>

		<h3><?php esc_html_e( 'Extra profile information', 'wpcf7-redirect' ); ?></h3>

		<table class="form-table">
			<?php foreach ( $fields as $field ) : ?>
				<?php
				if ( ! $field['user_field_key'] ) {
					continue;
				}
				?>
				<?php $value = get_user_meta( $user->ID, $field['user_field_key'], true ); ?>
				<tr>
					<th>
						<label for="<?php echo esc_attr( $field['user_field_key'] ); ?>"><?php echo esc_html( $field['user_field_key'] ); ?></label>
					</th>
					<td>
						<input type="text" name="<?php echo esc_attr( $field['user_field_key'] ); ?>" value="<?php echo esc_attr( $value ); ?>">
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php
	}
}

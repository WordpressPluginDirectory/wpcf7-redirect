<?php
/**
 * Class WPCF7R_Action_erasedatarequest file.
 *
 * @package Redirection_For_Contact_Form_7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	function () {
		register_wpcf7r_actions(
			'erasedatarequest',
			__( 'Erase/Export Data Request', 'wpcf7-redirect' ),
			'WPCF7R_Action_Erasedatarequest',
			6
		);
	}
);

/**
 * Class WPCF7R_Action_Erasedatarequest
 *
 * Handles processing of data erasure and export requests through Contact Form 7.
 * This class manages the creation and handling of user data privacy requests
 * in compliance with privacy regulations.
 *
 * @package Redirection_For_Contact_Form_7
 */
class WPCF7R_Action_Erasedatarequest extends WPCF7R_Action {

	/**
	 * A constant defining the action slug.
	 *
	 * @var string
	 */
	const ACTION_SLUG = 'erasedatarequest';


	/**
	 * Init the parent action class
	 *
	 * @param mixed $post The post object.
	 */
	public function __construct( $post ) {
		parent::__construct( $post );
	}

	/**
	 * Get the action admin fields
	 */
	public function get_action_fields() {

		$parent_fields = parent::get_default_fields();

		$tags = $this->get_mail_tags_array();

		return array_merge(
			array(
				array(
					'name'        => 'request_type',
					'type'        => 'select',
					'label'       => __( 'Request type', 'wpcf7-redirect' ),
					'placeholder' => __( 'Request type', 'wpcf7-redirect' ),
					'value'       => $this->get( 'request_type' ),
					'class'       => '',
					'required'    => true,
					'options'     => array(
						'remove_personal_data' => __( 'Remove personal data', 'wpcf7-redirect' ),
						'export_personal_data' => __( 'Export personal data', 'wpcf7-redirect' ),
					),
				),
				array(
					'name'        => 'email_field',
					'type'        => 'select',
					'label'       => __( 'The field that is used for username ([username])', 'wpcf7-redirect' ),
					'placeholder' => __( 'Username field', 'wpcf7-redirect' ),
					'tooltip'     => __( 'Add a text field to your form and save it', 'wpcf7-redirect' ),
					'footer'      => '<div>' . $this->get_formatted_mail_tags() . '</div>',
					'value'       => $this->get( 'email_field' ),
					'class'       => '',
					'options'     => $tags,
				),
				array(
					'name'        => 'email_does_not_exist_message',
					'type'        => 'text',
					'label'       => __( 'Email/Username does not exist error message', 'wpcf7-redirect' ),
					'placeholder' => __( 'Email/Username does not exist error message', 'wpcf7-redirect' ),
					'footer'      => '<div>' . $this->get_formatted_mail_tags() . '</div>',
					'value'       => $this->get( 'email_does_not_exist_message' ),
					'class'       => '',
					'options'     => $tags,
				),

				array(
					'name'        => 'send_confirmation_email',
					'type'        => 'checkbox',
					'label'       => __( 'Send confirmation email', 'wpcf7-redirect' ),
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'send_confirmation_email' ),
				),
			),
			$parent_fields
		);
	}

	/**
	 * Get an HTML of the
	 */
	public function get_action_settings() {
		$this->get_settings_template( 'html-action-redirect.php' );
	}

	/**
	 * Handle a simple redirect rule
	 *
	 * @param object $submission The form submission object.
	 * @return mixed
	 */
	public function process( $submission ) {
		$response = array();

		$this->posted_data = $submission->get_posted_data();

		$action_type = $this->get( 'request_type' );
		$status      = 'pending';

		if ( ! $this->get( 'send_confirmation_email' ) ) {
			$status = 'confirmed';
		}

		$email_address = $this->get_user_email_address();

		$request_id = wp_create_user_request( $email_address, $action_type, array(), $status );
		$message    = '';

		if ( is_wp_error( $request_id ) ) {
			$message = $request_id->get_error_message();
		} elseif ( ! $request_id ) {
			$message = __( 'Unable to initiate confirmation request.', 'wpcf7-redirect' );
		}

		if ( $message ) {
			$response = new WP_Error( 'erase_data_request', $message );
		}

		if ( ! $response ) {
			if ( 'pending' === $status ) {
				wp_send_user_request( $request_id );

				$response = __( 'Confirmation request initiated successfully.', 'wpcf7-redirect' );
			} elseif ( 'confirmed' === $status ) {
				$response = __( 'Request added successfully.', 'wpcf7-redirect' );
			}
		}

		return $response;
	}

	/**
	 * Get the user email if the user exists
	 *
	 * @return string The email address.
	 */
	private function get_user_email_address() {
		$username_or_email_field = $this->get( 'email_field' );

		$username_or_email_address = $this->get_submitted_value( $username_or_email_field );

		if ( ! is_email( $username_or_email_address ) ) {
			$user = get_user_by( 'login', $username_or_email_address );
			if ( $user instanceof WP_User ) {
				$email_address = $user->user_email;
			}
		} else {
			$email_address = $username_or_email_address;
		}

		return $email_address;
	}

	/**
	 * Process validation for the submission
	 *
	 * @param object $submission The form submission object.
	 * @return array
	 */
	public function process_validation( $submission ) {

		$this->posted_data = $submission->get_posted_data();

		$username_or_email_field = $this->get( 'email_field' );

		$message = null;

		$email_address = $this->get_user_email_address();

		if ( empty( $email_address ) ) {
			$email_does_not_exists_message = $this->get( 'email_does_not_exist_message', __( 'Unable to add this request. A valid email address or username must be supplied', 'wpcf7-redirect' ) );

			$email_does_not_exists_message = $this->replace_tags( $email_does_not_exists_message );

			$message = new WP_Error( 'erase_data_request', $email_does_not_exists_message );

			/**
			 * Get the tags that are used to send the username/email
			 *
			 * @var [type]
			 */
			$login_field_tag = $this->get_validation_mail_tags( $username_or_email_field );

			$error = array(
				'tag'           => $login_field_tag,
				'error_message' => $message->get_error_message(),
			);

			$results['invalid_tags'][] = new WP_Error( 'tag_invalid', $error );
		}

		return $results;
	}
}

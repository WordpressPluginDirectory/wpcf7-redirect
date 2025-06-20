<?php
/**
 * Functions file
 *
 * @package Redirection for Contact Form 7
 * @category Contact Form 7 Add-on
 * @author Themeisle
 * @version 0.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get the list of available Extensions
 *
 * @return array - $extensions - extensions array.
 */
function wpcf7_get_extensions() {
	$extensions = wpcf7_redirect_get_all_extensions_list();

	$installed_extensions = wpcf7r_get_available_actions();

	foreach ( $extensions as $key => $extension ) {
		foreach ( $installed_extensions as $installed_extension ) {
			if ( class_exists( $extension['classname'] ) || $installed_extension['handler'] === $extension['classname'] || ( isset( $extension['type'] ) && 'affiliate' === $extension['type'] ) ) {
				$extensions[ $key ]['active'] = true;
			}
		}
	}

	return apply_filters( 'wpcf7_get_extensions', $extensions );
}

/**
 * Verify nonce
 *
 * @return int|boolean Returns 1, 2 or false depending on the nonce check result
 */
function wpcf7_validate_nonce() {
	$nonce = isset( $_REQUEST['wpcf7r_nonce'] ) && $_REQUEST['wpcf7r_nonce'] ? $_REQUEST['wpcf7r_nonce'] : '';

	$verified = wp_verify_nonce( $nonce, 'manage_cf7_redirect' );

	if ( $verified ) {
		return $verified;
	}

	header( 'HTTP/1.0 403 Forbidden' );
	die( 'You are not allowed to access.' );
}

/**
 * Get all available extensions definitions.
 *
 * @return array - $defaults - extensions array.
 */
function wpcf7_redirect_get_all_extensions_list() {
	$defaults = array(
		'wpcf7r-conditional-logic'    => array(
			'name'        => 'wpcf7r-conditional-logic',
			'filename'    => 'class-wpcf7r-conditions.php',
			'title'       => __( 'Conditional Logic', 'wpcf7-redirect' ),
			'description' => __( 'Powerful conditional rules management. Set if/or rules for each of your actions.', 'wpcf7-redirect' ),
			'icon'        => WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/icon7.png',
			'classname'   => 'WPCF7_Redirect_Conditional_Logic',
		),
		'wpcf7r-create-pdf'           => array(
			'name'        => 'wpcf7r-create-pdf',
			'filename'    => 'class-wpcf7r-create-pdf',
			'title'       => __( 'Create PDF', 'wpcf7-redirect' ),
			'description' => __( 'Easily create and send PDF files generated automaticaly.', 'wpcf7-redirect' ),
			'icon'        => WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/pdf-icon.png',
			'classname'   => 'WPCF7R_Action_Create_pdf',
		),
		'wpcf7r-paypal'               => array(
			'name'        => 'wpcf7r-paypal',
			'filename'    => 'class-wpcf7r-action-paypal.php',
			'title'       => __( 'PayPal Integration', 'wpcf7-redirect' ),
			'description' => __( 'Collect payments with your Contact Form 7 form. Setup product details and custom paypal fields.', 'wpcf7-redirect' ),
			'icon'        => WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/paypal.png',
			'classname'   => 'WPCF7R_Action_redirect_to_paypal',
		),
		'wpcf7r-stripe'               => array(
			'name'        => 'wpcf7r-stripe',
			'filename'    => 'class-wpcf7r-stripe.php',
			'title'       => __( 'Stripe Integration', 'wpcf7-redirect' ),
			'description' => __( 'Collect payments with your Contact Form 7 form. Setup product details and custom stripe fields.', 'wpcf7-redirect' ),
			'icon'        => WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/stripe.png',
			'classname'   => 'WPCF7R_Action_Stripe_Integration',
		),
		'wpcf7r-api'                  => array(
			'name'        => 'wpcf7r-api',
			'filename'    => 'class-wpcf7r-action-api-url.php',
			'title'       => __( 'API Integrations', 'wpcf7-redirect' ),
			'description' => __( 'RESTful POST/GET/PUT/DELETE send to remote server (json/xml/params)', 'wpcf7-redirect' ),
			'icon'        => WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/icon7.png',
			'classname'   => 'WPCF7R_Action_api_url_request',
		),
		'wpcf7r-custom-errors'        => array(
			'name'        => 'wpcf7r-custom-errors',
			'filename'    => 'class-wpcf7r-action-custom-errors.php',
			'title'       => __( 'Custom Validations', 'wpcf7-redirect' ),
			'description' => __( 'Manage your form error messages by defining each field with its own message.', 'wpcf7-redirect' ),
			'icon'        => WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/icon11.png',
			'classname'   => 'WPCF7R_Action_custom_errors',
		),
		'wpcf7r-popup'                => array(
			'name'        => 'wpcf7r-popup',
			'filename'    => 'class-wpcf7r-action-popup.php',
			'title'       => __( 'Thank You Popup', 'wpcf7-redirect' ),
			'description' => __( 'Display popup message after form submission success.', 'wpcf7-redirect' ),
			'icon'        => WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/icon3.png',
			'classname'   => 'WPCF7R_Action_Popup',
		),
		'wpcf7r-create-post'          => array(
			'name'        => 'wpcf7r-create-post',
			'filename'    => 'class-wpcf7r-action-create-post.php',
			'title'       => __( 'Create Posts', 'wpcf7-redirect' ),
			'description' => __( 'Create any post with post meta and taxonomy assigments.', 'wpcf7-redirect' ),
			'icon'        => WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/icon12.png',
			'classname'   => 'WPCF7R_Action_Create_Post',
		),
		'wpcf7r-login'                => array(
			'name'        => 'wpcf7r-login',
			'filename'    => 'class-wpcf7r-action-login.php',
			'title'       => __( 'Custom Login Forms', 'wpcf7-redirect' ),
			'description' => __( 'Create custom login forms.', 'wpcf7-redirect' ),
			'icon'        => WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/icon13.png',
			'classname'   => 'WPCF7R_Action_login',
		),
		'wpcf7r-register'             => array(
			'name'        => 'wpcf7r-register',
			'filename'    => 'class-wpcf7r-action-register.php',
			'title'       => __( 'Custom Registration Forms', 'wpcf7-redirect' ),
			'description' => __( 'Create registration form instead of WordPress default registration screen.', 'wpcf7-redirect' ),
			'icon'        => WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/icon8.png',
			'classname'   => 'WPCF7R_Action_Register',
		),
		'wpcf7r-mailchimp'            => array(
			'name'        => 'wpcf7r-mailchimp',
			'filename'    => 'class-wpcf7r-action-mailchimp.php',
			'title'       => __( 'Subscribe to Mailchimp', 'wpcf7-redirect' ),
			'description' => __( 'Subscribe users to Mailchimp. Auto subscribe submitted forms.', 'wpcf7-redirect' ),
			'icon'        => WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/icon1.png',
			'classname'   => 'WPCF7R_Action_Mailchimp',
		),
		'wpcf7r-salesforce'           => array(
			'name'        => 'wpcf7r-salesforce',
			'filename'    => 'class-wpcf7r-action-salesforce.php',
			'title'       => __( 'Salesforce Integration', 'wpcf7-redirect' ),
			'description' => __( 'Send your leads to Salesforce.', 'wpcf7-redirect' ),
			'icon'        => WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/salesforce.png',
			'classname'   => 'WPCF7R_Action_Salesforce',
		),
		'wpcf7r-hubspot'              => array(
			'name'        => 'wpcf7r-hubspot',
			'filename'    => 'class-wpcf7r-action-hubspot.php',
			'title'       => __( 'Hubspot Integration', 'wpcf7-redirect' ),
			'description' => __( 'Send your leads to hubspot.', 'wpcf7-redirect' ),
			'icon'        => WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/hubspot.png',
			'classname'   => 'WPCF7R_Action_Hubspot',
		),
		'wpcf7r-monday'               => array(
			'name'        => 'wpcf7r-monday',
			'filename'    => 'class-wpcf7r-action-monday.php',
			'title'       => __( 'Monday Integration', 'wpcf7-redirect' ),
			'description' => __( 'Send your leads to monday.', 'wpcf7-redirect' ),
			'icon'        => '',
			'classname'   => 'WPCF7R_Action_Monday',
		),
		'wpcf7r-twilio'               => array(
			'name'        => 'wpcf7r-twillio',
			'filename'    => 'class-wpcf7r-action-twilio.php',
			'title'       => __( 'Send sms with twlio', 'wpcf7-redirect' ),
			'description' => __( 'Send sms to yourself or your users with twlio.', 'wpcf7-redirect' ),
			'icon'        => WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/twilio.png',
			'classname'   => 'WPCF7R_Action_TwilioSms',
		),
		'wpcf7r-slack'                => array(
			'name'        => 'wpcf7r-slack',
			'filename'    => 'class-wpcf7r-action-slack.php',
			'title'       => __( 'Send slack message', 'wpcf7-redirect' ),
			'description' => __( 'Send slack message.', 'wpcf7-redirect' ),
			'icon'        => WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/slack-icon.png',
			'classname'   => 'WPCF7R_Action_SlackMessage',
		),
		'wpcf7r-eliminate-duplicates' => array(
			'name'        => 'wpcf7r-eliminate-duplicates',
			'filename'    => 'class-wpcf7r-action-eliminate-duplicates.php',
			'title'       => __( 'Eliminate Duplicates', 'wpcf7-redirect' ),
			'description' => __( 'Eliminate Duplicates.', 'wpcf7-redirect' ),
			'icon'        => WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/icon11.png',
			'classname'   => 'WPCF7R_Action_Eliminate_Duplicates',
		),
	);

	return $defaults;
}

/**
 * Get the slugs of the Pro plugins.
 *
 * @return string[] The slugs of the plugins.
 */
function wpcf7_get_pro_plugin_slugs() {
	return array(
		'wpcf7r_create_post',
		'wpcf7r_paypal',
		'wpcf7r_salesforce',
		'wpcf7r_api',
		'wpcf7r_hubspot',
		'wpcf7r_pdf',
		'wpcf7r_stripe',
		'wpcf7r_conditional_logic',
		'wpcf7r_mailchimp',
		'wpcf7r_popup',
		'wpcf7r_twilio',
		'wpcf7r_firescript',
	);
}

/**
 * Check if at least one Pro product is active.
 *
 * @return boolean True if at least one Pro product is active.
 */
function wpcf7_has_pro() {
	$basename_list = array_keys( wpcf7_get_plugins_namespace() );

	foreach ( $basename_list as $basename ) {
		if (
			defined( $basename ) &&
			'valid' === wpcf7r_get_license_status( constant( $basename ) )
		) {
			return true;
		}
	}

	return false;
}

/**
 * Return the URL for the upgrade page.
 *
 * @return string
 */
function wpcf7_redirect_upgrade_url() {
	return tsdk_translate_link( 'https://themeisle.com/plugins/wpcf7-redirect/upgrade' );
}

/**
 * Convert a file to base64 encoded string.
 *
 * Takes a file path, reads the content of the file, and converts it to a base64 encoded string.
 * This is useful for embedding file contents in data URIs or for transmitting binary data.
 *
 * @since 1.0.0
 *
 * @param string $path The absolute path to the file to be encoded.
 * @return string The base64 encoded content of the file.
 * @throws Exception If the file does not exist or is not readable.
 */
function wpcf7r_base_64_file( $path ) {

	$data   = file_get_contents( $path );
	$base64 = base64_encode( $data );

	return $base64;
}

/**
 * General function for retrieving form actions.
 *
 * This function retrieves actions associated with a Contact Form 7 form based on various filtering parameters.
 *
 * @param string $post_type   The type of the action (post type).
 * @param int    $count       Number of actions to return.
 * @param int    $post_id     The Contact Form 7 form ID.
 * @param string $rule_id     The action rule ID.
 * @param array  $extra_args  Additional arguments to filter the results.
 * @param bool   $active      Whether to return only active actions (true) or all actions (false).
 *
 * @return array An array of action post objects.
 */
function wpcf7r_get_actions( $post_type, $count, $post_id, $rule_id, $extra_args, $active ) {
	$actions = array();

	$args = array(
		'post_type'      => $post_type,
		'posts_per_page' => $count,
		'post_status'    => 'private',
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'meta_query'     => array(
			array(
				'key'   => 'wpcf7_id',
				'value' => $post_id,
			),
			array(
				'key'   => 'wpcf7_rule_id',
				'value' => $rule_id,
			),
		),
	);

	$args = array_merge( $args, $extra_args );

	if ( $active ) {
		$args['meta_query'][] = array(
			'key'   => 'action_status',
			'value' => 'on',
		);
	}

	$actions = get_posts( $args );

	return $actions;
}

/**
 * Check if the conditional logic extension is enabled for Contact Form 7 Redirection.
 *
 * This function verifies if the conditional logic class exists and is available for use.
 * The conditional logic extension allows for creating rules to determine when redirections occur.
 *
 * @return bool True if WPCF7_Redirect_Conditional_Logic class exists, false otherwise.
 */
function wpcf7r_conditional_logic_enabled() {
	return class_exists( 'WPCF7_Redirect_Conditional_Logic' );
}

/**
 * Serach for the file in the available directories
 *
 * @param string $filename The name of the file to return.
 * @return mixed Path if the file exists, false if not.
 */
function wpcf7r_get_addon_path( $filename ) {
	if ( file_exists( WPCF7_PRO_REDIRECT_ACTIONS_PATH . $filename ) ) {
		return WPCF7_PRO_REDIRECT_ACTIONS_PATH . $filename;
	} elseif ( file_exists( WPCF7_PRO_REDIRECT_ADDONS_PATH . $filename ) ) {
		return WPCF7_PRO_REDIRECT_ADDONS_PATH . $filename;
	}

	return false;
}

/**
 * Get an array and transform it to html attributes
 *
 * @param array $attributes The attributes to convert to HTML.
 * @return string The HTML attributes string.
 */
function wpcf7r_implode_attributes( $attributes ) {
	$result = join(
		' ',
		array_map(
			function ( $key ) use ( $attributes ) {
				if ( is_bool( $attributes[ $key ] ) ) {
					return $attributes[ $key ] ? $key : '';
				}
				return $key . '="' . $attributes[ $key ] . '"';
			},
			array_keys( $attributes )
		)
	);

	return $result;
}

/**
 * Set the content type for Contact Form 7 emails to HTML.
 *
 * @return string The content type 'text/html' for HTML formatted emails
 */
function wpcf7r_send_emails_as_html() {
	return 'text/html';
}

/**
 * Helper function
 *
 * @param string $name    Action name.
 * @param string $title   Action title.
 * @param string $class   Action class.
 * @param int    $order   Action order.
 */
function register_wpcf7r_actions( $name, $title, $class, $order = 0 ) {
	WPCF7r_Utils::register_wpcf7r_actions( $name, $title, $class, $order );
}

/**
 * Get a list of available actions for Contact Form 7 Redirection plugin.
 *
 * @return array Sorted array of available redirection actions.
 * @uses WPCF7r_Utils::get_wpcf7r_actions() To retrieve the actions.
 * @uses apply_filters() Filters the actions with 'wpcf7r_get_available_actions'.
 */
function wpcf7r_get_available_actions() {
	$actions = WPCF7r_Utils::get_wpcf7r_actions();

	array_multisort( array_column( $actions, 'order' ), SORT_ASC, $actions );

	return apply_filters( 'wpcf7r_get_available_actions', $actions );
}

/**
 *  Get a list of available actions handlers for Contact Form 7 Redirection plugin.
 *
 *  @return array Sorted array of available redirection actions handlers.
 */
function wpcf7r_get_available_actions_handlers() {
	$available_handlers = array();

	foreach ( wpcf7r_get_available_actions() as $available_action ) {
		if ( empty( $available_action['handler'] ) || ! is_string( $available_action['handler'] ) ) {
			continue;
		}
		$available_handlers[] = $available_action['handler'];
	}

	return $available_handlers;
}

/**
 * Get an instance of contact form 7 redirect form
 *
 * @param int           $form_id       The form ID.
 * @param string|object $submission    The submission data.
 * @param string|object $validation_obj The validation object.
 * @return WPCF7R_Form Form instance.
 */
function get_cf7r_form( $form_id, $submission = '', $validation_obj = '' ) {
	return new WPCF7R_Form( $form_id, $submission, $validation_obj );
}

/**
 * Create HTML tooltip
 *
 * @param string $tip The tooltip text.
 * @return string HTML tooltip.
 */
function cf7r_tooltip( $tip ) {
	return '<i class="dashicons dashicons-editor-help qs-tooltip"><span class="qs-tooltip-inner">' . wp_kses_post( $tip ) . '</span></i>';
}

/**
 * Returns the base URL of the Redirection for Contact Form 7 plugin.
 *
 * @return string The absolute URL to the plugin root directory.
 */
function wpcf7r_get_redirect_plugin_url() {
	return WPCF7_PRO_REDIRECT_BASE_URL;
}

/**
 * Get the value of a single block field
 *
 * @param string $key       The field key.
 * @param string $block_key The block key.
 * @param array  $fields    The fields array.
 * @return string The field value.
 */
function wpcf7r_block_field_value( $key, $block_key, $fields ) {
	return isset( $fields['blocks'][ $block_key ][ $key ] ) ? $fields['blocks'][ $block_key ][ $key ] : '';
}

/**
 * Remove old plugin notice
 */
function wpcf7_remove_old_plugin_notice() {
	?>

<div class="wpcf7-redirect-error error notice">
	<h3>
		<?php esc_html_e( 'Redirection for Contact Form 7', 'wpcf7-redirect' ); ?>
	</h3>
	<p>
		<?php esc_html_e( 'Error: It is recommended to deactivate and remove Redirection for Contact Form 7 plugin for the PRO version to work.', 'wpcf7-redirect' ); ?>
	</p>
</div>

	<?php
}

/**
 * A notice to remove the free plugin
 */
function wpcf7_remove_contact_form_7_to_api() {
	?>

<div class="wpcf7-redirect-error error notice">
	<h3>
		<?php esc_html_e( 'Redirection for Contact Form 7', 'wpcf7-redirect' ); ?>
	</h3>
	<p>
		<?php esc_html_e( 'Error: It is recommended to deactivate and remove Contact Form 7 to API plugin.', 'wpcf7-redirect' ); ?>
	</p>
</div>

	<?php
}

/**
 * Get Contact Form 7 version.
 *
 * @return string|false The version of Contact Form 7 plugin, or false if not found.
 */
function wpcf7_get_cf7_ver() {
	return defined( 'WPCF7_VERSION' ) ? WPCF7_VERSION : false;
	if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
		$wpcf7_path = WPCF7_PRO_REDIRECT_PLUGINS_PATH . 'contact-form-7/wp-contact-form-7.php';
		$wpcf7_data = get_plugin_data( $wpcf7_path, false, false );

		return $wpcf7_data['Version'];
	}

	return false;
}

/**
 * Show admin notices
 */
function wpcf7r_admin_notice() {
	$ver = wpcf7_get_cf7_ver() ? wpcf7_get_cf7_ver() : '';

	if ( $ver < 4.8 ) {
		?>

<div class="wpcf7-redirect-error error notice">
	<h3>
		<?php esc_html_e( 'Redirection for Contact Form 7', 'wpcf7-redirect' ); ?>
	</h3>
	<p>
		<?php esc_html_e( 'Error: Contact Form 7 version is too old. Redirection for Contact Form 7 is compatible from version 4.8 and above. Please update Contact Form 7.', 'wpcf7-redirect' ); ?>
	</p>
</div>

		<?php
	} else {
		// If CF7 isn't installed and activated, throw an error.
		?>
<div class="wpcf7-redirect-error error notice">
	<h3>
		<?php esc_html_e( 'Contact Form Redirection', 'wpcf7-redirect' ); ?>
	</h3>
	<p>
		<?php esc_html_e( 'Error: Please install and activate Contact Form 7.', 'wpcf7-redirect' ); ?>
	</p>
</div>

		<?php
	}
}

add_shortcode( 'qs_date', 'wpcf7r_qs_date' );

/**
 * Shortcode for creating date
 *
 * @param array $atts Shortcode attributes.
 * @return string The formatted date.
 */
function wpcf7r_qs_date( $atts ) {
	$atts = shortcode_atts(
		array(
			'format' => 'Ydm',
		),
		$atts,
		'wpcf7-redirect'
	);

	return gmdate( $atts['format'], time() );
}

/**
 * Check if debugging mode is enabled for Redirection for Contact Form 7.
 *
 * This function checks if the CF7_REDIRECT_DEBUG constant is defined and set to true.
 *
 * @return boolean True if debugging is enabled, false otherwise.
 */
function is_wpcf7r_debug() {
	return defined( 'CF7_REDIRECT_DEBUG' ) && CF7_REDIRECT_DEBUG ? true : false;
}

/**
 * Check if the current page is the edit contact form 7 page.
 *
 * This function determines whether the current admin page is:
 * - A Contact Form 7 edit page (via GET parameters)
 * - A Contact Form 7 new form page
 * - A post edit page for Contact Form 7 post type
 *
 * @global WP_Post $post WordPress post object
 *
 * @return boolean True if the current screen is a Contact Form 7 edit screen, false otherwise.
 */
function wpcf7r_is_wpcf7_edit() {
	global $post;

	$wpcf7_page          = isset( $_GET['page'] ) && 'wpcf7' === $_GET['page'] && isset( $_GET['post'] ) && $_GET['post'];
	$wpcf7_page_new_page = isset( $_GET['page'] ) && 'wpcf7-new' === $_GET['page'];
	$wpcf7_post          = isset( $post->post_type ) && 'wpcf7_contact_form' === $post->post_type ? true : false;

	return $wpcf7_page_new_page || $wpcf7_page || $wpcf7_post;
}

/**
 * Get an array of available countries.
 *
 * Returns an associative array of country codes and their localized names.
 * The country codes are in ISO 3166-1 alpha-2 format (two-letter country codes).
 *
 * @return array An associative array of country codes and localized country names.
 */
function wpcf73_get_country_list() {
	$country_array = array(
		'AF' => __( 'Afghanistan', 'wpcf7-redirect' ),
		'AL' => __( 'Albania', 'wpcf7-redirect' ),
		'DZ' => __( 'Algeria', 'wpcf7-redirect' ),
		'AS' => __( 'American Samoa', 'wpcf7-redirect' ),
		'AD' => __( 'Andorra', 'wpcf7-redirect' ),
		'AO' => __( 'Angola', 'wpcf7-redirect' ),
		'AI' => __( 'Anguilla', 'wpcf7-redirect' ),
		'AQ' => __( 'Antarctica', 'wpcf7-redirect' ),
		'AG' => __( 'Antigua and Barbuda', 'wpcf7-redirect' ),
		'AR' => __( 'Argentina', 'wpcf7-redirect' ),
		'AM' => __( 'Armenia', 'wpcf7-redirect' ),
		'AW' => __( 'Aruba', 'wpcf7-redirect' ),
		'AU' => __( 'Australia', 'wpcf7-redirect' ),
		'AT' => __( 'Austria', 'wpcf7-redirect' ),
		'AZ' => __( 'Azerbaijan', 'wpcf7-redirect' ),
		'BS' => __( 'Bahamas', 'wpcf7-redirect' ),
		'BH' => __( 'Bahrain', 'wpcf7-redirect' ),
		'BD' => __( 'Bangladesh', 'wpcf7-redirect' ),
		'BB' => __( 'Barbados', 'wpcf7-redirect' ),
		'BY' => __( 'Belarus', 'wpcf7-redirect' ),
		'BE' => __( 'Belgium', 'wpcf7-redirect' ),
		'BZ' => __( 'Belize', 'wpcf7-redirect' ),
		'BJ' => __( 'Benin', 'wpcf7-redirect' ),
		'BM' => __( 'Bermuda', 'wpcf7-redirect' ),
		'BT' => __( 'Bhutan', 'wpcf7-redirect' ),
		'BO' => __( 'Bolivia', 'wpcf7-redirect' ),
		'BA' => __( 'Bosnia and Herzegovina', 'wpcf7-redirect' ),
		'BW' => __( 'Botswana', 'wpcf7-redirect' ),
		'BV' => __( 'Bouvet Island', 'wpcf7-redirect' ),
		'BR' => __( 'Brazil', 'wpcf7-redirect' ),
		'BQ' => __( 'British Antarctic Territory', 'wpcf7-redirect' ),
		'IO' => __( 'British Indian Ocean Territory', 'wpcf7-redirect' ),
		'VG' => __( 'British Virgin Islands', 'wpcf7-redirect' ),
		'BN' => __( 'Brunei', 'wpcf7-redirect' ),
		'BG' => __( 'Bulgaria', 'wpcf7-redirect' ),
		'BF' => __( 'Burkina Faso', 'wpcf7-redirect' ),
		'BI' => __( 'Burundi', 'wpcf7-redirect' ),
		'KH' => __( 'Cambodia', 'wpcf7-redirect' ),
		'CM' => __( 'Cameroon', 'wpcf7-redirect' ),
		'CA' => __( 'Canada', 'wpcf7-redirect' ),
		'CT' => __( 'Canton and Enderbury Islands', 'wpcf7-redirect' ),
		'CV' => __( 'Cape Verde', 'wpcf7-redirect' ),
		'KY' => __( 'Cayman Islands', 'wpcf7-redirect' ),
		'CF' => __( 'Central African Republic', 'wpcf7-redirect' ),
		'TD' => __( 'Chad', 'wpcf7-redirect' ),
		'CL' => __( 'Chile', 'wpcf7-redirect' ),
		'CN' => __( 'China', 'wpcf7-redirect' ),
		'CX' => __( 'Christmas Island', 'wpcf7-redirect' ),
		'CC' => __( 'Cocos [Keeling] Islands', 'wpcf7-redirect' ),
		'CO' => __( 'Colombia', 'wpcf7-redirect' ),
		'KM' => __( 'Comoros', 'wpcf7-redirect' ),
		'CG' => __( 'Congo - Brazzaville', 'wpcf7-redirect' ),
		'CD' => __( 'Congo - Kinshasa', 'wpcf7-redirect' ),
		'CK' => __( 'Cook Islands', 'wpcf7-redirect' ),
		'CR' => __( 'Costa Rica', 'wpcf7-redirect' ),
		'HR' => __( 'Croatia', 'wpcf7-redirect' ),
		'CU' => __( 'Cuba', 'wpcf7-redirect' ),
		'CY' => __( 'Cyprus', 'wpcf7-redirect' ),
		'CZ' => __( 'Czech Republic', 'wpcf7-redirect' ),
		'CI' => __( 'Côte d’Ivoire', 'wpcf7-redirect' ),
		'DK' => __( 'Denmark', 'wpcf7-redirect' ),
		'DJ' => __( 'Djibouti', 'wpcf7-redirect' ),
		'DM' => __( 'Dominica', 'wpcf7-redirect' ),
		'DO' => __( 'Dominican Republic', 'wpcf7-redirect' ),
		'NQ' => __( 'Dronning Maud Land', 'wpcf7-redirect' ),
		'DD' => __( 'East Germany', 'wpcf7-redirect' ),
		'EC' => __( 'Ecuador', 'wpcf7-redirect' ),
		'EG' => __( 'Egypt', 'wpcf7-redirect' ),
		'SV' => __( 'El Salvador', 'wpcf7-redirect' ),
		'GQ' => __( 'Equatorial Guinea', 'wpcf7-redirect' ),
		'ER' => __( 'Eritrea', 'wpcf7-redirect' ),
		'EE' => __( 'Estonia', 'wpcf7-redirect' ),
		'ET' => __( 'Ethiopia', 'wpcf7-redirect' ),
		'FK' => __( 'Falkland Islands', 'wpcf7-redirect' ),
		'FO' => __( 'Faroe Islands', 'wpcf7-redirect' ),
		'FJ' => __( 'Fiji', 'wpcf7-redirect' ),
		'FI' => __( 'Finland', 'wpcf7-redirect' ),
		'FR' => __( 'France', 'wpcf7-redirect' ),
		'GF' => __( 'French Guiana', 'wpcf7-redirect' ),
		'PF' => __( 'French Polynesia', 'wpcf7-redirect' ),
		'TF' => __( 'French Southern Territories', 'wpcf7-redirect' ),
		'FQ' => __( 'French Southern and Antarctic Territories', 'wpcf7-redirect' ),
		'GA' => __( 'Gabon', 'wpcf7-redirect' ),
		'GM' => __( 'Gambia', 'wpcf7-redirect' ),
		'GE' => __( 'Georgia', 'wpcf7-redirect' ),
		'DE' => __( 'Germany', 'wpcf7-redirect' ),
		'GH' => __( 'Ghana', 'wpcf7-redirect' ),
		'GI' => __( 'Gibraltar', 'wpcf7-redirect' ),
		'GR' => __( 'Greece', 'wpcf7-redirect' ),
		'GL' => __( 'Greenland', 'wpcf7-redirect' ),
		'GD' => __( 'Grenada', 'wpcf7-redirect' ),
		'GP' => __( 'Guadeloupe', 'wpcf7-redirect' ),
		'GU' => __( 'Guam', 'wpcf7-redirect' ),
		'GT' => __( 'Guatemala', 'wpcf7-redirect' ),
		'GG' => __( 'Guernsey', 'wpcf7-redirect' ),
		'GN' => __( 'Guinea', 'wpcf7-redirect' ),
		'GW' => __( 'Guinea-Bissau', 'wpcf7-redirect' ),
		'GY' => __( 'Guyana', 'wpcf7-redirect' ),
		'HT' => __( 'Haiti', 'wpcf7-redirect' ),
		'HM' => __( 'Heard Island and McDonald Islands', 'wpcf7-redirect' ),
		'HN' => __( 'Honduras', 'wpcf7-redirect' ),
		'HK' => __( 'Hong Kong SAR China', 'wpcf7-redirect' ),
		'HU' => __( 'Hungary', 'wpcf7-redirect' ),
		'IS' => __( 'Iceland', 'wpcf7-redirect' ),
		'IN' => __( 'India', 'wpcf7-redirect' ),
		'ID' => __( 'Indonesia', 'wpcf7-redirect' ),
		'IR' => __( 'Iran', 'wpcf7-redirect' ),
		'IQ' => __( 'Iraq', 'wpcf7-redirect' ),
		'IE' => __( 'Ireland', 'wpcf7-redirect' ),
		'IM' => __( 'Isle of Man', 'wpcf7-redirect' ),
		'IL' => __( 'Israel', 'wpcf7-redirect' ),
		'IT' => __( 'Italy', 'wpcf7-redirect' ),
		'JM' => __( 'Jamaica', 'wpcf7-redirect' ),
		'JP' => __( 'Japan', 'wpcf7-redirect' ),
		'JE' => __( 'Jersey', 'wpcf7-redirect' ),
		'JT' => __( 'Johnston Island', 'wpcf7-redirect' ),
		'JO' => __( 'Jordan', 'wpcf7-redirect' ),
		'KZ' => __( 'Kazakhstan', 'wpcf7-redirect' ),
		'KE' => __( 'Kenya', 'wpcf7-redirect' ),
		'KI' => __( 'Kiribati', 'wpcf7-redirect' ),
		'KW' => __( 'Kuwait', 'wpcf7-redirect' ),
		'KG' => __( 'Kyrgyzstan', 'wpcf7-redirect' ),
		'LA' => __( 'Laos', 'wpcf7-redirect' ),
		'LV' => __( 'Latvia', 'wpcf7-redirect' ),
		'LB' => __( 'Lebanon', 'wpcf7-redirect' ),
		'LS' => __( 'Lesotho', 'wpcf7-redirect' ),
		'LR' => __( 'Liberia', 'wpcf7-redirect' ),
		'LY' => __( 'Libya', 'wpcf7-redirect' ),
		'LI' => __( 'Liechtenstein', 'wpcf7-redirect' ),
		'LT' => __( 'Lithuania', 'wpcf7-redirect' ),
		'LU' => __( 'Luxembourg', 'wpcf7-redirect' ),
		'MO' => __( 'Macau SAR China', 'wpcf7-redirect' ),
		'MK' => __( 'Macedonia', 'wpcf7-redirect' ),
		'MG' => __( 'Madagascar', 'wpcf7-redirect' ),
		'MW' => __( 'Malawi', 'wpcf7-redirect' ),
		'MY' => __( 'Malaysia', 'wpcf7-redirect' ),
		'MV' => __( 'Maldives', 'wpcf7-redirect' ),
		'ML' => __( 'Mali', 'wpcf7-redirect' ),
		'MT' => __( 'Malta', 'wpcf7-redirect' ),
		'MH' => __( 'Marshall Islands', 'wpcf7-redirect' ),
		'MQ' => __( 'Martinique', 'wpcf7-redirect' ),
		'MR' => __( 'Mauritania', 'wpcf7-redirect' ),
		'MU' => __( 'Mauritius', 'wpcf7-redirect' ),
		'YT' => __( 'Mayotte', 'wpcf7-redirect' ),
		'FX' => __( 'Metropolitan France', 'wpcf7-redirect' ),
		'MX' => __( 'Mexico', 'wpcf7-redirect' ),
		'FM' => __( 'Micronesia', 'wpcf7-redirect' ),
		'MI' => __( 'Midway Islands', 'wpcf7-redirect' ),
		'MD' => __( 'Moldova', 'wpcf7-redirect' ),
		'MC' => __( 'Monaco', 'wpcf7-redirect' ),
		'MN' => __( 'Mongolia', 'wpcf7-redirect' ),
		'ME' => __( 'Montenegro', 'wpcf7-redirect' ),
		'MS' => __( 'Montserrat', 'wpcf7-redirect' ),
		'MA' => __( 'Morocco', 'wpcf7-redirect' ),
		'MZ' => __( 'Mozambique', 'wpcf7-redirect' ),
		'MM' => __( 'Myanmar [Burma]', 'wpcf7-redirect' ),
		'NA' => __( 'Namibia', 'wpcf7-redirect' ),
		'NR' => __( 'Nauru', 'wpcf7-redirect' ),
		'NP' => __( 'Nepal', 'wpcf7-redirect' ),
		'NL' => __( 'Netherlands', 'wpcf7-redirect' ),
		'AN' => __( 'Netherlands Antilles', 'wpcf7-redirect' ),
		'NT' => __( 'Neutral Zone', 'wpcf7-redirect' ),
		'NC' => __( 'New Caledonia', 'wpcf7-redirect' ),
		'NZ' => __( 'New Zealand', 'wpcf7-redirect' ),
		'NI' => __( 'Nicaragua', 'wpcf7-redirect' ),
		'NE' => __( 'Niger', 'wpcf7-redirect' ),
		'NG' => __( 'Nigeria', 'wpcf7-redirect' ),
		'NU' => __( 'Niue', 'wpcf7-redirect' ),
		'NF' => __( 'Norfolk Island', 'wpcf7-redirect' ),
		'KP' => __( 'North Korea', 'wpcf7-redirect' ),
		'VD' => __( 'North Vietnam', 'wpcf7-redirect' ),
		'MP' => __( 'Northern Mariana Islands', 'wpcf7-redirect' ),
		'NO' => __( 'Norway', 'wpcf7-redirect' ),
		'OM' => __( 'Oman', 'wpcf7-redirect' ),
		'PC' => __( 'Pacific Islands Trust Territory', 'wpcf7-redirect' ),
		'PK' => __( 'Pakistan', 'wpcf7-redirect' ),
		'PW' => __( 'Palau', 'wpcf7-redirect' ),
		'PS' => __( 'Palestinian Territories', 'wpcf7-redirect' ),
		'PA' => __( 'Panama', 'wpcf7-redirect' ),
		'PZ' => __( 'Panama Canal Zone', 'wpcf7-redirect' ),
		'PG' => __( 'Papua New Guinea', 'wpcf7-redirect' ),
		'PY' => __( 'Paraguay', 'wpcf7-redirect' ),
		'YD' => __( 'Yemen, Democratic', 'wpcf7-redirect' ),
		'PE' => __( 'Peru', 'wpcf7-redirect' ),
		'PH' => __( 'Philippines', 'wpcf7-redirect' ),
		'PN' => __( 'Pitcairn Islands', 'wpcf7-redirect' ),
		'PL' => __( 'Poland', 'wpcf7-redirect' ),
		'PT' => __( 'Portugal', 'wpcf7-redirect' ),
		'PR' => __( 'Puerto Rico', 'wpcf7-redirect' ),
		'QA' => __( 'Qatar', 'wpcf7-redirect' ),
		'RO' => __( 'Romania', 'wpcf7-redirect' ),
		'RU' => __( 'Russia', 'wpcf7-redirect' ),
		'RW' => __( 'Rwanda', 'wpcf7-redirect' ),
		'RE' => __( 'Réunion', 'wpcf7-redirect' ),
		'BL' => __( 'Saint Barthélemy', 'wpcf7-redirect' ),
		'SH' => __( 'Saint Helena', 'wpcf7-redirect' ),
		'KN' => __( 'Saint Kitts and Nevis', 'wpcf7-redirect' ),
		'LC' => __( 'Saint Lucia', 'wpcf7-redirect' ),
		'MF' => __( 'Saint Martin', 'wpcf7-redirect' ),
		'PM' => __( 'Saint Pierre and Miquelon', 'wpcf7-redirect' ),
		'VC' => __( 'Saint Vincent and the Grenadines', 'wpcf7-redirect' ),
		'WS' => __( 'Samoa', 'wpcf7-redirect' ),
		'SM' => __( 'San Marino', 'wpcf7-redirect' ),
		'SA' => __( 'Saudi Arabia', 'wpcf7-redirect' ),
		'SN' => __( 'Senegal', 'wpcf7-redirect' ),
		'RS' => __( 'Serbia', 'wpcf7-redirect' ),
		'CS' => __( 'Serbia and Montenegro', 'wpcf7-redirect' ),
		'SC' => __( 'Seychelles', 'wpcf7-redirect' ),
		'SL' => __( 'Sierra Leone', 'wpcf7-redirect' ),
		'SG' => __( 'Singapore', 'wpcf7-redirect' ),
		'SK' => __( 'Slovakia', 'wpcf7-redirect' ),
		'SI' => __( 'Slovenia', 'wpcf7-redirect' ),
		'SB' => __( 'Solomon Islands', 'wpcf7-redirect' ),
		'SO' => __( 'Somalia', 'wpcf7-redirect' ),
		'ZA' => __( 'South Africa', 'wpcf7-redirect' ),
		'GS' => __( 'South Georgia and the South Sandwich Islands', 'wpcf7-redirect' ),
		'KR' => __( 'South Korea', 'wpcf7-redirect' ),
		'ES' => __( 'Spain', 'wpcf7-redirect' ),
		'LK' => __( 'Sri Lanka', 'wpcf7-redirect' ),
		'SD' => __( 'Sudan', 'wpcf7-redirect' ),
		'SR' => __( 'Suriname', 'wpcf7-redirect' ),
		'SJ' => __( 'Svalbard and Jan Mayen', 'wpcf7-redirect' ),
		'SZ' => __( 'Swaziland', 'wpcf7-redirect' ),
		'SE' => __( 'Sweden', 'wpcf7-redirect' ),
		'CH' => __( 'Switzerland', 'wpcf7-redirect' ),
		'SY' => __( 'Syria', 'wpcf7-redirect' ),
		'ST' => __( 'São Tomé and Príncipe', 'wpcf7-redirect' ),
		'TW' => __( 'Taiwan', 'wpcf7-redirect' ),
		'TJ' => __( 'Tajikistan', 'wpcf7-redirect' ),
		'TZ' => __( 'Tanzania', 'wpcf7-redirect' ),
		'TH' => __( 'Thailand', 'wpcf7-redirect' ),
		'TL' => __( 'Timor-Leste', 'wpcf7-redirect' ),
		'TG' => __( 'Togo', 'wpcf7-redirect' ),
		'TK' => __( 'Tokelau', 'wpcf7-redirect' ),
		'TO' => __( 'Tonga', 'wpcf7-redirect' ),
		'TT' => __( 'Trinidad and Tobago', 'wpcf7-redirect' ),
		'TN' => __( 'Tunisia', 'wpcf7-redirect' ),
		'TR' => __( 'Turkey', 'wpcf7-redirect' ),
		'TM' => __( 'Turkmenistan', 'wpcf7-redirect' ),
		'TC' => __( 'Turks and Caicos Islands', 'wpcf7-redirect' ),
		'TV' => __( 'Tuvalu', 'wpcf7-redirect' ),
		'UM' => __( 'U.S. Minor Outlying Islands', 'wpcf7-redirect' ),
		'PU' => __( 'U.S. Miscellaneous Pacific Islands', 'wpcf7-redirect' ),
		'VI' => __( 'U.S. Virgin Islands', 'wpcf7-redirect' ),
		'UG' => __( 'Uganda', 'wpcf7-redirect' ),
		'UA' => __( 'Ukraine', 'wpcf7-redirect' ),
		'SU' => __( 'Union of Soviet Socialist Republics', 'wpcf7-redirect' ),
		'AE' => __( 'United Arab Emirates', 'wpcf7-redirect' ),
		'GB' => __( 'United Kingdom', 'wpcf7-redirect' ),
		'US' => __( 'United States', 'wpcf7-redirect' ),
		'ZZ' => __( 'Unknown or Invalid Region', 'wpcf7-redirect' ),
		'UY' => __( 'Uruguay', 'wpcf7-redirect' ),
		'UZ' => __( 'Uzbekistan', 'wpcf7-redirect' ),
		'VU' => __( 'Vanuatu', 'wpcf7-redirect' ),
		'VA' => __( 'Vatican City', 'wpcf7-redirect' ),
		'VE' => __( 'Venezuela', 'wpcf7-redirect' ),
		'VN' => __( 'Vietnam', 'wpcf7-redirect' ),
		'WK' => __( 'Wake Island', 'wpcf7-redirect' ),
		'WF' => __( 'Wallis and Futuna', 'wpcf7-redirect' ),
		'EH' => __( 'Western Sahara', 'wpcf7-redirect' ),
		'YE' => __( 'Yemen', 'wpcf7-redirect' ),
		'ZM' => __( 'Zambia', 'wpcf7-redirect' ),
		'ZW' => __( 'Zimbabwe', 'wpcf7-redirect' ),
		'AX' => __( 'Åland Islands', 'wpcf7-redirect' ),
	);

	return $country_array;
}

/**
 * Get a list of available languages with their localized names.
 *
 * Returns an associative array where keys are language codes (ISO 639-1)
 * and values are the translated language names.
 *
 * @return array Associative array of language codes and their translated names.
 */
function wpcf7_get_languages_list() {
	return array(
		'en' => __( 'American English', 'wpcf7-redirect' ),
		'ar' => __( 'Arabic', 'wpcf7-redirect' ),
		'nl' => __( 'Dutch', 'wpcf7-redirect' ),
		'fr' => __( 'French', 'wpcf7-redirect' ),
		'de' => __( 'German', 'wpcf7-redirect' ),
		'he' => __( 'Hebrew', 'wpcf7-redirect' ),
		'it' => __( 'Italian', 'wpcf7-redirect' ),
		'ja' => __( 'Japanese', 'wpcf7-redirect' ),
		'pt' => __( 'Portuguese', 'wpcf7-redirect' ),
		'ru' => __( 'Russian', 'wpcf7-redirect' ),
		'es' => __( 'Spanish', 'wpcf7-redirect' ),
		'tw' => __( 'Twi', 'wpcf7-redirect' ),
	);
}

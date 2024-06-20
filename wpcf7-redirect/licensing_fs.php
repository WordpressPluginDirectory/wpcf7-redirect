<?php

if ( ! function_exists( 'fs_dynamic_init' ) ) {
	function fs_dynamic_init( $array ) {
		add_filter( 'wpcf7r_legacy_used', '__return_true' );
		return $array;
	}
}

/**
 * Get the path for the admin page
 *
 * @return void
 */
function wpcf7_get_freemius_addons_path() {
	return 'admin.php?page=wpcf7r-addons-upsell';
}

/**
 * Check if this the user has a premium liscense
 *
 * @param [type] $func
 * @return void
 */
function wpcf7r_is_premium_user( $func ) {
	return true;
}

/**
 * Check if the parent plugins is active and loaded
 *
 * @return void
 */
function wpcf7r_is_parent_active_and_loaded() {
	return true;
}

/**
 * Check if the parent plugin is active
 *
 * @return void
 */
function wpcf7r_is_parent_active() {
	$active_plugins = get_option( 'active_plugins', array() );

	if ( is_multisite() ) {
		$network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
		$active_plugins         = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
	}

	foreach ( $active_plugins as $basename ) {
		if ( 0 === strpos( $basename, 'wpcf7-redirect/' ) || 0 === strpos( $basename, 'wpcf7-redirect-premium/' ) ) {
			return true;
		}
	}
	return false;
}

function wpcf7_freemius_get_id() {
	return 9546;
}

/**
 * General loading addon function
 *
 * @param [type] $name
 * @return void
 */
function wpcf7r_load_freemius_addon( $name ) {
	//return;
	$callback    = $name;
	$loaded_hook = $name . '_loaded';

	if ( wpcf7r_is_parent_active_and_loaded() ) {
		// If parent already included, init add-on.
		$callback();

		do_action( $loaded_hook );
	} elseif ( wpcf7r_is_parent_active() ) {
		// Init add-on only after the parent is loaded.
		add_action(
			'wpcf7_fs_loaded',
			function () use ( $callback ) {
				$callback();

				do_action( $loaded_hook );
			}
		);
	} else {
		// Even though the parent is not activated, execute add-on for activation / uninstall hooks.
		$callback();

		do_action( $loaded_hook );
	}
}

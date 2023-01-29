<?php
/**
 * Uninstaller
 *
 * Uninstall the plugin by removing any options from the database
 *
 * @package  plugins-list
 */

// If the uninstall was not called by WordPress, exit.

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit(); }

// Delete cache.

delete_transient( 'plugins_list' );

delete_transient( 'artiss_plugins_list' ); // Previous version - kept to ensure this is housekept on older versions.

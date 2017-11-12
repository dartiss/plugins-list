<?php
/**
* Uninstaller
*
* Uninstall the plugin by removing any options from the database
*
* @package	Artiss-Plugins-List
* @since	2.2.1
*/

// If the uninstall was not called by WordPress, exit

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit(); }

// Delete cache

delete_transient( 'artiss_plugins_list' );
?>
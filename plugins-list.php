<?php
/*
Plugin Name: Plugins List
Plugin URI: https://wordpress.org/plugins/plugins-list/
Description: Allows you to insert a list of the Wordpress plugins you are using into any post/page.
Version: 2.3.2
Author: David Artiss
Author URI: https://artiss.blog
Text Domain: plugins-list
*/

/**
* Artiss Plugins List
*
* Main code - include various functions
*
* @package	Artiss-Plugins-List
* @since	2.1
*/

define( 'plugins_list_version', '2.3.1' );

define( 'APL_DEFAULT_PLUGIN_FORMAT', '<li>#LinkedTitle# by #LinkedAuthor#.</li>' );

if ( !function_exists( 'get_plugins' ) ) { require_once ( ABSPATH . 'wp-admin/includes/plugin.php' ); }

/**
* Add meta to plugin details
*
* Add options to plugin meta line
*
* @since	2.0
*
* @param	string  $links	Current links
* @param	string  $file	File in use
* @return   string			Links, now with settings added
*/

function apl_set_plugin_meta( $links, $file ) {

	if ( false !== strpos( $file, 'plugins-list.php' ) ) { $links = array_merge( $links, array( '<a href="http://wordpress.org/support/plugin/plugins-list">' . __( 'Support', 'plugins-list' ) . '</a>' ) ); }

	return $links;
}

add_filter( 'plugin_row_meta', 'apl_set_plugin_meta', 10, 2 );

/**
* Main Shortcode Function
*
* Extract shortcode parameters and call main function to output results
*
* @since	1.0
*
* @uses     get_plugins_list    Get the list of plugins
*
* @param    string      $paras  Shortcode parameters
* @return   string              Output
*/

function apl_plugins_list_shortcode( $paras ) {

	extract( shortcode_atts( array( 'format' => '', 'show_inactive' => '', 'cache' => '', 'nofollow' => '', 'target' => '' ), $paras ) );

	$output = get_plugins_list( $format, $show_inactive, $cache, $nofollow, $target );

	return $output;
}

add_shortcode( 'plugins_list', 'apl_plugins_list_shortcode' );

/**
* Number of plugins shortcode
*
* Shortcode to return number of plugins
*
* @since	2.2
*
* @uses     get_plugin_number   Get the number of plugins
*
* @param    string      $paras  Shortcode parameters
* @return   string              Output
*/

function apl_plugin_number_shortcode( $paras ) {

	extract( shortcode_atts( array( 'inactive' => '', 'cache' => '' ), $paras ) );

	$output = get_plugin_number( $inactive, $cache );

	return $output;
}

add_shortcode( 'plugins_number', 'apl_plugin_number_shortcode' );

/**
* Get Plugins List
*
* Get list of plugins and, optionally, format them
*
* @since	1.0
*
* @uses     apl_format_plugin_list  Format the plugin list
* @uses		apl_get_plugin_data		Get the plugin data
*
* @param    string  $format         Requires format
* @param    string  $show_inactive  Whether to format or not
* @param	string	$cache			Cache time
* @param	string	$nofollow		Whether to add nofollow to link
* @param	string	$target			Link target
* @return   string                  Output
*/

function get_plugins_list( $format = '', $show_inactive = false, $cache = 1, $nofollow = false, $target = '' ) {

	if ( '' == $format ) { $format = APL_DEFAULT_PLUGIN_FORMAT; }

	// Generate NOFOLLOW and TARGET text

	if ( $nofollow ) { $nofollow = ' rel="nofollow"'; } else { $nofollow = ''; }
	if ( '' != $target ) { $target = ' target="' . $target . '"'; } else { $target = ''; }

	// Get plugin data

	$plugins = apl_get_plugin_data( $cache );

	// Extract each plugin and format the output

	$output = '';

	foreach( $plugins as $plugin_file => $plugin_data ) {

		if ( $show_inactive || is_plugin_active( $plugin_file ) )  {
			$output .= apl_format_plugin_list( $plugin_data, $format, $nofollow, $target );
		}
	}

	// Return the code, with HTML comments

	return "\n<!-- Plugins List v" . plugins_list_version . " -->\n" . $output . "\n<!-- End of Plugins List -->\n";

}

/**
* Get Plugin number
*
* Get number of plugins
*
* @since	2.2
*
* @uses     apl_get_plugin_data		Get the plugin data
*
* @param	string	$show_inactive	Whether to include inactive plugins or not
* @param	string	$cache			Cache time
* @return   string                  Plugin number
*/

function get_plugin_number( $show_inactive = false, $cache = 1 ) {

	// Get plugin data

	$plugins = apl_get_plugin_data( $cache );

	// Get count

	if ( $show_inactive ) {
		$output = count( $plugins );
	} else {
		$output = 0;
		foreach( $plugins as $plugin_file => $plugin_data ) {
			if ( is_plugin_active( $plugin_file ) )  { $output++; }
		}
	}

	return $output;
}

/**
* Get Plugins data
*
* Get plugin data and cache it
*
* @since	2.2
*
* @uses     apl_format_plugin_list  Format the plugin list
*
* @param	string	$cache			Cache time
* @return   string                  Plugin data
*/

function apl_get_plugin_data( $cache ) {

	// Attempt to get plugin list from cache

	if ( !$cache ) { $cache = 'no'; }

	$plugins = false;
	$cache_key = 'artiss_plugins_list';
	if ( is_numeric( $cache ) ) { $plugins = get_transient( $cache_key ); }

	// If not using cache, generate a new list and cache that

	if ( !$plugins ) {
		$plugins = get_plugins();
		if ( ( '' != $plugins ) && ( is_numeric( $cache ) ) ) { set_transient( $cache_key, $plugins, 3600 * $cache ); }
	}

	return $plugins;
}

/**
* Format Plugin List
*
* Format the plugin list
*
* @since	1.0
*
* @uses     apl_replace_tags        Replace the tags
*
* @param    string  $plugin_data    The plugin list
* @param    string  $format         Format to use
* @param	string	$nofollow		Nofollow text
* @param	string	$target			Target text
* @return   string                  Output
*/

function apl_format_plugin_list( $plugin_data, $format, $nofollow, $target ) {

	// Allowed tag

	$plugins_allowedtags1 = array( 'a' => array( 'href' => array(), 'title' => array() ), 'abbr' => array( 'title' => array() ), 'acronym' => array( 'title' => array() ), 'code' => array(), 'em' => array(), 'strong' => array() );

	$plugins_allowedtags2 = array( 'abbr' => array( 'title' => array() ), 'acronym' => array( 'title' => array() ), 'code' => array(), 'em' => array(), 'strong' => array() );

	// Sanitize all displayed data

	$plugin_data[ 'Title' ] = wp_kses( $plugin_data[ 'Title' ], $plugins_allowedtags1 );
	$plugin_data[ 'PluginURI' ] = wp_kses( $plugin_data[ 'PluginURI' ], $plugins_allowedtags1 );
	$plugin_data[ 'AuthorURI' ] = wp_kses( $plugin_data[ 'AuthorURI' ], $plugins_allowedtags1 );
	$plugin_data[ 'Version' ] = wp_kses( $plugin_data[ 'Version' ], $plugins_allowedtags1 );
	$plugin_data[ 'Author' ] = wp_kses( $plugin_data[ 'Author' ], $plugins_allowedtags1 );

	// Replace the tags

	$format = apl_replace_tags( $plugin_data, $format, $nofollow, $target );

	return $format;
}

/**
* Replace tags
*
* Replace the tags in the provided format
*
* @since	2.1
*
* @param    string  $plugin_data    The plugin list
* @param    string  $format         Format to use
* @param	string	$nofollow		Nofollow text
* @param	string	$target			Target text
* @return   string                  Output
*/

function apl_replace_tags( $plugin_data, $format, $nofollow, $target ) {

	$format = strtr( $format, array( '{' => '<', '}' => '>' ) );

	$format = strtr ( $format,
						array(
						'#Title#' => $plugin_data[ 'Title' ],
						'#PluginURI#' => $plugin_data[ 'PluginURI' ],
						'#AuthorURI#' => $plugin_data[ 'AuthorURI' ],
						'#Version#' => $plugin_data[ 'Version' ],
						'#Description#' => $plugin_data[ 'Description' ],
						'#Author#' => $plugin_data[ 'Author' ],
						'#LinkedTitle#' => "<a href='" . $plugin_data[ 'PluginURI' ] . "' title='" . $plugin_data[ 'Title' ] . "'" . $nofollow . $target . ">" . $plugin_data[ 'Title' ] . "</a>",
						'#LinkedAuthor#' => "<a href='" . $plugin_data[ 'AuthorURI' ] . "' title='" . $plugin_data[ 'Author' ] . "'" . $nofollow . $target . ">" . $plugin_data[ 'Author' ] . "</a>",
						) );

	return $format;
}
?>

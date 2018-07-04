<?php
/*
Plugin Name: Plugins List
Plugin URI: https://github.com/dartiss/plugins-list
Description: Allows you to insert a list of the WordPress plugins you are using into any post/page.
Version: 2.4.2
Author: David Artiss
Author URI: https://artiss.blog
Text Domain: plugins-list
*/

/**
* Artiss Plugins List
*
* Main code - include various functions
*
* @package  plugins-list
* @since    2.1
*/

define( 'PLUGINS_LIST_VERSION', '2.4.2' );

define( 'DEFAULT_PLUGIN_LIST_FORMAT', '<li>{{LinkedTitle}} by {{LinkedAuthor}}.</li>' );

if ( ! function_exists( 'get_plugins' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' ); }

/**
* Add meta to plugin details
*
* Add options to plugin meta line
*
* @since    2.0
*
* @param    string  $links  Current links
* @param    string  $file   File in use
* @return   string          Links, now with settings added
*/

function set_plugins_list_meta( $links, $file ) {

	if ( false !== strpos( $file, 'plugins-list.php' ) ) {

		$links = array_merge( $links, array( '<a href="https://github.com/dartiss/plugins-list">' . __( 'Github', 'plugins-list' ) . '</a>' ) );

		$links = array_merge( $links, array( '<a href="http://wordpress.org/support/plugin/plugins-list">' . __( 'Support', 'plugins-list' ) . '</a>' ) );
	}

	return $links;
}

add_filter( 'plugin_row_meta', 'set_plugins_list_meta', 10, 2 );

/**
* Main Shortcode Function
*
* Extract shortcode parameters and call main function to output results
*
* @since    1.0
*
* @uses     get_plugins_list    Get the list of plugins
*
* @param    string      $paras  Shortcode parameters
* @return   string              Output
*/

function plugins_list_shortcode( $paras ) {

	$atts = shortcode_atts(
		array(
			'format'        => '',
			'show_inactive' => '',
			'show_active'   => '',
			'cache'         => '',
			'nofollow'      => '',
			'target'        => '',
			'by_author'     => '',
		), $paras
	);

	$output = get_plugins_list( $atts['format'], $atts['show_inactive'], $atts['show_active'], $atts['cache'], $atts['nofollow'], $atts['target'], $atts['by_author'] );

	return $output;
}

add_shortcode( 'plugins_list', 'plugins_list_shortcode' );

/**
* Number of plugins shortcode
*
* Shortcode to return number of plugins
*
* @since    2.2
*
* @uses     get_plugin_number   Get the number of plugins
*
* @param    string      $paras  Shortcode parameters
* @return   string              Output
*/

function plugin_number_shortcode( $paras ) {

	$atts = shortcode_atts(
		array(
			'active'   => 'true',
			'inactive' => 'false',
			'cache'    => 5,
		), $paras
	);

	$output = get_plugin_number( $atts['active'], $atts['inactive'], $atts['cache'] );

	return $output;
}

add_shortcode( 'plugins_number', 'plugin_number_shortcode' );

/**
* Get Plugins List
*
* Get list of plugins and, optionally, format them
*
* @since    1.0
*
* @uses     format_plugin_list      Format the plugin list
* @uses     get_plugin_list_data    Get the plugin data
*
* @param    string  $format         Requires format
* @param    string  $show_inactive  Whether to format or not
* @param    string  $show_active    Whether to show active or not
* @param    string  $cache          Cache time
* @param    string  $nofollow       Whether to add nofollow to link
* @param    string  $target         Link target
* @return   string                  Output
*/

function get_plugins_list( $format, $show_inactive, $show_active, $cache, $nofollow, $target, $by_author ) {

	// Set default values

	if ( '' === $format ) {
		$format = DEFAULT_PLUGIN_LIST_FORMAT;
	}
	if ( '' === $show_inactive ) {
		$show_inactive = 'false';
	}
	if ( '' === $show_active ) {
		$show_active = 'true';
	}
	if ( '' === $cache ) {
		$cache = 5;
	}
	if ( $nofollow ) {
		$nofollow = ' rel="nofollow"';
	} else {
		$nofollow = '';
	}
	if ( '' !== $target ) {
		$target = ' target="' . $target . '"';
	} else {
		$target = '';
	}
	if ( '' !== $by_author ) {
		$by_author = 'true';
	}

	// Get plugin data

	$plugins = get_plugin_list_data( $cache );

	// Sort the plugin array if required in author sequence

	if ( 'true' === $by_author ) {
		uasort( $plugins, function( $a, $b ) {
			return strtoupper( $a['Author'] ) <=> strtoupper( $b['Author'] );
		});
	}

	// Extract each plugin and format the output

	$output = '';

	foreach ( $plugins as $plugin_file => $plugin_data ) {

		if ( ( is_plugin_active( $plugin_file ) && 'true' === $show_active ) or ( ! is_plugin_active( $plugin_file ) && 'true' === $show_inactive ) ) {

			$output .= format_plugin_list( $plugin_data, $format, $nofollow, $target );
		}
	}

	// Return the code, with HTML comments

	return "\n<!-- Plugins List v" . PLUGINS_LIST_VERSION . " -->\n" . $output . "\n<!-- " . __( 'End of Plugins List', 'plugins-list' ) . " -->\n";

}

/**
* Get Plugin number
*
* Get number of plugins
*
* @since    2.2
*
* @uses     get_plugin_list_data    Get the plugin data
*
* @param    string  $show_active    Whether to include active plugins or not
* @param    string  $show_inactive  Whether to include inactive plugins or not
* @param    string  $cache          Cache time
* @return   string                  Plugin number
*/

function get_plugin_number( $show_active, $show_inactive, $cache ) {

	// Get plugin data

	$plugins = get_plugin_list_data( $cache );

	// Get count

	if ( 'true' === $show_inactive && 'true' === $show_active ) {
		$output = count( $plugins );
	} else {
		$output = 0;
		foreach( $plugins as $plugin_file => $plugin_data ) { // @codingStandardsIgnoreLine -- require $plugin_data for array values but not doing anything with it
			if ( is_plugin_active( $plugin_file ) ) {
				$output++;
			}
		}
		if ( 'true' === $show_inactive ) {
			$output = count( $plugins ) - $output; }
	}

	return $output;
}

/**
* Get Plugins data
*
* Get plugin data and cache it
*
* @since    2.2
*
* @uses     format_plugin_list      Format the plugin list
*
* @param    string  $cache          Cache time
* @return   string                  Plugin data
*/

function get_plugin_list_data( $cache ) {

	// Attempt to get plugin list from cache

	if ( ! $cache ) {
		$cache = 'no'; }

	$plugins   = false;
	$cache_key = 'plugins_list';
	if ( is_numeric( $cache ) ) {
		$plugins = get_transient( $cache_key ); }

	// If not using cache, generate a new list and cache that

	if ( ! $plugins ) {
		$plugins = get_plugins();
		if ( ( '' !== $plugins ) && ( is_numeric( $cache ) ) ) {
			set_transient( $cache_key, $plugins, MINUTE_IN_SECONDS * $cache ); }
	}

	return $plugins;
}

/**
* Format Plugin List
*
* Format the plugin list
*
* @since    1.0
*
* @uses     replace_plugin_list_tags  Replace the tags
*
* @param    string  $plugin_data    The plugin list
* @param    string  $format         Format to use
* @param    string  $nofollow       Nofollow text
* @param    string  $target         Target text
* @return   string                  Output
*/

function format_plugin_list( $plugin_data, $format, $nofollow, $target ) {

	// Allowed tag

	$plugins_allowedtags = array(
		'a'       => array(
			'href'  => array(),
			'title' => array(),
		),
		'abbr'    => array( 'title' => array() ),
		'acronym' => array( 'title' => array() ),
		'code'    => array(),
		'em'      => array(),
		'strong'  => array(),
	);

	// Sanitize all displayed data

	$plugin_data['Title']     = wp_kses( $plugin_data['Title'], $plugins_allowedtags );
	$plugin_data['PluginURI'] = wp_kses( $plugin_data['PluginURI'], $plugins_allowedtags );
	$plugin_data['AuthorURI'] = wp_kses( $plugin_data['AuthorURI'], $plugins_allowedtags );
	$plugin_data['Version']   = wp_kses( $plugin_data['Version'], $plugins_allowedtags );
	$plugin_data['Author']    = wp_kses( $plugin_data['Author'], $plugins_allowedtags );

	// Replace the tags

	$format = replace_plugin_list_tags( $plugin_data, $format, $nofollow, $target );

	return $format;
}

/**
* Replace tags
*
* Replace the tags in the provided format
*
* @since    2.1
*
* @param    string  $plugin_data    The plugin list
* @param    string  $format         Format to use
* @param    string  $nofollow       Nofollow text
* @param    string  $target         Target text
* @return   string                  Output
*/

function replace_plugin_list_tags( $plugin_data, $format, $nofollow, $target ) {

	$format = strtr(
		$format,
		array(
			'{{Title}}'        => $plugin_data['Title'],
			'{{PluginURI}}'    => $plugin_data['PluginURI'],
			'{{AuthorURI}}'    => $plugin_data['AuthorURI'],
			'{{Version}}'      => $plugin_data['Version'],
			'{{Description}}'  => $plugin_data['Description'],
			'{{Author}}'       => $plugin_data['Author'],
			'{{LinkedTitle}}'  => "<a href='" . $plugin_data['PluginURI'] . "' title='" . $plugin_data['Title'] . "'" . $nofollow . $target . '>' . $plugin_data['Title'] . '</a>',
			'{{LinkedAuthor}}' => "<a href='" . $plugin_data['AuthorURI'] . "' title='" . $plugin_data['Author'] . "'" . $nofollow . $target . '>' . $plugin_data['Author'] . '</a>',
			'#Title#'          => $plugin_data['Title'],
			'#PluginURI#'      => $plugin_data['PluginURI'],
			'#AuthorURI#'      => $plugin_data['AuthorURI'],
			'#Version#'        => $plugin_data['Version'],
			'#Description#'    => $plugin_data['Description'],
			'#Author#'         => $plugin_data['Author'],
			'#LinkedTitle#'    => "<a href='" . $plugin_data['PluginURI'] . "' title='" . $plugin_data['Title'] . "'" . $nofollow . $target . '>' . $plugin_data['Title'] . '</a>',
			'#LinkedAuthor#'   => "<a href='" . $plugin_data['AuthorURI'] . "' title='" . $plugin_data['Author'] . "'" . $nofollow . $target . '>' . $plugin_data['Author'] . '</a>',
			'{{'               => '<',
			'}}'               => '>',
			'{'                => '<',
			'}'                => '>',
		)
	);

	return $format;
}

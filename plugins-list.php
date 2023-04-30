<?php
/**
 * Plugins List
 *
 * @package           plugins-list
 * @author            David Artiss
 * @license           GPL-2.0-or-later
 *
 * Plugin Name:       Plugins List
 * Plugin URI:        https://wordpress.org/plugins/plugins-list/
 * Description:       🔌 Allows you to insert a list of the WordPress plugins you are using into any post/page.
 * Version:           2.5.2
 * Requires at least: 4.6
 * Requires PHP:      7.4
 * Author:            David Artiss
 * Author URI:        https://artiss.blog
 * Text Domain:       plugins-list
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

define( 'DEFAULT_PLUGIN_LIST_FORMAT', '<li>{{LinkedTitle}} by {{LinkedAuthor}}.</li>' );

if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php'; }

/**
 * Add meta to plugin details
 *
 * Add options to plugin meta line
 *
 * @param    string $links  Current links.
 * @param    string $file   File in use.
 * @return   string         Links, now with settings added.
 */
function set_plugins_list_meta( $links, $file ) {

	if ( false !== strpos( $file, 'plugins-list.php' ) ) {

		$links = array_merge( $links, array( '<a href="https://github.com/dartiss/plugins-list">' . __( 'Github', 'plugins-list' ) . '</a>' ) );

		$links = array_merge( $links, array( '<a href="http://wordpress.org/support/plugin/plugins-list">' . __( 'Support', 'plugins-list' ) . '</a>' ) );

		$links = array_merge( $links, array( '<a href="https://artiss.blog/donate">' . __( 'Donate', 'plugins-list' ) . '</a>' ) );

		$links = array_merge( $links, array( '<a href="https://wordpress.org/support/plugin/plugins-list/reviews/#new-post">' . __( 'Write a Review', 'plugins-list' ) . '&nbsp;⭐️⭐️⭐️⭐️⭐️</a>' ) );
	}

	return $links;
}

add_filter( 'plugin_row_meta', 'set_plugins_list_meta', 10, 2 );

/**
 * Main Shortcode Function
 *
 * Extract shortcode parameters and call main function to output results
 *
 * @uses     get_plugins_list    Get the list of plugins
 *
 * @param    string $paras  Shortcode parameters.
 * @return   stri           Output.
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
			'chars'         => '',
			'words'         => '',
			'emoji'         => '',
			'end'           => '',
		),
		$paras
	);

	// Pass the shortcode parameters onto a function to generate the plugins list.

	$output = get_plugins_list( $atts['format'], $atts['show_inactive'], $atts['show_active'], $atts['cache'], $atts['nofollow'], $atts['target'], $atts['by_author'], $atts['chars'], $atts['words'], $atts['emoji'], $atts['end'] );

	return $output;
}

add_shortcode( 'plugins_list', 'plugins_list_shortcode' );

/**
 * Number of plugins shortcode
 *
 * Shortcode to return number of plugins
 *
 * @uses     get_plugin_number   Get the number of plugins
 *
 * @param    string $paras  Shortcode parameters.
 * @return   string         Output.
 */
function plugin_number_shortcode( $paras ) {

	$atts = shortcode_atts(
		array(
			'active'   => 'true',
			'inactive' => 'false',
			'cache'    => 5,
		),
		$paras
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
 * @uses     format_plugin_list      Format the plugin list
 * @uses     get_plugin_list_data    Get the plugin data
 *
 * @param    string $format         Requires format.
 * @param    string $show_inactive  Whether to format or not.
 * @param    string $show_active    Whether to show active or not.
 * @param    string $cache          Cache time.
 * @param    string $nofollow       Whether to add nofollow to link.
 * @param    string $target         Link target.
 * @param    string $by_author      Which author.
 * @param    string $characters     Maximum characters for description.
 * @param    string $words          Maximum words for description.
 * @param    string $emoji          True or false, whether to strip emoji from description.
 * @param    string $end            When the description is truncated, what to place at the end of the string.
 * @return   string                 Output.
 */
function get_plugins_list( $format, $show_inactive, $show_active, $cache, $nofollow, $target, $by_author, $characters, $words, $emoji, $end ) {

	// Set default values.

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
	if ( 'false' == $emoji ) {
		$emoji = false;
	} else {
		$emoji = true;
	}
	if ( '' == $end ) {
		$end = '&#8230;';
	}

	// Get plugin data.

	$plugins = get_plugin_list_data( $cache );

	// Sort the plugin array if required in author sequence.

	if ( 'true' === $by_author ) {
		uasort(
			$plugins,
			function( $a, $b ) {
				return strtoupper( $a['Author'] ) <=> strtoupper( $b['Author'] );
			}
		);
	}

	// Extract each plugin and format the output.

	$output = '';

	foreach ( $plugins as $plugin_file => $plugin_data ) {

		if ( ( is_plugin_active( $plugin_file ) && 'true' === $show_active ) || ( ! is_plugin_active( $plugin_file ) && 'true' === $show_inactive ) ) {

			$output .= format_plugin_list( $plugin_data, $format, $nofollow, $target, $characters, $words, $emoji, $end );
		}
	}

	// Return the code, with HTML comments.

	return "\n" . $output . "\n";

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
 * @param    string $show_active    Whether to include active plugins or not.
 * @param    string $show_inactive  Whether to include inactive plugins or not.
 * @param    string $cache          Cache time.
 * @return   string                  Plugin number.
 */
function get_plugin_number( $show_active, $show_inactive, $cache ) {

	// Get plugin data.

	$plugins = get_plugin_list_data( $cache );

	// Get count.

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
 * @uses     format_plugin_list      Format the plugin list.
 *
 * @param    string $cache           Cache time.
 * @return   string                  Plugin data.
 */
function get_plugin_list_data( $cache ) {

	// Attempt to get plugin list from cache.

	if ( ! $cache ) {
		$cache = 'no'; }

	$plugins   = false;
	$cache_key = 'plugins_list';
	if ( is_numeric( $cache ) ) {
		$plugins = get_transient( $cache_key ); }

	// If not using cache, generate a new list and cache that.

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
 * @uses     replace_plugin_list_tags  Replace the tags
 *
 * @param    string $plugin_data    The plugin list.
 * @param    string $format         Format to use.
 * @param    string $nofollow       Nofollow text.
 * @param    string $target         Target text.
 * @param    string $characters     Maximum characters for description.
 * @param    string $words          Maximum words for description.
 * @param    string $emoji          True or false, whether to strip emoji from description.
 * @param    string $end            When the description is truncated, what to place at the end of the string.
 * @return   string                 Output.
 */
function format_plugin_list( $plugin_data, $format, $nofollow, $target, $characters, $words, $emoji, $end ) {

	// Allowed tag.

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

	// Sanitize all displayed data.

	$plugin_data['Title']     = wp_kses( $plugin_data['Title'], $plugins_allowedtags );
	$plugin_data['PluginURI'] = wp_kses( $plugin_data['PluginURI'], $plugins_allowedtags );
	$plugin_data['AuthorURI'] = wp_kses( $plugin_data['AuthorURI'], $plugins_allowedtags );
	$plugin_data['Version']   = wp_kses( $plugin_data['Version'], $plugins_allowedtags );
	$plugin_data['Author']    = wp_kses( $plugin_data['Author'], $plugins_allowedtags );

	// Strip emoji, HTML and unnecessary space from the description.
	if ( false == $emoji ) {
		$plugin_data['Description'] = remove_emoji_from_plugin_desc( $plugin_data['Description'] );
	}
	$plugin_data['Description'] = strip_spaces_from_plugin_desc( wp_strip_all_tags( $plugin_data['Description'] ) );

	// Truncate the description, if required.

	if ( '' != $characters || '' != $words ) {

		// Use WordPress function to truncate description at a set number of words (ellipsis added automatically).

		if ( '' != $words ) {
			$word_limited               = wp_trim_words( $plugin_data['Description'], $words, $end );
			$plugin_data['Description'] = $word_limited;
		}

		// Manually truncate description to a set number of characters. This is done cleanly, however, by doing so to
		// the previous space. Then an ellipsis is added.

		if ( '' != $characters ) {
			$character_limited = $plugin_data['Description'];
			// Make sure the description is greater than the required length.
			if ( strlen( $character_limited ) > $characters ) {
				$space = strrpos( substr( $character_limited, 0, $characters + 1 ), ' ' );

				if ( false == $space ) {
					// If there is no space before the truncation length, just truncate.
					$character_limited = substr( $character_limited, 0, $characters );
				} else {
					// If there is a space within the truncated area, trim to that.
					$character_limited = substr( $character_limited, 0, $space );
				}
				$plugin_data['Description'] = rtrim( $character_limited ) . $end;
			}
		}

		// If both words and character limits are used, take whichever results in the shortest result.

		if ( ( '' != $characters && '' != $words ) && ( $word_limited < $character_limited ) ) {
			$plugin_data['Description'] = $word_limited;
		}
	}

	// Replace the tags.

	$format = replace_plugin_list_tags( $plugin_data, $format, $nofollow, $target );

	return $format;
}

/**
 * Replace tags
 *
 * Replace the tags in the provided format
 *
 * @param    string $plugin_data    The plugin list.
 * @param    string $format         Format to use.
 * @param    string $nofollow       Nofollow text.
 * @param    string $target         Target text.
 * @return   string                 Output.
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

	// Remove all HTML tags other than those used for formatting.

	$allowed_tags = array(
		'a'          => array(
			'href'     => array(),
			'hreflang' => array(),
			'rel'      => array(),
			'target'   => array(),
			'type'     => array(),
			'class'    => array(),
			'style'    => array(),
		),
		'b'          => array(),
		'big'        => array(),
		'blockquote' => array(
			'cite'  => array(),
			'class' => array(),
			'style' => array(),
		),
		'br'         => array(),
		'caption'    => array(),
		'center'     => array(),
		'cite'       => array(),
		'code'       => array(
			'pre'   => array(),
			'class' => array(),
			'style' => array(),
		),
		'col'        => array(
			'span'  => array(),
			'class' => array(),
			'style' => array(),
		),
		'colgroup'   => array(
			'span'  => array(),
			'class' => array(),
			'style' => array(),
		),
		'div'        => array(
			'class' => array(),
			'style' => array(),
		),
		'em'         => array(
			'class' => array(),
			'style' => array(),
		),
		'font'       => array(
			'class' => array(),
			'style' => array(),
		),
		'h1'         => array(
			'class' => array(),
			'style' => array(),
		),
		'h2'         => array(
			'class' => array(),
			'style' => array(),
		),
		'h3'         => array(
			'class' => array(),
			'style' => array(),
		),
		'h4'         => array(
			'class' => array(),
			'style' => array(),
		),
		'h5'         => array(
			'class' => array(),
			'style' => array(),
		),
		'h6'         => array(
			'class' => array(),
			'style' => array(),
		),
		'hr'         => array(
			'class' => array(),
			'style' => array(),
		),
		'i'          => array(
			'class' => array(),
			'style' => array(),
		),
		'img'        => array(
			'src'   => array(),
			'alt'   => array(),
			'class' => array(),
			'style' => array(),
		),
		'li'         => array(
			'class' => array(),
			'style' => array(),
		),
		'ol'         => array(
			'start' => array(),
			'type'  => array(),
			'class' => array(),
			'style' => array(),
		),
		'p'          => array(
			'class' => array(),
			'style' => array(),
		),
		'pre'        => array(
			'code'  => array(),
			'samp'  => array(),
			'class' => array(),
			'style' => array(),
		),
		'q'          => array(
			'cite'  => array(),
			'class' => array(),
			'style' => array(),
		),
		's'          => array(
			'class' => array(),
			'style' => array(),
		),
		'small'      => array(
			'class' => array(),
			'style' => array(),
		),
		'span'       => array(
			'class' => array(),
			'style' => array(),
		),
		'strike'     => array(
			'class' => array(),
			'style' => array(),
		),
		'strong'     => array(
			'class' => array(),
			'style' => array(),
		),
		'style'      => array(
			'type'  => array(),
			'class' => array(),
			'style' => array(),
		),
		'sub'        => array(
			'class' => array(),
			'style' => array(),
		),
		'sup'        => array(
			'class' => array(),
			'style' => array(),
		),
		'table'      => array(
			'class' => array(),
			'style' => array(),
		),
		'td'         => array(
			'colspan' => array(),
			'headers' => array(),
			'rowspan' => array(),
			'class'   => array(),
			'style'   => array(),
		),
		'th'         => array(
			'colspan' => array(),
			'headers' => array(),
			'rowspan' => array(),
			'scope'   => array(),
			'class'   => array(),
			'style'   => array(),
		),
		'tr'         => array(
			'class' => array(),
			'style' => array(),
		),
		'u'          => array(
			'class' => array(),
			'style' => array(),
		),
		'ul'         => array(
			'class' => array(),
			'style' => array(),
		),
	);

	$format = wp_kses( $format, $allowed_tags );

	return $format;
}

/**
 * Remove emoji
 *
 * Function to strip emoji from the plugin description.
 *
 * @param    string $description    The plugin description.
 * @return   string                 Stripped description.
 */
function remove_emoji_from_plugin_desc( $description ) {

	$symbols = "\x{1F100}-\x{1F1FF}" // Enclosed Alphanumeric Supplement.
		. "\x{1F300}-\x{1F5FF}"      // Miscellaneous Symbols and Pictographs.
		. "\x{1F600}-\x{1F64F}"      // Emoticons.
		. "\x{1F680}-\x{1F6FF}"      // Transport And Map Symbols.
		. "\x{1F900}-\x{1F9FF}"      // Supplemental Symbols and Pictographs.
		. "\x{2600}-\x{26FF}"        // Miscellaneous Symbols.
		. "\x{2700}-\x{27BF}";       // Dingbats.

	$description = preg_replace( '/[' . $symbols . ']+/u', '', $description );

	return $description;
}

/**
 * Strip spaces
 *
 * Function to strip extra spaces from the plugin description.
 *
 * @param    string $description    The plugin description.
 * @return   string                 Stripped description.
 */
function strip_spaces_from_plugin_desc( $description ) {

	$continue = true;
	while ( true === $continue ) {
		$replace = str_replace( '  ', ' ', $description );
		if ( $replace == $description ) {
			$continue = false;
		}
		$description = $replace;
	}
	return trim( $description );
}

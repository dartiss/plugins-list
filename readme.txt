=== Plugins List ===
Contributors: dartiss, nutsmuggler
Donate link: https://artiss.blog/donate
Tags: plugin, list, show, installed, display
Requires at least: 4.6
Tested up to: 6.2
Requires PHP: 7.4
Stable tag: 2.5.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

🔌 Allows you to insert a list of the Wordpress plugins you are using into any post/page.

== Description ==

This is a simple community Wordpress plugin aimed at giving credit where credit is due.

The plugin inserts an XHTML list into any post/page through a shortcode. If you're into customization, you can specify a format argument and indicate the exact output you are after. There's also an option to display inactive plugins as well.

Key features include...

* A simple template system allows you to format how you'd like the plugin information to be shown
* Template tags are available for automatically linked items as well as simple text
* Choose from a number of pieces of plugin data to be output
* Display inactive plugins as well as active plugins if you wish
* Automatically limit long descriptions to specific lengths, and even remove emojis from the output
* Output is cached to provide a super-quick response
* A separate shortcode allows you to display how many plugins you have!

Thanks to [Matej Nastran](http://matej.nastran.net/)'s [My plugins](http://wordpress.org/extend/plugins/my-plugins/), from which *Plugins list* was initially derived.

Iconography is courtesy of the very talented [Janki Rathod](https://www.fiverr.com/jankirathore) ♥️

👉 Please visit the [Github page](https://github.com/dartiss/plugins-list "Github") for the latest code development, planned enhancements and known issues 👈

== Getting Started ==

To get a list of the plugins that are installed and activated in your website, insert the following into any post or page:

`<ul>[plugins_list]</ul>`

You can customise the output specifying the `format` argument and a number of pre-defined `tags`. Here's an example:

`[plugins_list format="{{LinkedTitle}} ({{LinkedAuthor}}) - {{Description}}{{br/}}"]`

The tags are as follows, all defined within double braces...

* `Title` - the plugin title
* `PluginURI` - the URL of the plugin
* `Author` - the plugin author
* `AuthorURI` - the author's URL
* `Version` - plugin version number
* `Description` - the plugin description
* `LinkedTitle` - the title but automatically linked to the corresponding URL
* `LinkedAuthor` - the author, linking to their profile

The plugins list can be freely styled with css, just place any *class* or *id* attribute on the `format` string, or on the elements surrounding it.

== Using HTML ==

If you wish to put HTML in your format then you can. However, this can cause havoc in the Visual editor and even causes extra characters to be passed into the output (rogue paragraph tags, for instance). I therefore highly recommend that, if you wish to add HTML, use double braces instead of < and > around your HTML tags - this plugin will correct this before output but it means the visual editor doesn't try and interpret the HTML.

For example...

`<ul>[plugins_list format="{{li}}{{LinkedTitle}} - {{LinkedAuthor}}{{/li}}"]</ul>`

The characters will be corrected upon output and you will get a lovely, bulleted, un-ordered list as output.

If you're using the block editor and need to wrap HTML around the outside of the short code, please see the details further below on the best way to do this.

== Additional Parameters ==

**Inactive Plugins**

If you want to list also the plug-ins you have installed but are not using, here's the formula:

`[plugins_list format="{{LinkedTitle}} ({{LinkedAuthor}}) - {{Description}}{{br/}}" show_inactive=true]`

**Link Targets & No Follow**

By default links will be followed but you can make these "nofollow" by simply adding the parameter of `nofollow=true`. For example...

`[plugins_list format="{{LinkedTitle}} ({{LinkedAuthor}}) - {{Description}}{{br/}}" nofollow=true]`

You can also specify the link target too. For example...

`[plugins_list format="{{LinkedTitle}} ({{LinkedAuthor}}) - {{Description}}{{br/}}" target="_blank"]`

**Truncate the Description**

Two parameters exist to truncate the description, so it doesn't get too long unwieldy. You can specify a maximum number of words or a maximum number of characters using `words` or `chars`. Here's an example of each...

`[plugins_list format="{{LinkedTitle}} ({{LinkedAuthor}}) - {{Description}}{{br/}}" words=20]`

`[plugins_list format="{{LinkedTitle}} ({{LinkedAuthor}}) - {{Description}}{{br/}}" chars=80]`

You shouldn't do this but if you specify both then the shortest one will be used.

By default, if a truncation occurs, ellipsis will be added to the end. However, you can change this by using the `end` parameter and specifying your own ending. For example...

`[plugins_list format="{{LinkedTitle}} ({{LinkedAuthor}}) - {{Description}}{{br/}}" chars=80 end=" [More]"]`

**Remove Emoji**

If you want to remove emoji from the description, use the `emoji` parameter to achieve this. By default this is `true` but set to `false` to have them removed. For example...

`[plugins_list format="{{LinkedTitle}} ({{LinkedAuthor}}) - {{Description}}{{br/}}" emoji=false]`

**Sort by Author**

Want so sort the output by author and not plugin name? Just use the parameter `by_author`. For example...

`[plugins_list format="{{LinkedTitle}} ({{LinkedAuthor}}) - {{Description}}{{br/}}" by_author=true]`

**Cache**

By default your plugin list will be cached for 5 minutes, ensuring that performance is impacted as little as possible. Use the parameter `cache` to change the number of minutes. Set this to false to switch off caching.

For example...

`[plugins_list format="{{LinkedTitle}} ({{LinkedAuthor}}) - {{Description}}{{br/}}" cache=60]`

This will cache for 1 hour. The following will switch the cache off...

`[plugins_list format="{{LinkedTitle}} ({{LinkedAuthor}}) - {{Description}}{{br/}}" cache=false]`

== Using with the block editor ==

You can insert shortcodes directly into the block editor without an issue and it will be automatically added to a shortcode block. However, if you need to wrap HTML around it then this will cause issues. The solution here is to add a shortcode block first and then add the whole line into that. The HTML then works just fine.

== Plugin Count ==

A shortcode also exists to allow you to display the number of plugins that you have. Simply add `[plugins_number]` to your post or page and it will return the number of active plugins.

To display the number of active AND inactive plugins use...

`[plugins_number inactive=true]`

You can also display the number of inactive plugins by specifying...

`[plugins_number inactive=true active=false]`

As with the other shortcode results will be cached by default. To change the number of minutes simply use the `cache` parameter. Set it to `false` to switch off caching. For example...

`[plugins_number inactive=true cache=120]`

This will set the cache to 2 hours.

== Reviews & Mentions ==

[A default WP credit page would be kind of neat](http://halfelf.org/2012/penguins-just-gotta-be-me/#comments "PENGUINS JUST GOTTA BE ME")

== Installation ==

Plugins List can be found and installed via the Plugin menu within WordPress administration (Plugins -> Add New). Alternatively, it can be downloaded from WordPress.org and installed manually...

1. Upload the entire `plugins-list` folder to your `wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress administration.

Voila! It's ready to go.

== Frequently Asked Questions ==

= You've changed from using hashes to double braces for tags - help! =

Using double braces (i.e. {{ and {{) for templates is pretty standard so something I wanted to move towards. However, I don't want existing usage to break so I'm supporting both the old and new methods - however, I'm not documenting the old method so my hope is for people to move towards usage of double braces.

= I'm adding an HTML tag to the format parameter and it's not appearing =

For your security, not all HTML tags are allowed - we've limited this to any that are for the purposed of text formatting (inc. bullet points) and tables.

The full of allowed tags are: <a>, <b>, <big>, <blockquote>, <br>, <caption>, <center>, <cite>, <code>, <col>, <colgroup>, <div>,  <em>, <font>, <h1>, <h2>, <h3>, <h4>, <h5>, <h6>, <hr>, <i>, <img>, <li>, <ol>, <p>, <pre>, <q>, <s>, <small>, <span>, <strike>, <strong>, <style>, <sub>, <sup>, <table>, <td>, <th>, <tr>, <u> and <ul>.

== Screenshots ==

1. An example of the list in use

== Changelog ==

I use semantic versioning, with the first release being 1.0.

= 2.5.2 =
* Bug: What can I say? I certainly made the plugin secure in the last release. Mainly by stopping things from working. Sorry. Hopefully that's now fixed now. I've also taken the opportunity to review which HTML tags I'm allowing - the full list is in the FAQ.

= 2.5.1 =
* Enhancement: Strip HTML tags from the format parameter, unless it's for formatting. This is to ensure no XSS vulnerabilities can occur. Thanks to [Yuki Haruma](https://patchstack.com/database/researcher/3d15e16d-f25d-4426-8c6b-20ef9ec4c7f2) and [Patchstack](https://patchstack.com/) for reporting this to me.

= 2.5 =
* Enhancement: New header, inc. GPL information, added to the plugin. A bit boring, I know, but a change is a change.
* Enhancement: New parameters added allowing you to limit the description length to either a certain number of words or characters. The latter is intelligent, not chopping the sentence off in the middle of a wo
* Enhancement: Extra spacing and HTML is now stripped from the plugin description by default. HTML in a plugin description? I'm looking at you Jetpack...
* Enhancement: A new parameter allows you to have emoji removed from the description as well. Nothing wrong with emojis, mind you, but they're not for everyone
* Enhancement: To support the length truncation, I've also added a new parameter to that you can specify what happens at the end of the sentence when this happens
* Enhancement: Finally, I updated this README with improved formatting and better examples. Oh, and I better explain how to use this plugin with the block editor too.
* Maintenance: Tested under PHP 8.2. No changes required for this, but I can confirm that it works without issue /wipes sweat from brow/

= 2.4.4 =
* Enhancement: To reduce the plugin's output footprint, I've removed the HTML comments - they were useful for the rare case of debugging but adding un-needed content to each page load for the 99.99999% rest of the time
* Enhancement: Now follows the best code standards - not just the official WordPress standards but also the strictest WordPress VIP standards too
* Enhancement: Added donation links back in and generally improved the plugin meta
* Maintenance: Removed PHP version check as this is now covered in the plugin README

= 2.4.3 =
* Enhancement: A PHP detection function has been added, so an error will be reported if the required level of PHP is not available
* Bug: Now using `uasort` instead of `usort` to get around an issue with array keys. Thanks to [dgw](https://github.com/dgw)

= 2.4.2 =
* Maintenance: This release sees the minimum PHP version required, increased to PHP 7. If you're running on an older release, please continue to use 2.4.1 until you're able to upgrade your PHP
* Enhancement: Because minimal VIP coding standards are not enough, it now passes the full-fat VIP standards as well
* Enhancement: Can now sort the output by author

= 2.4.1 =
* Enhancement: Code quality enhancements to bring it in line with WordPress.com VIP coding standards

= 2.4 =
* Enhancement: Now uses the standard double braces for templates (and HTML as well)
* Enhancement: Can now specify to list just inactive plugins
* Enhancement: Now using a time constant instead of a hard-coded number
* Enhancement: Added Github links to plugin meta
* Maintenance: Changed caching from hours to minutes
* Maintenance: Tidying up of code

= 2.3.2 =
* Maintenance: Updates to README
* Maintenance: Removed un-needed language folder and domain path
* Maintenance: Removed donation links

= 2.3.1 =
* Bug: A number of the tags had stopped working. I don’t know what I’d been drinking when I tested the last release but it can’t have been good. Now all fixed
* Maintenance: Plugin now requires WP 4.6 to work as we need to move with the times
* Maintenance: Updated the links to my site because I like to move around
* Maintenance: Tweaked this README to reflect the changes above but also the new plugin directory layout

= 2.3 =
* Enhancement: Improved the performance of the search/replace of tags
* Enhancement: Added a method of adding HTML to the formatting without causing issues with the visual editor
* Enhancement: After WP 4.6 you no longer need to include the plugin domain. So I don't!
* Maintenance: Merged all the included files together as the total amount of code wasn't enough to justify having it split!

= 2.2.7 =
* Maintenance: Updated branding, inc. adding donation links

= 2.2.6 =
* Maintenance: Updated branding
* Maintenance: Stopped the naughty behavior of hard-coding the plugin folder name in INCLUDES
* Maintenance: Removed the apl- prefix from the file names

= 2.2.5 =
* Maintenance: Added text domain and domain path

= 2.2.4 =
* Enhancement: Added internationalization

= 2.2.3 =
* Maintenance: Updated links and changed branding

= 2.2.2 =
* Bug: Accidentally left some debug output in place. Sorry!

= 2.2.1 =
* Bug: Fixed PHP error
* Bug: Corrected caching
* Enhancement: Added uninstaller - cache will be wiped upon uninstall

= 2.2 =
* Maintenance: Added instructions for generating list via PHP function call
* Enhancement: Improved caching so that data is not left behind on options table
* Enhancement: Prevent plugin's HTML comment from appearing around each entry
* Enhancement: Add link target and nofollow option
* Enhancement: Added shortcode to return number of plugins

= 2.1 =
* Maintenance: Divided code into separate files all of which, except the main launch file, have been added into an 'includes' folder
* Maintenance: Split main code into separate functions to make future enhancement easier. This and the previous change have been made in preparation for version 3.
* Enhancement: Added caching
* Enhancement: Comment added to HTML output with debug information on

= 2.0 =
* Maintenance: Renamed plugin and functions within it
* Maintenance: Improved code readability, including adding PHPDoc comments
* Maintenance: Re-written README
* Maintenance: Changed default format to not display plugin version, as this is a security risk
* Enhancement: Added links to plugin meta

== Upgrade Notice ==

= 2.5.2 =
* Fixed a bug introduced in 2.5.1 which, amongst other things, stopped links from working
=== Easy Post Embed Code ===
Contributors: hlashbrooke
Tags: embed, code, oembed, copy, post, gutenberg
Requires at least: 4.4
Tested up to: 4.9.8
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Give you and your readers the ability to easily copy any post's embed code.

== Description ==

The oEmbed feature that is built into WordPress core from version 4.4 allows your content to be easily embedded in any other site. This is great if the site in which you are embedding your content supports oEmbed, but in many cases it does not. In these cases you will need to paste the full embed code on the site in question. This plugin provides that embed code for you directly on the post edit screen.

You can also choose to display the post embed code on the frontend, allowing other people to share your content more easily, with the built-in shortcode or Gutenberg block. The shortcode can be customised to include default width and height options, as well as show the embed code for any other post by optionally specifying the post ID.

**Features**

- Find your embed code directly on the post edit screen
- Customise the width and height of the embed before copying the code
- Add the embed code to your content with a shortcode or Gutenberg block
- Fully Gutenberg compatible with meta box and custom block type
- Fully backwards compatible with the current Classic Editor

**How to contribute**

If you want to contribute to Easy Post Embed Code, you can [fork the GitHub repository](https://github.com/hlashbrooke/Easy-Post-Embed-Code) - all pull requests will be reviewed and merged if they fit into the goals for the plugin.

== Installation ==

Installing "Easy Post Embed Code" can be done either by searching for "Easy Post Embed Code" via the "Plugins > Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org
1. Upload the ZIP file through the 'Plugins > Add New > Upload' screen in your WordPress dashboard
1. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. The embed code meta box on the Classic Editor post edit screen.
2. The full Classic Editor post edit screen (so you can see the meta box in context).
3. The Gutenberg block after being added to the post content.
4. The embed code meta box on the Gutenberg post edit screen.
5. The embed code being displayed on the frontend inside the post content.
6. The standard appearance of a WordPress post embed (not created by this plugin).

== Frequently Asked Questions ==

= The meta box is not showing for me - what gives? =

The embed code meta box will only show for posts that have a status of 'published' - it will not show for drafts or private posts as those cannot be embedded externally anyway.

= No really - the meta box is *still* not showing for me! =

That's not even a question, but the answer is most likely that you are using a WordPress version older than 4.4 - the oEmbed feature is only available in WordPress 4.4 and above, so you need to be more up to date in order to use this plugin.

= How do I use the shortcode? =

The shortcode to display the embed code for a post is `[embed_code]`. If used in the context of a post it will show the embed code for that post, but you can also specify a different post using the ID in the `post` paramter. Additionally, you can also set a custom width and height for the size attributes of the embed code, with the default being 500 and 350 respectively. a shortcode with all the available parameters being used will look like this:

`[embed_code post=36 width=800 height=400]`

== Changelog ==

= 1.1 =
* 2018-09-19
* Adding shortcode and Gutenberg block for displaying embed code on the frontend.

= 1.0 =
* 2015-11-03
* Initial release.

== Upgrade Notice ==

= 1.1 =
* 2018-09-19: Adding shortcode and Gutenberg block for displaying embed code on the frontend.
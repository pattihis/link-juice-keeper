=== Plugin Name ===
Contributors: sirzooro
Tags: google, link, links, redirect, seo
Requires at least: 2.2
Tested up to: 3.2.9
Stable tag: 1.2.3

This plugin helps you to keep the link juice by redirecting all non-existing URLs (they return error 404) to the blog front page using 301 redirect.

== Description ==

As you probably know, incoming links play important role in ranking well in Google and other search engines. Therefore you should assure that every incoming link leads to one of pages on your blog. This may be a challenge, because World Wide Web is dynamic and changes every day:

* you can remove some posts or pages on your blog;
* you can change URL scheme on whole blog;
* incoming links may became broken (e.g. due to some automatic text formatting);
* someone may simply put wrong (broken) link somewhere.

All of the above leads to link juice waste. You can avoid this by using this plugin. It redirects all non-existing URLs which normally return a 404 error to the blog front page using 301 (permanent) redirect. This way everyone who comes to your blog via broken link (both people and robots) will be redirected to the front page.

The only one exception are files in root blog directory with names starting with `noexist_`. They are used by Google to verify page in Google Webmasters Tools, so Link Juice Keeper does not redirect them.

[Changelog](http://wordpress.org/extend/plugins/link-juice-keeper/changelog/)

== Installation ==

1. Upload `link-juice-keeper` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure and enjoy :)

== Changelog ==

= 1.2.3 =
* Marked as compatible with WP 3.2.x

= 1.2.2 =
* Marked as compatible with WP 2.9.x

= 1.2.1 =
* Marked as compatible with WP 2.8.5

= 1.2 =
* Fix: does not redirect Google Bot doing page verification for Google Webmasters Tools

= 1.1.1 =
* Make plugin compatible with WordPress 2.8

= 1.1 =
* Decreased filter priority, so now it will run after ones with default priority

= 1.0 =
* Initial version

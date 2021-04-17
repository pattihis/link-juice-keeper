=== Plugin Name ===
Contributors: pattihis, sirzooro
Tags: google, link, links, redirect, seo
Requires at least: 3.0.1
Tested up to: 5.7
Requires PHP: 5.6
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Improve your SEO and keep your link juice by automatically redirecting all 404 errors to any page/post/url. User friendly options and log feature.

== Description ==

`Page not found` ( status 404 ) errors are very common and they are one of the main reasons a search engine, like Google, can give you a lower ranking. Taking steps to handle 404 errors can be complex for non-techy users. With this free plugin you can finally eliminate all 404 errors with just 1 click, once and for all!

### What are 404s?

As you probably know, incoming links play important role in ranking well in Google and other search engines. Therefore you should assure that every incoming link leads to one of your valid pages on your blog. This may be a challenge because your website is dynamic, it changes every day:

* you might remove or unpublish posts or pages;
* you might change your URL structure on your blog;
* links may brake because of technical misconfiguration;
* a webmaster might add your link in their posts with a typo;
* a visitor may simply mis-type your post's URL

Any of the above will lead to link juice waste and a 404 error. You can avoid this simply by installing this plugin. It redirects all non-existing URLs, which normally return a 404 error, to the page/post of your choice. This way everyone who comes to your blog via a broken link (both people and robots) will be redirected to a valid page.

### Features

* You can redirect errors to your homepage, an existing page, post or even a custom link
* You can optionally monitor/log all 404 errors
* You can choose which redirect method to be used (301,302,307)
* You can configure email notifications for every 404 incident
* Translations ready
* Free with lifetime updates
* Build according to best practices and WordPress coding standards
* Never have a 404 error again!

[Changelog](http://wordpress.org/extend/plugins/link-juice-keeper/changelog/)

== Installation ==

1. In your WordPress admin panel, go to Plugins > New Plugin, search for "Link Juice Keeper" and click "Install now"
2. Alternatively, download the plugin and upload the contents of link-juice-keeper.zip to your plugins directory, which usually is /wp-content/plugins/
3. Activate the plugin
4. Go to "link Juice Keeper" tab on your admin menus
5. Configure the plugin options with available settings


== Changelog ==

= 2.0.0 =
* Major update

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

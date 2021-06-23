=== AffiliateWP - External Referral Links ===
Contributors: sumobi, mordauk
Tags: AffiliateWP, affiliate, affiliates, affwp, Pippin Williamson, Andrew Munro, mordauk, pippinsplugins, sumobi, ecommerce, e-commerce, e commerce, selling, membership, referrals, marketing
Requires at least: 4.6
Tested up to: 5.5
Requires at least: 3.3
Tested up to: 5.7
Requires PHP: 5.3
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows affiliates to promote external landing pages by including the affiliate's ID or username in any outbound links to your e-commerce store.

== Description ==

> This plugin was built to be used in conjunction with [AffiliateWP](https://affiliatewp.com/ "AffiliateWP").

Like other affiliate plugins, AffiliateWP must be installed on the same domain as your e-commerce system (Easy Digital Downloads, WooCommerce etc) to properly track visits and referrals.

This plugin allows your affiliates to promote any landing page (or site) that exists on a completely separate domain. Simply install this plugin on the external WordPress site and your affiliates can now promote it using the site's URL and their affiliate ID or username appended (eg /?ref=123 or /?ref=john). If a customer uses the affiliate's referral URL, any outbound links to your e-commerce store will automatically include the affiliate's ID or username. If the customer then makes a purchase on your e-commerce store, the proper affiliate will be awarded commission. The affiliate's ID/username is stored in a cookie so even if the customer moves between pages on your site, the outbound links will still have the affiliate's ID/username appended.

[More information](https://affiliatewp.com/add-ons/official-free/external-referral-links/)

**What is AffiliateWP?**

[AffiliateWP](https://affiliatewp.com/ "AffiliateWP") provides a complete affiliate management system for your WordPress website that seamlessly integrates with all major WordPress e-commerce and membership platforms. It aims to provide everything you need in a simple, clean, easy to use system that you will love to use.

== Installation ==

1. Unpack the entire contents of this plugin zip file into your `wp-content/plugins/` folder locally
1. Upload to the site that you'd like affiliates to promote
1. Navigate to `wp-admin/plugins.php` on your site (your WP Admin plugin page)
1. Activate this plugin and navigate to Settings &rarr; External Referral Links to configure the plugin

OR you can just install it with WordPress by going to Plugins >> Add New >> and type this plugin's name

== Screenshots ==

1. The admin settings

== Changelog ==

= 1.0.2 =
* Fix: Referral variable of "join" conflicted with jquery.cookie script

= 1.0.1 =
* New: French language files, props fxbenard
* Fix: Remove trailing slash on URLs that already have query string
* Fix: Cookie expiration not being pulled from settings

= 1.0 =
* Initial release

=== AffiliateWP - External Referral Links ===
Contributors: sumobi, mordauk
Tags: AffiliateWP, affiliate, Pippin Williamson, Andrew Munro, mordauk, pippinsplugins, sumobi, ecommerce, e-commerce, e commerce, selling, membership, referrals, marketing
Requires at least: 3.3
Tested up to: 4.2.2
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows affiliates to promote external WordPress sites by including the affiliate's ID or username in any outbound links

== Description ==

> This plugin requires [AffiliateWP](http://affiliatewp.com/ "AffiliateWP") in order to function.

Like other affiliate plugins, AffiliateWP must be installed on the same domain as your eCommerce or membership system to properly track visits and referrals.

This plugin allows your affiliates to promote your external WordPress-based site/s. Simply install this plugin on the external WordPress site and your affiliates can now promote it using the site's URL and their affiliate ID or username appended (eg /?ref=123 or /?ref=john). 

If a customer uses the affiliate's referral URL, any outbound links to your main website where AffiliateWP is installed will automatically include the affiliate's ID or username. If the customer then makes a purchase on your main website, the referring affiliate will be awarded a commission. 

The affiliate's ID or username is stored in a cookie so even if the customer moves between pages on your site, the outbound links will still have the affiliate's ID or username appended.

More information at [http://affiliatewp.com/addons/external-referral-links/](http://affiliatewp.com/addons/external-referral-links "External Referral Links")

**What is AffiliateWP?**

[AffiliateWP](http://affiliatewp.com/ "AffiliateWP") provides a complete affiliate management system for your WordPress website that seamlessly integrates with all major WordPress e-commerce and membership platforms. It aims to provide everything you need in a simple, clean, easy to use system that you will love to use.

== Installation ==

1. Unpack the entire contents of this plugin zip file into your `wp-content/plugins/` folder locally
1. Upload to your site where your 
1. Navigate to `wp-admin/plugins.php` on your site (your WP Admin plugin page)
1. Activate this plugin and navigate to Settings &rarr; External Referral Links to configure the plugin

OR you can just install it with WordPress by going to Plugins >> Add New >> and type this plugin's name

== Frequently Asked Questions ==

== Screenshots ==

1. Admin settings

== Upgrade Notice ==

== Changelog ==

= 1.0.1 =
* Fix: Remove trailing slash on URLs that already have query string
* Fix: Cookie expiration not being pulled from settings

= 1.0 =
* Initial release
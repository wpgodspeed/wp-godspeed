=== Instant WordPress CDN - WP Godspeed ===

Contributors: godspeedcdn, wpgodspeed
Tags: cdn, content delivery network, content distrubution network, godspeed cdn, performance, lazy load
Requires at least: 4.6
Tested up to: 4.9.7
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP Godspeed is an instant bolt-on CDN plugin for WordPress.

== Description ==

A **content delivery network (CDN)** is a global network of servers which expedite the delivery of your site's content on behalf of your server. The primary benefits of a CDN are extensive, but mainly you get more reliability, scalability, and performance. The **WP Godspeed CDN plugin** helps you to easily connect a CDN to WordPress.

= What does this CDN plugin do exactly? =

The WP Godspeed CDN plugin has been created to serve static content directly via CDN URLs. It will first setup your site's very own CDN. Then, you can simply activate/deactivate the CDN on the options page.

= Features =

* Serves wp-content resources from CDN links
* CDN automatically detects correct HTTP/HTTPS protocol
* Reports daily usage + historical data for 6 months
* Lazy load images/iframes/YouTube

While most caching plugins will enable the use of a CDN, [WP Godspeed](https://wpgodspeed.io) *is* the CDN service provider. It's super easy to setup & use on all of your WordPress sites, with a one click setup process.

WP Godspeed makes your site load really fast by offloading static resources (such as images, audio/video, archives, etc.) to your site's very own lightning fast global CDN.

It includes 5GB of transfer free every month, and upgrade plans start at $12 for 25GB.

The plugin intentionally does not mess with CSS or JS files, so there's very little risk for breaking your site.

*Important: while this CDN plugin has been tested thoroughly, if something isn't working for you, don't give it a poor rating. Instead, [please file an issue on Github](https://github.com/wpgodspeed/wp-godspeed/issues) and we'll fix any issues for you immediately. Thank you!*

== Installation ==

**Using The WordPress Dashboard**
1. Navigate to **Add New** in the Plugins dashboard menu
2. In the search field, type **WP Godspeed** and press enter
3. Click the **Install** button, and then **Activate**

**Uploading in WordPress Dashboard**
1. Navigate to **Add New** in the Plugins dashboard menu
2. Click the **Upload Plugin** button
3. Select **wp-godspeed.zip** from your computer
4. Click **Install Now**
5. Activate the plugin in the Plugin dashboard

**Using FTP**
1. Download **wp-godspeed.zip**
2. Extract the **wp-godspeed** directory to your computer
3. Upload the **wp-godspeed** directory to the /wp-content/plugins/ directory
4. Activate the plugin in the Plugin dashboard

== Frequently Asked Questions ==

= What is a CDN? =

CDN is an acronym for Content Delivery Network.

A CDN is a geographically disparate network comprised of hundreds of servers spread across the globe, all of which serve static resources to visitors on behalf of your website.

= How does a CDN Work? =

It works via a series of protocols which orchestrate the synchronization and transmission of static resources (such as images, various types of audio/video media, archives, documents and PDFs, [etc](https://github.com/wpgodspeed/wp-godspeed/blob/master/includes/class-wp-godspeed-cdn-stack.php#L105).) from your web server, to the global network of distributed endpoints.

WP Godspeed CDNs feature [hundreds of network endpoints spanning the globe](https://www.google.com/maps/d/viewer?mid=1Gs-4KuyCiFp1fBK8uwfhCNzcVlQ&hl=en&usp=sharing) that serve your site’s content.

= Why do I need a WordPress CDN? =

**Speed & Performance**

Having a WordPress CDN (versus not having one) makes all the difference in the world. Offloading the largest files on your site to the CDN has clear performance implications.

Let’s say for example, that your site lives on a server in New York City and a visitor is accessing your site from Stockholm. Instead of downloading every single file from your server in NYC (with lengthy network latency & round trip times for every request) the CDN automatically serves your site’s content directly to the visitor via the network endpoint which is closest in proximity to the visitor.

**Resiliance**

With regard to the performance of your site’s server, the CDN is extremely efficient when it comes to removing traffic load from the server, and distributing it evenly throughout the CDN. By reducing the overall load on your server, you will drastically improve the stability and performance of the server itself.

**Enhanced User Experience**

Good user experience (UX) means that your site will notice a decline in bounce rate, more page views per visitor session, and thus more page views for your site overall.

In other words, a faster site means a better user experience. And better UX means more traffic.

**Increased SEO**

Google has clearly shown favoritism in the seach results for sites which are both faster and secure, versus sites which have not paid any attention to performance optimization or running full SSL.

Simply put, a faster site also means better SEO, page rank, and an increase in your site’s organic traffic levels.

= What makes this WordPress CDN plugin different? =

Some of the most popular plugins in the WordPress community such as W3 Total Cache, WP Super Cache, WP Rocket, etc., will enable the use of a CDN (if you have one, or if you have manually setup your own CDN), but none of which also provide the CDN.

WP Godspeed not only enables the CDN for your WordPress site, but also is the CDN service, and provides world class CDN service for your WordPress site with the simplicity of one-click setup.

In providing these CDN services, this plugin drastically differs from other plugins which are simply performance-enabling, but still require tons of technical expertise and manual configuration.

= Does this support HTTP/2? =

Yes, it does.

The protocol for all SSL-enabled sites is HTTP/2 by default.

= Does this support SSL? =

Yes, WP Godspeed supports advanced protocols and ciphers, and advanced SSL features, such as Session Tickets, OCSP Stapling, and Perfect Forward Secrecy.

Rest assured that your WP Godspeed CDN will operate under the highest levels of security using modern security standards.

= Will this CDN plugin work if I don't have SSL? =

Absolutely. We use a security policy that matches the viewer protocol for your site. This means that if you don’t have SSL installed, all CDN resources (while still accessible via secure HTTPS links) will be served over plain text along with the rest of your site.

However, if you do have SSL installed, all CDN resources will be served exlcusively over SSL as well. There’s no need to worry about mixed content browser warnings, and there’s no risk of breaking your site’s existing SSL functionality by using the WP Godspeed CDN.

= Will this work if I have an "EV" SSL certificate installed? =

Yes, it will work.

An extended value (EV) certificate is a type of SSL certificate which embeds additional information about your site/business into the certificate. It instructs the browser to display the name of your business as a green badge next to the browser address bar.

Typically, EV certificates are expensive compared to the more common domain validated (DV) certificates, as they require actual human verification of your business entity.

Since the WP Godspeed CDN does not mask the IP address of your server, this will not affect the functionality of your site’s existing SSL, regardless of which type of certificate is presently installed (whether DV or EV).

= Is this standards compliant? =

Yes, every WP Godspeed CDN is PCI DSS compliant and HIPAA eligible.

We have enabled additional services to assist with auditing purposes.

= Does the plugin support Multisite? =

Sorry, the plugin does not support Multisite at this time.

= Author =

* [GodspeedCDN](https://wpgodspeed.com "GodspeedCDN")
* [WPGodspeed](https://wpgodspeed.com "WPGodspeed")

== Screenshots ==

1. CDN usage statistics

== Changelog ==

= 0.9.7 =
* [Changed] Bootstrap 4.1.2

= 0.9.6 =
* [Fixed] Lazyload js path
* [Changed] Verbage & terminology to be more clear

= 0.9.5 =
* [Added] CDN stack global threshold
* [Changed] Test version bump

= 0.9.4 =
* [Fixed] Issue with ajax registration
* [Changed] Registration auto-dismissed if ignored

= 0.9.3 =
* [Changed] Chart.js upgrade to 2.7.2
* [Fixed] Minor issue with plugin debug/reset

= 0.9.2 =
* [Fixed] Display API error messages

= 0.9.1 =
* [Changed] Removed jquery-ui

= 0.9.0 =
* [Changed] Preperations for repo submission

= 0.8.9 =
* [Added] Additional timezone checks
* [Added] IP check

= 0.8.8 =
* [Changed] Bootstrap 4.1.1

= 0.8.7 =
* [Changed] Test version bump

= 0.8.6 =
* [Changed] Lazy tweaks

= 0.8.5 =
* [Fixed] LazyLoad css

= 0.8.4 =
* [Fixed] More JS issues
* [Changed] More JS cleanup

= 0.8.3 =
* [Fixed] Registration callback issue

= 0.8.2 =
* [Added] Debug functionality

= 0.8.1 =
* [Fixed] Various things

= 0.8.0 =
* [Changed] Cleaned up JS

= 0.7.9 =
* [Changed] Moved JS from setup page into admin.js

= 0.7.8 =
* [Fixed] Chart stats

= 0.7.7 =
* [Changed] Tons of things
* [Fixed] All kinds of broken stuff

= 0.7.6 =
* [Fixed] Broken stuff

= 0.7.5 =
* [Fixed] A lot

= 0.7.4 =
* [Changed] Something since the previous version

= 0.7.3 =
* [Added] A truckload of new JS

== Upgrade Notice ==

= 1.0.0 =
Not yet...

For more changelogs please refer to the [changelog.txt](https://github.com/wpgodspeed/wpgodspeed/blob/master/changelog.txt) file.
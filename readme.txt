=== SO Remove Translation Services ===
Contributors: senlin
Donate link: http://so-wp.com/donations
Tags: wpml, translation services, remove
Requires at least: 4.0
Tested up to: 4.4
Stable tag: 1.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This free WPML Addon removes the entire Translation Services block as well as all references to it from the Translation Management addon of the WPML plugin.

== Description ==

The Translation Management addon of the WPML plugin shows two translation services on the Translators tab.

It is a great service to have for most people, as they can choose and use a translation service right from the WPML Dashboard without having to leave the site admin!

But if you are a translation agency, then you are probably less happy about it, after all why would you want to show competing services right in the WordPress admin interface of your clients?

With the SO Remove Translation Services plugin activated the entire Translation Services block is removed, including all references to it.

We support this plugin exclusively through [Github](https://github.com/senlin/so-remove-icl-promotion/issues). Therefore, if you have any questions, need help and/or want to make a feature request, please open an issue over at Github. You can also browse through open and closed issues to find what you are looking for and perhaps even help others.

**PLEASE DO NOT POST YOUR ISSUES VIA THE WORDPRESS FORUMS**

Thanks for your understanding and cooperation.

If you like the SO Remove Translation Services plugin, please consider leaving a [review](http://wordpress.org/support/view/plugin-reviews/so-remove-icl-promotion#postform) or making a [donation](http://so-wp.com/donations/). Thanks!


== Installation ==

= Wordpress =

Search for "SO Remove Translation Services" and install with the **Plugins > Add New** back-end page.

 &hellip; OR &hellip;

Follow these steps:

 1. Download zip file.

 2. Upload the zip file via the Plugins > Add New > Upload page &hellip; OR &hellip; unpack and upload with your favourite FTP client to the /plugins/ folder.

 3. Activate the plugin on the Plugins page.

Done!


== Frequently Asked Questions ==

= Where is the settings page? =

You can stop looking, there is none. Activate the plugin to remove the Translation Services block and all references to it, deactivate the plugin to bring them back.

= I have an issue with this plugin, where can I get support? =

Please open an issue on [Github](https://github.com/senlin/so-remove-icl-promotion/issues)

== Screenshots ==

N/A

== Changelog ==

= 2.0.0 (date: 2015.08.22) =

* Translation Services are promoted in a different way within the WPML Dashboard, therefore we have changed the plugin (and its name). This is a temporary move, because WPML will come soon with a filter that enables you to remove or hide the entire Translation Services block from the Translators tab of the WPML Translation Management Addon.
* Changed the check for WPML being active into the Translation Management addon being active instead.
* Changed the deactivation text
* Changed the plugin activation to remove any existing definitions from the wp-config.php file

= 1.0.0 (date: 2015.04.08) =

* Release version
* banner image (in assets folder) by [Kelly Sikkema](https://unsplash.com/kellysikkema)

== Upgrade Notice ==

= 2.0.0 =

* If you don't use the Translation Management Addon, you can remove this plugin as it no longer serves any purpose. The reason is that WPML has changed the way if integrates the services of ICanLocalize into the WPML Dashboard (only when the Translation Management Addon is active).
* Version 2.0.0 no longer writes to your wp-config.php file. We therefore need to ask you to deactivate the plugin after updating. After that you can reactivate the plugin. The reason we ask you to do this is to flush out the old rules we added to your wp-config.php file and which now have become redundant. Apologies for any inconvenience.

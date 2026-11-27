=== User Notifications ===
Contributors: dogbytemarketing
Tags: notifications, alerts, marketing, user engagement, woocommerce
Tested up to: 6.7
Stable tag: 1.0.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Easily display a notification bell with a badge count in your WordPress menu to notify users of new updates, promotions, and more.


== Description ==

User Notifications lets you seamlessly inform WordPress & WooCommerce users about updates, sales, and more via the site navigation menu. You can add a notification menu easily by using the [notifications_dogbytemarketing] shortcode to inject a notification bell into your menu. The bell displays a badge when new notifications are available, ensuring users never miss an update. If you use something other than BootStrap icons, you can do [notifications_dogbytemarketing class="your-icon-class"] to use your own icons.

This plugin is ideal for websites and WooCommerce stores looking to improve user engagement and streamline notification delivery.


## Features

* Supports WooCommerce
* Send notifications when a WooCommerce order is completed (shipped)
* Custom post type for creating and managing notifications.
* Integration with WordPress menus using a shortcode.
* Dynamic badge to highlight unread notifications.
* Sidebar notifications for detailed views of messages.
* Clean and modern design with customizable icons and styles.
* Privacy settings


## Roadmap

* Add ability to manually send notifications to individual users
* EDD integration


## GDPR

We are not lawyers and always recommend doing your own compliance research into third party plugins, libraries, ect, as we've seen other plugins not be in compliance with these regulations.

This plugin uses the Bootstrap, BootStrap Icons, and SweetAlert2 3rd party libraries. These libraries are loaded locally to be compliant with data protection regulations.

This plugin collects and stores certain data on your server to ensure proper functionality. This includes:

* Storing plugin settings
* Remembering which notices have been dismissed


== Installation ==

1. Backup WordPress
1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly
1. Activate the plugin through the 'Plugins' screen in WordPress


== Screenshots ==

1. Preview
2. General Settings
3. WooCommerce Settings
4. Privacy Settings


== Changelog ==

= 1.0.2 =
* Added: Settings to Notifications admin menu
* Added: Enabled setting under general to disable notifications on frontend
* Added: Bootstrap Icon setting under general section
* Added: WooCommerce and Privacy sections
* Added: Order notifications under WooCommerce section
* Added: Read Tracking and User Targeting under privacy section
* Added: SweetAlert2 for nice alerts on setting page (Modified)
* Added: Handling for if order notifications are not enabled then it won't send order notifications
* Updated: Notification bar to have handling notifications for specific users
* Bugfix: Notification expandable if no description
* Replaced: Font Awesome icons with Bootstrap icons since they are lighter than Font Awesome and don't require additional JS


= 1.0.1 =
* Bugfix: Notifications not showing
* Optimized: Code base for ease of extending in the future

= 1.0.0 =
* Initial Release


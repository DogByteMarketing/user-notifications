=== User Notifications ===
Contributors: dogbytemarketing
Tags: notifications, alerts, marketing, user engagement, woocommerce
Tested up to: 7.1
Stable tag: 1.1.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Easily display a notification bell with a badge count in your WordPress menu to notify users of new updates, promotions, and more.


== Description ==

User Notifications lets you seamlessly inform WordPress & WooCommerce users about updates, sales, and more via the site navigation menu. You can add a notification menu easily by using the [notifications_dogbytemarketing] shortcode to inject a notification bell into your menu. The bell displays a badge when new notifications are available, ensuring users never miss an update. If you use something other than BootStrap icons, you can do [notifications_dogbytemarketing class="your-icon-class"] to use your own icons. You can also use the "class_container" attribute to add additional classes to the notification-container element.

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
* Admin toolbar bell for notifications from other plugins
* Hook for other plugins to add dismissible notifications in the admin bar and on the frontend
* Allows other plugins to integrate with User Notifications and send custom notifications, with an optional setting to disable notifications from external plugins.
* Mark one notification as read, or mark all as read
* Relative time on notification rows

Want to help? Submit a PR on [Github](https://github.com/DogByteMarketing/user-notifications/).


## Roadmap

* Add ability to manually send notifications to individual users
* EDD integration


## For developers

Other plugins can add their own notifications when User Notifications is active. If this plugin is not active, the filter never runs.

The filter is `user_notifications_notifications`. The second argument is the context: `admin_bar` or `frontend`.

Each item needs an `id` and a `title`. `message` and `url` are optional. Every notification can be dismissed. `id` is stored when the visitor dismisses the item, so keep it stable. Ids are saved with `sanitize_key`, so use lowercase letters, numbers, hyphens, and underscores.

The admin bell is always in the WordPress admin toolbar, next to the username. The badge shows the number of `admin_bar` notifications that have not been marked read. When that number is zero, the bell stays and the badge is hidden. Marking one read, or marking all read, keeps the item in the panel. An optional `time` unix timestamp sets the relative time. If it is left out, the first time that id appears is stored and reused.

Frontend items are added to the notification sidebar and the badge. Frontend notifications must be enabled in the plugin settings. Logged-in dismissals are saved on the user account. Guest dismissals are saved in the `user_notifications_dismissed` cookie. The Advanced setting Disable Third-Party Notifications turns this filter off for both contexts.

```php
add_filter('user_notifications_notifications', 'my_plugin_notifications', 10, 2);

function my_plugin_notifications($notifications, $context) {
  if ('admin_bar' !== $context && 'frontend' !== $context) {
    return $notifications;
  }

  $notifications[] = array(
    'id'          => 'my-plugin-welcome',
    'title'       => 'Welcome',
    'message'     => 'You can dismiss this.',
    'url'         => 'https://example.com',
  );

  return $notifications;
}
```

Return the item for `admin_bar`, `frontend`, or both.


## GDPR

We are not lawyers and always recommend doing your own compliance research into third party plugins, libraries, ect, as we've seen other plugins not be in compliance with these regulations.

This plugin uses the Bootstrap, BootStrap Icons, and SweetAlert2 3rd party libraries. These libraries are loaded locally to be compliant with data protection regulations.

This plugin collects and stores certain data on your server to ensure proper functionality. This includes:

* Storing plugin settings
* Remembering which notices have been dismissed
* Storing dismissed notification IDs from other plugins in user meta on the logged-in user's account (`_dismissed_user_notifications`)
* Storing which admin notifications a logged-in user has marked read in user meta (`_read_admin_notifications`)
* Storing the first time each admin notification id was seen, in a site option (`user_notifications_admin_seen`), so relative times stay the same
* For guests, storing those dismissed notification IDs in a first-party cookie named `user_notifications_dismissed`. The cookie lasts one year, stays on this site, and is not shared with other sites. It only remembers which notifications a guest dismissed.


== Installation ==

1. Backup WordPress
1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly
1. Activate the plugin through the 'Plugins' screen in WordPress


== Screenshots ==

1. Preview
2. General Settings
3. WooCommerce Settings
4. Privacy Settings
5. Advanced Settings


== Changelog ==

= 1.1.0 =
* Added: Admin toolbar bell next to the username for notifications from other plugins
* Added: Filter so other plugins can add dismissible notifications to the admin bar and the frontend
* Added: Guest cookie that remembers dismissed frontend notifications from other plugins
* Added: Setting to turn off notifications from other plugins
* Added: Mark one notification as read, and mark all as read
* Added: Relative time on notification rows
* Updated: Notification sidebar layout
* Updated: Tested up to WordPress 7.1

= 1.0.4 =
* Added: Translations
* Added: Shortcode attribute "class_container" in order to add additional classes to the notification-container
* Added: Toggle for disabling automatic adding of "no-scroll" class on click of notification icon as it can conflict with some themes
* Bugfix: Notification bar empty when no notifications
* Bugfix: Settings need to be saved twice after initial install

= 1.0.3 =
* Added: Security enhancements.
* Bugfix: When marking notifications as read, if there's an error it would be stuck on loading icon.

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


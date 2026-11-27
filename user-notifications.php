<?php

/**
 * Plugin Name: User Notifications
 * Description: Send notifications to your users.
 * Version: 1.0.0
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * Author: Dog Byte Marketing
 * Author URI: https://www.dogbytemarketing.com
 * Plugin URI: https://www.dogbytemarketing.com/contact/
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

namespace DogByteMarketing;

/**
 * Increase cSAT
 * Send marketing messages to logged in and guest users
 * Easily notify users of changes
 */

/**
 * To-Do
 * 
 * Add admin settings for design and colors and icon classes
 * 
 * Add notification count
 * Add order status updates as notifications to users
 * Store read messages via the users account (add a snippet about updating their GDPR notice)
 * Implement an option to only show future notifications to new users
 * Implement an option for Shortcode, and a fixed icon injection
 * Implement an option for auto-delete last X days
 * Implement option in CPT to set who to show to
 * Implement option in CPT to have expiration date
 * Add tracking for opens / clicks
 * Add hooks for other plugin developers to use
 * 
 * Add we want to hear your ideas on plugin page
 * 
 */

class User_Notifications
{

  public function init() {
    add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    add_action('init', array($this, 'create_notification_post_type'));
    add_action('wp_footer', array($this, 'side_notifications'));
    add_shortcode('notifications_dogbytemarketing', array($this, 'notifications'));
    add_filter('wp_nav_menu_objects', array($this, 'menu_shortcode'));
  }

  /**
	 * Enqueue scripts
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_style('user-notifications', plugins_url('/css/public/style.css', __FILE__), array(), filemtime(plugin_dir_path(dirname(__FILE__)) . dirname(plugin_basename(__FILE__))  . '/css/public/style.css'));
    wp_enqueue_script('user-notifications', plugins_url('/js/public/main.js', __FILE__), array('jquery'), filemtime(plugin_dir_path(dirname(__FILE__)) . dirname(plugin_basename(__FILE__))  . '/js/public/main.js'), true);
	}

	public function create_notification_post_type() {
    $args = array(
      'labels' => array(
        'name'               => 'Notifications',
        'singular_name'      => 'Notification',
        'add_new'            => 'Add New Notification',
        'add_new_item'       => 'Add New Notification',
        'edit_item'          => 'Edit Notification',
        'new_item'           => 'New Notification',
        'view_item'          => 'View Notification',
        'search_items'       => 'Search Notifications',
        'not_found'          => 'No notifications found',
        'not_found_in_trash' => 'No notifications found in Trash',
        'all_items'          => 'All Notifications',
        'menu_name'          => 'Notifications',
        'name_admin_bar'     => 'Notification',
      ),
      'public'            => true,
      'has_archive'       => true,
      'show_ui'           => true,
      'show_in_menu'      => true,
      'supports'          => array('title', 'editor', 'author', 'custom-fields'),
      'menu_position'     => 5,
      'menu_icon'         => 'dashicons-bell',
      'capability_type'   => 'post',
      'rewrite'           => array('slug' => 'notifications'),
    );

    // Register the post type
    register_post_type('user_notification', $args);
	}

  public function side_notifications() {
    ?>
    <!-- <div class="notification-sidebar-container"> -->
    <div class="notification-sidebar-container" style="display: none;">
      <div class="notification-sidebar">
        <div class="actions">
          <div class="col-6">
            <!-- This will be added later -->
            <!-- <a href="javascript:void(0);" class="read-notifications">
              MARK ALL READ
            </a> -->
          </div>
          <div class="col-6 end">
            <a href="javascript:void(0);" class="close-notifications">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" width="24" height="24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
              </svg>
            </a>
          </div>
        </div>
        <div class="notifications">
          <?php
          $notifications = get_posts(array(
            'post_type'      => 'notification',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
          ));
          ?>
          <?php foreach($notifications as $notification) : ?>
            <div class="notification">
              <div class="notification-preview-container">
                <div class="notification-preview">
                  <div class="col-11"><?php echo esc_html($notification->post_title); ?></div>
                  <div class="col-1 end">
                    <a href="javascript:void(0);" class="expand-notification">
                      <svg class="expand-icon active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                      </svg>
                      
                      <svg class="expand-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                      </svg>
                    </a>
                  </div>
                </div>
              </div>
              <div class="notification-full" style="display: none;">
                <?php echo wp_kses_post($notification->post_content); ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php
  }

  public function notifications($atts) {
		$atts = shortcode_atts(
			array(
				'class' => '',
			),
			$atts,
			'notifications_dogbytemarketing'
		);

		ob_start();
		
		$this->show_notifications($atts);

		$output = ob_get_clean();

    return $output;
  }

  public function menu_shortcode($items) {
    foreach ($items as $item) {
      if (str_contains($item->title, '[notifications_dogbytemarketing')) {
        $item->title = do_shortcode($item->title);
      }
    }

    return $items;
  }

  private function show_notifications($atts) {
    $class = isset($atts['class']) ? sanitize_text_field($atts['class']) : '';

    if (!$class) {
      // wp_enqueue_style('font-awesome', plugins_url('/css/font-awesome.min.css', __FILE__), array(), filemtime(plugin_dir_path(dirname(__FILE__)) . dirname(plugin_basename(__FILE__))  . '/css/font-awesome.min.css'));
    }
    ?>
    <a href="javascript:void(0)" class="notification-container">
      <i class="<?php echo $class ? esc_attr($class) : 'fa fa-bell'; ?> notification-icon" aria-hidden="true"></i>
      <!-- This is coming soon -->
      <!-- <span class="notification-bubble">12</span> -->
    </a>
    <?php
  }

}

$user_notifications = new User_Notifications;
$user_notifications->init();
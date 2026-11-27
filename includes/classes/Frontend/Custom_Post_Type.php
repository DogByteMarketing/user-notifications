<?php

namespace DogByteMarketing\User_Notifications\Frontend;

class Custom_Post_Type {
  
  /**
   * Init
   *
   * @return void
   */
  public function init() {
    add_action('init', array($this, 'create_notification_post_type'));
  }
  
  /**
   * Create the custom post type
   *
   * @return void
   */
  public function create_notification_post_type() {
    $args = array(
      'labels' => array(
        'name'               => __('Notifications', 'user-notifications'),
        'singular_name'      => __('Notification', 'user-notifications'),
        'add_new'            => __('Add New Notification', 'user-notifications'),
        'add_new_item'       => __('Add New Notification', 'user-notifications'),
        'edit_item'          => __('Edit Notification', 'user-notifications'),
        'new_item'           => __('New Notification', 'user-notifications'),
        'view_item'          => __('View Notification', 'user-notifications'),
        'search_items'       => __('Search Notifications', 'user-notifications'),
        'not_found'          => __('No notifications found', 'user-notifications'),
        'not_found_in_trash' => __('No notifications found in Trash', 'user-notifications'),
        'all_items'          => __('All Notifications', 'user-notifications'),
        'menu_name'          => __('Notifications', 'user-notifications'),
        'name_admin_bar'     => __('Notification', 'user-notifications'),
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
  
}
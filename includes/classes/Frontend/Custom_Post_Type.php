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
  
}
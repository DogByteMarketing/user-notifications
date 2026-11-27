<?php

namespace DogByteMarketing\User_Notifications\Frontend;

use DogByteMarketing\User_Notifications\Utils;

class WooCommerce {
  
  /**
   * Init
   *
   * @return void
   */
  public function init() {
    add_action('woocommerce_loaded', array($this, 'woocommerce_loaded'));
  }
  
  /**
   * These hooks will only fire if WooCommerce is activated
   *
   * @return void
   */
  public function woocommerce_loaded() {
    add_action('woocommerce_order_status_completed', array($this, 'create_user_notification'), 10, 1);
  }

  /**
   * Create user notification when an order is completed
   *
   * @param  int $order_id The order ID
   * @return void
   */
  public function create_user_notification($order_id) {
    $user_targeting      = Utils::get_option('privacy', 'user_targeting');
    $order_notifications = Utils::get_option('woocommerce', 'order_notifications');

    // Don't create notification if tracking isn't enabled
    if (!$user_targeting || !$order_notifications) {
      return;
    }

    if (!$order_id) {
      return;
    }

    $order = wc_get_order($order_id);

    if (!$order) {
      return;
    }

    $user_id = $order->get_user_id();

    if (!$user_id) {
      return;
    }

    $notification_sent = $order->get_meta('_notification_sent', true);

    // Only send notification once
    if ($notification_sent) {
      return;
    }

    // Translators: %d is the order ID.
    $notification_post = [
      'post_title'   => sprintf(__('Order #%d has shipped', 'user-notifications'), $order_id),
      'post_content' => '',
      'post_status'  => 'publish',
      'post_type'    => 'user_notification',
    ];

    $post_id = wp_insert_post($notification_post);

    if ($post_id) {
      // Store user id so it doesn't send the notification to everyone.
      update_post_meta($post_id, '_user_id', $user_id);

      // Add notification sent to order meta so it doesn't send more than once
      $order->update_meta_data('_notification_sent', current_time('timestamp'));
      $order->save();
    }
  }
  
}
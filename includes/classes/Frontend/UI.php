<?php

namespace DogByteMarketing\User_Notifications\Frontend;

use DogByteMarketing\User_Notifications\Notifications;
use DogByteMarketing\User_Notifications\Utils;

class UI {
  
  /**
   * Init
   *
   * @return void
   */
  public function init() {
    add_action('wp_footer', array($this, 'render_side_notification_bar'));
    add_action('wp_ajax_mark_notifications_read', array($this, 'mark_notifications_read'));
    add_action('wp_ajax_mark_notification_read', array($this, 'mark_notification_read'));
  }
  
  /**
   * Render side notification bar
   *
   * @return void
   */
  public function render_side_notification_bar() {
    $items = $this->get_panel_items();
    $unread_count = 0;

    foreach ($items as $item) {
      if ($item['unread']) {
        $unread_count++;
      }
    }
    ?>
    <div class="notification-sidebar-container">
      <div class="notification-sidebar">
        <div class="notification-panel__header">
          <div class="notification-panel__heading">
            <div class="notification-panel__title-row">
              <h2 class="notification-panel__title"><?php esc_html_e('Notifications', 'user-notifications'); ?></h2>
              <?php if ($unread_count) : ?>
                <span class="notification-panel__badge"><?php echo esc_html(sprintf(
                  /* translators: %d: number of unread notifications */
                  _n('%d new', '%d new', $unread_count, 'user-notifications'),
                  $unread_count
                )); ?></span>
              <?php endif; ?>
            </div>
            <p class="notification-panel__subtitle"><?php esc_html_e('Your latest updates and offers.', 'user-notifications'); ?></p>
          </div>
          <button type="button" class="close-notifications" aria-label="<?php esc_attr_e('Close notifications', 'user-notifications'); ?>">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
          </button>
        </div>
        <div class="notifications">
          <?php if ($items) : ?>
            <?php foreach ($items as $item) : ?>
              <?php $this->render_panel_item($item); ?>
            <?php endforeach; ?>
            <p class="notification-panel__end"><?php esc_html_e('No more notifications', 'user-notifications'); ?></p>
          <?php else : ?>
            <div class="no-notifications">
              <?php esc_html_e('You have no notifications', 'user-notifications'); ?>
            </div>
          <?php endif; ?>
        </div>
        <?php if ($items && is_user_logged_in()) : ?>
          <div class="notification-panel__footer">
            <button type="button" class="read-notifications"><?php esc_html_e('Mark All Read', 'user-notifications'); ?></button>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <?php
  }

  /**
   * Notifications that belong in the front-end panel.
   *
   * @return array
   */
  private function get_panel_items() {
    $current_user_id = get_current_user_id();
    $read_ids        = array();
    $items           = array();

    if ($current_user_id) {
      $stored   = get_user_meta($current_user_id, '_read_notifications', true);
      $read_ids = is_array($stored) ? array_map('intval', $stored) : array();
    }

    $notifications = get_posts(array(
      'post_type'      => 'user_notification',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
      'orderby'        => 'date',
      'order'          => 'DESC',
    ));

    foreach ($notifications as $notification) {
      $user_id = (int) get_post_meta($notification->ID, '_user_id', true);

      if ($user_id && $user_id !== (int) $current_user_id) {
        continue;
      }

      $items[] = array(
        'id'          => (int) $notification->ID,
        'title'       => $notification->post_title,
        'content'     => $notification->post_content,
        'time'        => $this->notification_time_ago($notification),
        'unread'      => !in_array((int) $notification->ID, $read_ids, true),
        'url'         => '',
        'external_id' => '',
      );
    }

    foreach (Notifications::get_visible_notifications('frontend') as $notification) {
      $items[] = array(
        'id'          => 0,
        'title'       => $notification['title'],
        'content'     => $notification['message'],
        'time'        => '',
        'unread'      => true,
        'url'         => $notification['url'],
        'external_id' => $notification['id'],
      );
    }

    return $items;
  }

  /**
   * One row in the notification panel.
   *
   * @param  array $item Panel item.
   * @return void
   */
  private function render_panel_item($item) {
    $classes = array('notification');

    if ($item['unread']) {
      $classes[] = 'is-unread';
    } else {
      $classes[] = 'is-read';
    }

    if ($item['external_id']) {
      $classes[] = 'user-notifications-external';
    }

    if (!$item['content']) {
      $classes[] = 'no-desc';
    }

    $can_mark_read = !empty($item['id']) && is_user_logged_in();
    ?>
    <div class="<?php echo esc_attr(implode(' ', $classes)); ?>"<?php echo $item['external_id'] ? ' data-id="' . esc_attr($item['external_id']) . '"' : ''; ?><?php echo $can_mark_read ? ' data-notification-id="' . esc_attr($item['id']) . '"' : ''; ?>>
      <div class="notification-preview">
        <?php if ($item['unread']) : ?>
          <span class="notification-item__dot" aria-hidden="true"></span>
        <?php endif; ?>
        <span class="notification-item__title">
          <?php if ($item['url']) : ?>
            <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
          <?php else : ?>
            <?php echo esc_html($item['title']); ?>
          <?php endif; ?>
        </span>
        <?php if ($item['time']) : ?>
          <span class="notification-item__time"><?php echo esc_html($item['time']); ?></span>
        <?php endif; ?>
        <span class="notification-item__actions">
          <?php if ($item['unread'] && $can_mark_read) : ?>
            <button type="button" class="notification-item__read" aria-label="<?php esc_attr_e('Mark as read', 'user-notifications'); ?>">
              <i class="fa-solid fa-eye" aria-hidden="true"></i>
            </button>
          <?php endif; ?>
          <?php if ($item['content']) : ?>
            <i class="fa-solid fa-chevron-down notification-item__chevron" aria-hidden="true"></i>
          <?php endif; ?>
        </span>
      </div>
      <?php if ($item['content']) : ?>
        <div class="notification-full"><?php echo wp_kses_post($item['content']); ?></div>
      <?php endif; ?>
      <?php if ($item['external_id']) : ?>
        <button type="button" class="user-notifications-dismiss"><?php esc_html_e('Dismiss', 'user-notifications'); ?></button>
      <?php endif; ?>
    </div>
    <?php
  }

  /**
   * Short relative time for a notification row.
   *
   * @param  WP_Post $notification Notification post.
   * @return string
   */
  private function notification_time_ago($notification) {
    $timestamp = get_post_timestamp($notification);

    return Utils::time_ago($timestamp);
  }
  
  /**
   * Ajax method for logged in users to mark notifications read
   *
   * @return void
   */
  public function mark_notifications_read() {
    // Ensure it's a valid request with nonce verification
    $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

    if (!wp_verify_nonce($nonce, 'user-notifications-nonce')) {
      Utils::send_error(__('Authentication failed.', 'user-notifications'), 401);
    }

    $current_user    = wp_get_current_user();
    $current_user_id = $current_user->ID;

    if (!$current_user_id) {
      Utils::send_error(__('Invalid user or password.', 'user-notifications'), 401);
    }

    Utils::mark_user_notifications_read($current_user_id);

    Utils::send_success(__('Your notifications have been marked as read.', 'user-notifications'));
  }

  /**
   * Ajax method for logged in users to mark one notification read
   *
   * @return void
   */
  public function mark_notification_read() {
    $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

    if (!wp_verify_nonce($nonce, 'user-notifications-nonce')) {
      Utils::send_error(__('Authentication failed.', 'user-notifications'), 401);
    }

    $current_user_id = get_current_user_id();

    if (!$current_user_id) {
      Utils::send_error(__('Invalid user or password.', 'user-notifications'), 401);
    }

    $notification_id = isset($_POST['id']) ? absint(wp_unslash($_POST['id'])) : 0;
    $notification    = get_post($notification_id);

    if (!$notification || 'user_notification' !== $notification->post_type || 'publish' !== $notification->post_status) {
      Utils::send_error(__('Notification not found.', 'user-notifications'), 404);
    }

    $owner_id = (int) get_post_meta($notification_id, '_user_id', true);

    if ($owner_id && $owner_id !== (int) $current_user_id) {
      Utils::send_error(__('Notification not found.', 'user-notifications'), 404);
    }

    Utils::mark_user_notification_read($current_user_id, $notification_id);

    Utils::send_success(__('Notification marked as read.', 'user-notifications'));
  }
  
}
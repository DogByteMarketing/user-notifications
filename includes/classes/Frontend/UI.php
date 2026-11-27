<?php

namespace DogByteMarketing\User_Notifications\Frontend;

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
  }
  
  /**
   * Render side notification bar
   *
   * @return void
   */
  public function render_side_notification_bar() {
    $current_user_id = get_current_user_id();
    ?>
    <!-- <div class="notification-sidebar-container"> -->
    <div class="notification-sidebar-container" style="display: none;">
      <div class="notification-sidebar">
        <div class="actions">
          <div class="col-6">
            <?php if (Utils::get_option('privacy', 'read_tracking') && is_user_logged_in()) : ?>
              <a href="javascript:void(0);" class="read-notifications">
                MARK ALL READ
              </a>
            <?php endif; ?>
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
            'post_type'      => 'user_notification',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
          ));
          ?>
          <?php foreach($notifications as $notification) : ?>
            <?php
            $notification_id = $notification->ID;
            $user_id = get_post_meta($notification_id, '_user_id', true);
            ?>
            <?php if (!$user_id || $user_id == $current_user_id) : ?>
              <div class="notification">
                <div class="notification-preview-container">
                  <div class="notification-preview<?php echo !$notification->post_content ? ' no-desc' : ''; ?>">
                    <div class="col-<?php echo $notification->post_content ? '11' : '12'; ?>"><?php echo esc_html($notification->post_title); ?></div>
                    <?php if ($notification->post_content) : ?>
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
                    <?php endif; ?>
                  </div>
                </div>
                <?php if ($notification->post_content) : ?>
                  <div class="notification-full" style="display: none;">
                    <?php echo wp_kses_post($notification->post_content); ?>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php
  }
  
  /**
   * Ajax method for logged in users to mark notifications read
   *
   * @return void
   */
  public function mark_notifications_read() {
    // Ensure it's a valid request with nonce verification
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'user-notifications-nonce')) {
      Utils::send_error('Authentication failed.', 401);
    }

    $current_user    = wp_get_current_user();
    $current_user_id = $current_user->ID;

    if (!$current_user_id) {
      Utils::send_error('Invalid user or password.', 401);
    }

    Utils::mark_user_notifications_read($current_user_id);

    Utils::send_success('Your notifications have been marked as read.');
  }
  
}
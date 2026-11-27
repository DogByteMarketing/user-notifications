<?php

namespace DogByteMarketing\User_Notifications\Frontend;

class UI {
  
  /**
   * Init
   *
   * @return void
   */
  public function init() {
    add_action('wp_footer', array($this, 'render_side_notification_bar'));
  }
  
  /**
   * Render side notification bar
   *
   * @return void
   */
  public function render_side_notification_bar() {
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
            'post_type'      => 'user_notification',
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
  
}
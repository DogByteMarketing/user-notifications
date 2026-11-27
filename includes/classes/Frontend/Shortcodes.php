<?php

namespace DogByteMarketing\User_Notifications\Frontend;

use DogByteMarketing\User_Notifications\Notifications;
use DogByteMarketing\User_Notifications\Utils;

class Shortcodes {
  
  /**
   * Init
   *
   * @return void
   */
  public function init() {
    add_shortcode('notifications_dogbytemarketing', array($this, 'add_notifications_shortcode'));
    add_filter('wp_nav_menu_objects', array($this, 'menu_shortcode'));
  }
    
  /**
   * Add notifications shortcode
   *
   * @param  array $atts The shortcode attributes
   * @return void
   */
  public function add_notifications_shortcode($atts) {
		$atts = shortcode_atts(
			array(
				'class'           => '',
				'class_container' => '',
			),
			$atts,
			'notifications_dogbytemarketing'
		);

		ob_start();
		
		$this->render_notifications($atts);

		$output = ob_get_clean();

    return $output;
  }
  
  
  /**
   * Searches for the shortcode in the menu and does the shortcode if exists
   *
   * @param  array $items The menu items
   * @return void
   */
  public function menu_shortcode($items) {
    foreach ($items as $item) {
      if (str_contains($item->title, '[notifications_dogbytemarketing')) {
        $item->title = do_shortcode($item->title);
      }
    }

    return $items;
  }
  
  /**
   * Render notifications within shortcode
   *
   * @param  array $atts The shortcode attributes
   * @return void
   */
  private function render_notifications($atts) {
    $current_user_id    = get_current_user_id();
    $notification_count = Utils::get_user_notification_count($current_user_id);
    $notification_count += count(Notifications::get_visible_notifications('frontend'));
    $class              = isset($atts['class']) ? sanitize_text_field($atts['class']) : '';
    $class_container    = isset($atts['class_container']) ? sanitize_text_field($atts['class_container']) : '';

    ?>
    <a href="javascript:void(0)" class="<?php echo $class_container ? esc_attr($class_container) : ''; ?> notification-container">
      <i class="<?php echo $class ? esc_attr($class) : 'bi bi-bell-fill'; ?> notification-icon" aria-hidden="true"></i>
      <!-- This is coming soon -->
      <?php if ($notification_count > 0) : ?>
        <span class="notification-bubble"><?php echo esc_html($notification_count); ?></span>
      <?php endif; ?>
    </a>
    <?php
  }
  
}
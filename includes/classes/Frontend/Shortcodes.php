<?php

namespace DogByteMarketing\User_Notifications\Frontend;

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
				'class' => '',
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
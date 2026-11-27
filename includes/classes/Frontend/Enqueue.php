<?php

namespace DogByteMarketing\User_Notifications\Frontend;

use DogByteMarketing\User_Notifications\Utils;

class Enqueue {

  /**
	 * Full path and filename of plugin.
	 *
	 * @var string $version Full path and filename of plugin.
	 */
  private $plugin;

	/**
	 * The version of this plugin.
	 *
	 * @var string $version The current version of this plugin.
	 */
	private $version;
  
  /**
   * __construct
   *
   * @return void
   */
  public function __construct($plugin, $version) {
    $this->plugin  = $plugin;
    $this->version = $version;
  }
  
  /**
   * Init
   *
   * @return void
   */
  public function init() {
    add_action('wp_enqueue_scripts', array($this, 'enqueue'));
  }
  
  /**
   * Enqueue scripts and styles
   *
   * @return void
   */
  public function enqueue() {
    $this->enqueue_styles();
    $this->enqueue_scripts();
  }
  
  /**
   * Enqueue styles
   *
   * @return void
   */
  private function enqueue_styles() {
    $bootstrap_icons = Utils::get_option('general', 'bootstrap_icons');
    
		wp_enqueue_style('user-notifications', plugins_url('/css/frontend/style.css', $this->plugin), array(), $this->version);
    
    if ($bootstrap_icons) {
      wp_enqueue_style('bootstrap-icons', plugins_url('/css/bootstrap-icons.min.css', $this->plugin), array(), $this->version);
    }
  }
  
  /**
   * Enqueue scripts
   *
   * @return void
   */
  private function enqueue_scripts() {
    wp_enqueue_script('user-notifications', plugins_url('/js/frontend/main.js', $this->plugin), array('jquery'), $this->version, true);
    wp_localize_script(
      'user-notifications',
      'user_notifications_object',
      array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('user-notifications-nonce'),
      )
    );
  }
  
}
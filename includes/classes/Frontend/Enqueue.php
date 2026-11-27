<?php

namespace DogByteMarketing\User_Notifications\Frontend;

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
    $this->enqueue();
  }
  
  /**
   * Enqueue scripts and styles
   *
   * @return void
   */
  public function enqueue() {
		wp_enqueue_style('user-notifications', plugins_url('/css/frontend/style.css', $this->plugin), array(), $this->version);
    wp_enqueue_script('user-notifications', plugins_url('/js/frontend/main.js', $this->plugin), array('jquery'), $this->version, true);
  }

  
}
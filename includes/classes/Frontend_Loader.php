<?php

namespace DogByteMarketing\User_Notifications;

use DogByteMarketing\User_Notifications\Frontend\Enqueue;
use DogByteMarketing\User_Notifications\Frontend\UI;
use DogByteMarketing\User_Notifications\Frontend\Custom_Post_Type;
use DogByteMarketing\User_Notifications\Frontend\Shortcodes;

class Frontend_Loader {

  /**
	 * Full path and filename of plugin.
	 *
	 * @var string $version Full path and filename of plugin.
	 */
  private $plugin;

	/**
	 * The version of this plugin.
	 *
	 * @var   string $version The current version of this plugin.
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
    $this->load_enqueue();
    $this->load_ui();
    $this->load_custom_post_type();
    $this->load_shortcodes();
  }
  
  /**
   * Load UI
   *
   * @return void
   */
  public function load_enqueue() {
    $gui = new Enqueue($this->plugin, $this->version);
    $gui->init();
  }
  
  /**
   * Load UI
   *
   * @return void
   */
  public function load_ui() {
    $gui = new UI();
    $gui->init();
  }
  
  /**
   * Load Custom Post Type
   *
   * @return void
   */
  public function load_custom_post_type() {
    $custom_post_type = new Custom_Post_Type();
    $custom_post_type->init();
  }
  
  /**
   * Load Shortcodes
   *
   * @return void
   */
  public function load_shortcodes() {
    $shortcodes = new Shortcodes();
    $shortcodes->init();
  }

}
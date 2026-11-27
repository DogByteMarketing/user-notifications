<?php

namespace DogByteMarketing\User_Notifications;

use DogByteMarketing\User_Notifications\Frontend\Enqueue;
use DogByteMarketing\User_Notifications\Frontend\UI;
use DogByteMarketing\User_Notifications\Frontend\WooCommerce;
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
    $enabled = Utils::get_option('general', 'enabled');

    if ($enabled) {
      $this->load_enqueue();
      $this->load_ui();
      $this->load_woocommerce();
      $this->load_shortcodes();
    }
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
   * Load WooCommerce
   *
   * @return void
   */
  public function load_woocommerce() {
    $woocommerce = new WooCommerce();
    $woocommerce->init();
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
<?php

namespace DogByteMarketing\User_Notifications;

use DogByteMarketing\User_Notifications\Backend\Admin;
use DogByteMarketing\User_Notifications\Backend\Admin_Bar;
use DogByteMarketing\User_Notifications\Backend\Enqueue;
use DogByteMarketing\User_Notifications\Frontend\Custom_Post_Type;

class Backend_Loader {

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
    $this->load_admin();
    $this->load_admin_bar();
    $this->load_custom_post_type();
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
   * Load Admin
   *
   * @return void
   */
  public function load_admin() {
    $admin = new Admin($this->plugin, $this->version);
    $admin->init();
  }

  /**
   * Load the admin toolbar bell
   *
   * @return void
   */
  public function load_admin_bar() {
    $admin_bar = new Admin_Bar($this->plugin, $this->version);
    $admin_bar->init();
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

}
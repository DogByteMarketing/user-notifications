<?php

namespace DogByteMarketing\User_Notifications\Backend;

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
    add_action('admin_enqueue_scripts', array($this, 'enqueue'));
  }
  
  /**
   * Enqueue scripts and styles
   *
   * @return void
   */
  public function enqueue($hook_suffix) {
    $is_settings = ($hook_suffix === 'user_notification_page_user-notifications');
    $is_plugins  = ($hook_suffix === 'plugins.php');

    if (!$is_settings && !$is_plugins) {
      return;
    }

    wp_enqueue_style('user-notifications-settings', plugins_url('/assets/css/backend/settings.css', $this->plugin), array(), $this->version);

    if (!$is_settings) {
      return;
    }

    wp_enqueue_style('sweetalert2', plugins_url('/assets/css/backend/sweetalert2.min.css', $this->plugin), array(), $this->version);
    wp_enqueue_script('sweetalert2', plugins_url('/assets/js/backend/sweetalert2.all.js', $this->plugin), array('jquery'), $this->version, true);
    wp_enqueue_script('user-notifications-settings', plugins_url('/assets/js/backend/settings.js', $this->plugin), array('jquery', 'sweetalert2', 'wp-i18n'), $this->version, true);
    wp_set_script_translations('user-notifications-settings', 'user-notifications', plugin_dir_path($this->plugin) . '/languages/');
  }

}
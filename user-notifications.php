<?php

/**
 * Plugin Name: User Notifications
 * Description: Send notifications to your users.
 * Version: 1.0.4
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * Author: Dog Byte Marketing
 * Author URI: https://www.dogbytemarketing.com
 * Plugin URI: https://www.dogbytemarketing.com/contact/
 * Text Domain: user-notifications
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

namespace DogByteMarketing\User_Notifications;

if (!defined('ABSPATH')) exit;

require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * Increase cSAT
 * Send marketing messages to logged in and guest users
 * Easily notify users of changes
 */

/**
 * To-Do
 * 
 * Add admin settings for design and colors and icon classes
 * 
 * Add notification count
 * Add order status updates as notifications to users
 * Store read messages via the users account (add a snippet about updating their GDPR notice)
 * Implement an option to only show future notifications to new users
 * Implement an option for Shortcode, and a fixed icon injection
 * Implement an option for auto-delete last X days
 * Implement option in CPT to set who to show to
 * Implement option in CPT to have expiration date
 * Add tracking for opens / clicks
 * Add hooks for other plugin developers to use
 * Add ability to enable various options dependant on GDPR like including Font-Awesome locally if icon don't load
 * 
 * Add we want to hear your ideas on plugin page
 * 
 */

class User_Notifications
{

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
  public function __construct() {
    $this->plugin  = __FILE__;
    $this->version = $this->get_plugin_version();
  }
  
  /**
   * Init
   *
   * @return void
   */
  public function init() {
    $this->load_dependencies();
  }
  
  /**
   * Load dependencies
   *
   * @return void
   */
  public function load_dependencies() {
    $dependency_loader = new Dependency_Loader($this->plugin, $this->version);
    $dependency_loader->init();
  }
  
  /**
   * Get the plugin version
   *
   * @return string $version The plugin version
   */
  private function get_plugin_version() {
    $plugin_data = get_file_data($this->plugin, array('Version' => 'Version'), false);
    $version     = $plugin_data['Version'];

    return $version;
  }

}

$user_notifications = new User_Notifications;
$user_notifications->init();
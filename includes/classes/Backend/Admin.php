<?php

namespace DogByteMarketing\User_Notifications\Backend;

use DogByteMarketing\User_Notifications\Utils;

class Admin {
  
  /**
   * Init
   *
   * @return void
   */
  public function init() {
		add_action('admin_menu', array($this, 'add_admin_menu'));
		add_action('admin_init', array($this, 'settings_init'));
  }

  /**
	 * Add admin menu to backend
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		add_submenu_page('edit.php?post_type=user_notification', __('User Notification Settings', 'user-notifications'), __('Settings', 'user-notifications'), 'manage_options', 'user-notifications', array($this, 'options_page'));
	}
  
	/**
	 * Initialize Settings
	 *
	 * @return void
	 */
	public function settings_init() {
    $is_first_run = get_option('user_notifications_settings_dogbytemarketing');

    // Check if first run and set empty options so the first save isn't empty
    if (!$is_first_run) {
      update_option('user_notifications_settings_dogbytemarketing', array());
    }

    // Register the setting page
    register_setting(
      'user_notifications_dogbytemarketing',          // Option group
      'user_notifications_settings_dogbytemarketing', // Option name
      array($this, 'sanitize')
    );

    // Add a section to assign an ID for admin.js to target switching between tabs
    add_settings_section(
      'user_notifications_general_section_dogbytemarketing',
      '',
      null,
      'user_notifications_general_dogbytemarketing'
    );

    add_settings_section(
      'user_notifications_woocommerce_section_dogbytemarketing',
      '',
      null,
      'user_notifications_woocommerce_dogbytemarketing'
    );

    add_settings_section(
      'user_notifications_privacy_section_dogbytemarketing',
      '',
      null,
      'user_notifications_privacy_dogbytemarketing'
    );

    // Add a setting under general section
		add_settings_field(
			'enabled',                                            // Setting Id
			__('Enabled?', 'user-notifications'),                 // Setting Label
			array($this, 'enabled_render'),                       // Setting callback
			'user_notifications_general_dogbytemarketing',        // Setting page
			'user_notifications_general_section_dogbytemarketing' // Setting section
		);

		add_settings_field(
			'disable_no_scroll',
			__('Disable No Scroll?', 'user-notifications'),
			array($this, 'disable_no_scroll_render'),
			'user_notifications_general_dogbytemarketing',
			'user_notifications_general_section_dogbytemarketing'
		);

		add_settings_field(
			'bootstrap_icons',
			__('BootStrap Icons', 'user-notifications'),
			array($this, 'bootstrap_icons_render'),
			'user_notifications_general_dogbytemarketing',
			'user_notifications_general_section_dogbytemarketing'
		);

		add_settings_field(
			'order_notifications',
			__('Order Notifications', 'user-notifications'),
			array($this, 'order_notifications_render'),
			'user_notifications_woocommerce_dogbytemarketing',
			'user_notifications_woocommerce_section_dogbytemarketing'
		);

		add_settings_field(
			'read_tracking',
			__('Read Tracking', 'user-notifications'),
			array($this, 'read_tracking_render'),
			'user_notifications_privacy_dogbytemarketing',
			'user_notifications_privacy_section_dogbytemarketing'
		);

		add_settings_field(
			'user_targeting',
			__('User Targeting', 'user-notifications'),
			array($this, 'user_targeting_render'),
			'user_notifications_privacy_dogbytemarketing',
			'user_notifications_privacy_section_dogbytemarketing'
		);
	}

  /**
	 * Render Enabled Field
	 *
	 * @return void
	 */
	public function enabled_render() {
    $option = Utils::get_option('general', 'enabled');
    ?>
    <div class="form-check form-switch">
      <input type="checkbox" name="user_notifications_settings_dogbytemarketing[general][enabled]" class="form-check-input" id="enabled" role="switch" <?php checked(1, $option, true); ?> /> <?php echo __('Yes', 'user-notifications'); ?>
    </div>
		<?php
	}

  /**
	 * Render Enabled Field
	 *
	 * @return void
	 */
	public function disable_no_scroll_render() {
    $option = Utils::get_option('general', 'disable_no_scroll');
    ?>
    <div class="form-check form-switch">
      <input type="checkbox" name="user_notifications_settings_dogbytemarketing[general][disable_no_scroll]" class="form-check-input" id="disable_no_scroll" role="switch" <?php checked(1, $option, true); ?> /> <?php echo __('Yes', 'user-notifications'); ?>
    </div>
    <p><strong><?php echo __('This will disable adding the no-scroll class after the notification icon is clicked as this could conflict with some themes.', 'user-notifications'); ?></strong></p>
		<?php
	}

  /**
	 * Render BootStrap Icons Field
	 *
	 * @return void
	 */
	public function bootstrap_icons_render() {
    $option = Utils::get_option('general', 'bootstrap_icons');
    ?>
    <div class="form-check form-switch">
      <input type="checkbox" name="user_notifications_settings_dogbytemarketing[general][bootstrap_icons]" class="form-check-input" id="bootstrap_icons" role="switch" <?php checked(1, $option, true); ?> /> <?php echo __('Yes', 'user-notifications'); ?>
    </div>
    <p><strong><?php echo __('If you use another icon set or already use BootStrap Icons, disable this option.', 'user-notifications'); ?></strong></p>
		<?php
	}

  /**
	 * Render BootStrap Icons Field
	 *
	 * @return void
	 */
	public function order_notifications_render() {
    $option = Utils::get_option('woocommerce', 'order_notifications');
    ?>
    <div class="form-check form-switch">
      <input type="checkbox" name="user_notifications_settings_dogbytemarketing[woocommerce][order_notifications]" class="form-check-input" id="order_notifications" role="switch" <?php checked(1, $option, true); ?> /> <?php echo __('Yes', 'user-notifications'); ?>
    </div>
    <p><strong><?php echo __('Send notifications on order status changes.', 'user-notifications'); ?></strong></p>
		<?php
	}

  /**
	 * Render BootStrap Icons Field
	 *
	 * @return void
	 */
	public function read_tracking_render() {
    $option = Utils::get_option('privacy', 'read_tracking');
    ?>
    <div class="form-check form-switch">
      <input type="checkbox" name="user_notifications_settings_dogbytemarketing[privacy][read_tracking]" class="form-check-input" id="read_tracking" role="switch" <?php checked(1, $option, true); ?> /> <?php echo __('Yes', 'user-notifications'); ?>
    </div>
    <p><strong><?php echo __("This allows users to mark notifications as read. This will add the notification IDs to a list of IDs that have been read in the user's meta.", 'user-notifications'); ?></strong></p>
		<?php
	}

  /**
	 * Render BootStrap Icons Field
	 *
	 * @return void
	 */
	public function user_targeting_render() {
    $option = Utils::get_option('privacy', 'user_targeting');
    ?>
    <div class="form-check form-switch">
      <input type="checkbox" name="user_notifications_settings_dogbytemarketing[privacy][user_targeting]" class="form-check-input" id="user_targeting" role="switch" <?php checked(1, $option, true); ?> /> <?php echo __('Yes', 'user-notifications'); ?>
    </div>
    <p><strong><?php echo __('This will allow you to send notifications to specific users. This processes existing user data to fetch their ID and username.', 'user-notifications'); ?></strong></p>
		<?php
	}
	
	/**
	 * Render options page
	 *
	 * @return void
	 */
	public function options_page() {
    ?>
    <form action='options.php' method='post'>
      <div class="bootstrap-wrapper">
        <div class="container">
          <div class="row">
            <div class="col-3"></div>
            <div class="col-6">
              <h1><?php echo __('User Notifications Settings', 'user-notifications'); ?></h1>
            </div>
            <div class="col-3"></div>
          </div>
          <div class="row">
            <div class="nav-links col-12 col-md-6 col-xl-3">
              <ul>
                <li>
                  <a href="javascript:void(0);" class="active" data-section="general">
                    <i class="bi bi-gear-fill"></i>
                    <?php echo __('General', 'user-notifications'); ?>
                  </a>
                </li>
                <li>
                  <a href="javascript:void(0);" data-section="woocommerce">
                    <i class="bi bi-cart-fill"></i>
                    <?php echo __('WooCommerce', 'user-notifications'); ?>
                  </a>
                </li>
                <li>
                  <a href="javascript:void(0);" data-section="privacy">
                    <i class="bi bi-shield-fill"></i>
                    <?php echo __('Privacy', 'user-notifications'); ?>
                  </a>
                </li>
              </ul>
            </div>
            <div class="tabs col-12 col-md-6 col-xl-6">
              <div class="tab general">
                <?php
                do_settings_sections('user_notifications_general_dogbytemarketing');
                ?>
              </div>

              <div class="tab woocommerce" style="display: none;">
                <?php
                do_settings_sections('user_notifications_woocommerce_dogbytemarketing');
                ?>
              </div>

              <div class="tab privacy" style="display: none;">
                <div class="privacy-notice"><?php echo __('These options collect data from guests/users. Ensure you are in compliance with data protection regulations before activation. Proceed at your own risk.', 'user-notifications'); ?></div>
                <?php
                do_settings_sections('user_notifications_privacy_dogbytemarketing');
                ?>
              </div>
            
              <?php
              settings_fields('user_notifications_dogbytemarketing');
              submit_button();
              ?>
              
            </div>

            <div class="ctas col-12 col-md-12 col-xl-3">
              <div class="cta">
                <h2 style="color: #fff;">Something Not Working?</h2>
                <p>We pride ourselves on quality, so if something isn't working or you have a suggestion, feel free to call or email us. We're based out of Tennessee in the USA.
                <p><a href="tel:+14237248922" class="button button-primary" style="text-decoration: none; color: #fff; font-weight: 700; text-transform: uppercase; background-color: #333; border-color: #333;" target="_blank">Call Us</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.dogbytemarketing.com/contact/" class="button button-primary" style="text-decoration: none; color: #fff; font-weight: 700; text-transform: uppercase; background-color: #333; border-color: #333;" target="_blank">Email Us</a></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  <?php
  }

  /**
   * Sanitize Options
   *
   * @param  array $input Array of option inputs
   * @return array $sanitary_values Array of sanitized options
   */
  public function sanitize($input) {
		$sanitary_values = array();

    if (isset($input['general']['enabled']) && $input['general']['enabled']) {
      $sanitary_values['general']['enabled'] = $input['general']['enabled'] === 'on' ? true : false;
    } else {
      $sanitary_values['general']['enabled'] = false;
    }

    if (isset($input['general']['disable_no_scroll']) && $input['general']['disable_no_scroll']) {
      $sanitary_values['general']['disable_no_scroll'] = $input['general']['disable_no_scroll'] === 'on' ? true : false;
    } else {
      $sanitary_values['general']['disable_no_scroll'] = false;
    }

    if (isset($input['general']['bootstrap']) && $input['general']['bootstrap']) {
      $sanitary_values['general']['bootstrap'] = $input['general']['bootstrap'] === 'on' ? true : false;
    } else {
      $sanitary_values['general']['bootstrap'] = false;
    }

    if (isset($input['general']['bootstrap_icons']) && $input['general']['bootstrap_icons']) {
      $sanitary_values['general']['bootstrap_icons'] = $input['general']['bootstrap_icons'] === 'on' ? true : false;
    } else {
      $sanitary_values['general']['bootstrap_icons'] = false;
    }

    if (isset($input['woocommerce']['order_notifications']) && $input['woocommerce']['order_notifications']) {
      $sanitary_values['woocommerce']['order_notifications'] = $input['woocommerce']['order_notifications'] === 'on' ? true : false;
    } else {
      $sanitary_values['woocommerce']['order_notifications'] = false;
    }

    if (isset($input['privacy']['user_targeting']) && $input['privacy']['user_targeting']) {
      $sanitary_values['privacy']['user_targeting'] = $input['privacy']['user_targeting'] === 'on' ? true : false;
    } else {
      $sanitary_values['privacy']['user_targeting'] = false;
    }

    if (isset($input['privacy']['read_tracking']) && $input['privacy']['read_tracking']) {
      $sanitary_values['privacy']['read_tracking'] = $input['privacy']['read_tracking'] === 'on' ? true : false;
    } else {
      $sanitary_values['privacy']['read_tracking'] = false;
    }

    return $sanitary_values;
  }

}
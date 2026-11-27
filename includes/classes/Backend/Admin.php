<?php

namespace DogByteMarketing\User_Notifications\Backend;

use DogByteMarketing\User_Notifications\Utils;

class Admin {

  /**
   * Full path and filename of plugin.
   *
   * @var string $plugin Full path and filename of plugin.
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
   * @param string $plugin  Full path and filename of plugin.
   * @param string $version The current version of this plugin.
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
    add_action('admin_menu', array($this, 'add_admin_menu'));
    add_action('admin_init', array($this, 'settings_init'));
    add_filter('plugin_action_links_' . plugin_basename($this->plugin), array($this, 'plugin_action_links'));
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
   * Add Settings and Support links on the Plugins screen.
   *
   * @param  array $links Plugin action links.
   * @return array
   */
  public function plugin_action_links($links) {
    $settings_url = admin_url('edit.php?post_type=user_notification&page=user-notifications');
    $support_url  = admin_url('edit.php?post_type=user_notification&page=user-notifications#support');
    $settings_link = '<a class="user-notifications-plugin-settings" href="' . esc_url($settings_url) . '">' . esc_html__('Settings', 'user-notifications') . '</a>';
    $support_link  = '<a class="user-notifications-plugin-support" href="' . esc_url($support_url) . '">' . esc_html__('Support', 'user-notifications') . '</a>';

    array_unshift($links, $settings_link, $support_link);

    return $links;
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

    register_setting(
      'user_notifications_dogbytemarketing',
      'user_notifications_settings_dogbytemarketing',
      array($this, 'sanitize')
    );
  }

  /**
   * Render a setting card with the existing option value.
   *
   * @param  string $id      Field id.
   * @param  string $name    Option input name.
   * @param  string $label   Visible label.
   * @param  string $section Stored section.
   * @param  string $key     Stored key.
   * @param  string $help    Optional description.
   * @return void
   */
  private function render_setting_card($id, $name, $label, $section, $key, $help = '') {
    $option = Utils::get_option($section, $key);
    ?>
    <article class="user-notifications-general__card">
      <div class="user-notifications-general__card-head">
        <div>
          <h3 class="user-notifications-general__card-title"><?php echo esc_html($label); ?></h3>
          <?php if ($help) : ?>
            <p class="user-notifications-general__help"><?php echo esc_html($help); ?></p>
          <?php endif; ?>
        </div>
        <label class="user-notifications-toggle" for="<?php echo esc_attr($id); ?>">
          <input type="checkbox" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($id); ?>" <?php checked(1, $option, true); ?> />
          <span class="user-notifications-toggle__switch" aria-hidden="true"></span>
          <span class="screen-reader-text"><?php echo esc_html($label); ?></span>
        </label>
      </div>
    </article>
    <?php
  }

  /**
   * Render the support tab.
   *
   * @return void
   */
  public function support_panel_render() {
    $plugin_version    = $this->version;
    $wordpress_version = get_bloginfo('version');
    $php_version       = PHP_VERSION;
    $copy_text         = 'User Notifications version: ' . $plugin_version . "\n" . 'WordPress version: ' . $wordpress_version . "\n" . 'PHP version: ' . $php_version;
    $forum_url         = 'https://wordpress.org/support/plugin/user-notifications/';
    $new_topic_url     = 'https://wordpress.org/support/plugin/user-notifications/#new-post';
    $review_url        = 'https://wordpress.org/support/plugin/user-notifications/reviews/#new-post';
    ?>
    <div class="user-notifications-support">
      <h2 class="user-notifications-support__title"><?php esc_html_e('How can we help?', 'user-notifications'); ?></h2>
      <p class="user-notifications-support__intro"><?php esc_html_e('Get help with User Notifications, report issues, or share your feedback.', 'user-notifications'); ?></p>
      <div class="user-notifications-support__cards">
        <article class="user-notifications-support__card">
          <h3 class="user-notifications-support__card-title"><span class="dashicons dashicons-sos" aria-hidden="true"></span><?php esc_html_e('Get Support', 'user-notifications'); ?></h3>
          <p class="user-notifications-support__card-text"><?php esc_html_e('Visit the WordPress support forum to ask questions or get help.', 'user-notifications'); ?></p>
          <a class="user-notifications-support__button" href="<?php echo esc_url($forum_url); ?>" target="_blank" rel="noopener noreferrer">
            <?php esc_html_e('Visit Support Forum', 'user-notifications'); ?>
          </a>
        </article>
        <article class="user-notifications-support__card">
          <h3 class="user-notifications-support__card-title"><span class="dashicons dashicons-warning" aria-hidden="true"></span><?php esc_html_e('Report a Bug', 'user-notifications'); ?></h3>
          <p class="user-notifications-support__card-text"><?php esc_html_e('Found something that isn\'t working as expected? Let us know.', 'user-notifications'); ?></p>
          <a class="user-notifications-support__button" href="<?php echo esc_url($new_topic_url); ?>" target="_blank" rel="noopener noreferrer">
            <?php esc_html_e('Report an Issue', 'user-notifications'); ?>
          </a>
        </article>
        <article class="user-notifications-support__card">
          <h3 class="user-notifications-support__card-title"><span class="dashicons dashicons-lightbulb" aria-hidden="true"></span><?php esc_html_e('Request a Feature', 'user-notifications'); ?></h3>
          <p class="user-notifications-support__card-text"><?php esc_html_e('Have an idea to make User Notifications better?', 'user-notifications'); ?></p>
          <a class="user-notifications-support__button" href="<?php echo esc_url($new_topic_url); ?>" target="_blank" rel="noopener noreferrer">
            <?php esc_html_e('Share an Idea', 'user-notifications'); ?>
          </a>
        </article>
      </div>
      <div class="user-notifications-support__lower">
        <article class="user-notifications-support__card">
          <h3 class="user-notifications-support__card-title"><span class="dashicons dashicons-editor-ul" aria-hidden="true"></span><?php esc_html_e('System Information', 'user-notifications'); ?></h3>
          <dl class="user-notifications-support__info">
            <div class="user-notifications-support__row">
              <dt><?php esc_html_e('User Notifications version', 'user-notifications'); ?></dt>
              <dd><?php echo esc_html($plugin_version); ?></dd>
            </div>
            <div class="user-notifications-support__row">
              <dt><?php esc_html_e('WordPress version', 'user-notifications'); ?></dt>
              <dd><?php echo esc_html($wordpress_version); ?></dd>
            </div>
            <div class="user-notifications-support__row">
              <dt><?php esc_html_e('PHP version', 'user-notifications'); ?></dt>
              <dd><?php echo esc_html($php_version); ?></dd>
            </div>
          </dl>
          <button type="button" class="user-notifications-support__button user-notifications-support__button--ghost user-notifications-support__copy" data-copy="<?php echo esc_attr($copy_text); ?>" data-copied="<?php esc_attr_e('Copied', 'user-notifications'); ?>">
            <span class="user-notifications-support__copy-label"><?php esc_html_e('Copy System Info', 'user-notifications'); ?></span>
          </button>
        </article>
        <article class="user-notifications-support__card user-notifications-support__card--review">
          <h3 class="user-notifications-support__card-title"><span class="dashicons dashicons-heart" aria-hidden="true"></span><?php esc_html_e('Enjoying User Notifications?', 'user-notifications'); ?></h3>
          <div class="user-notifications-support__stars" role="group" aria-label="<?php esc_attr_e('Rate User Notifications', 'user-notifications'); ?>">
            <button type="button" class="user-notifications-support__star dashicons dashicons-star-empty" data-rating="1" aria-haspopup="dialog">
              <span class="screen-reader-text"><?php esc_html_e('1 star', 'user-notifications'); ?></span>
            </button>
            <button type="button" class="user-notifications-support__star dashicons dashicons-star-empty" data-rating="2" aria-haspopup="dialog">
              <span class="screen-reader-text"><?php esc_html_e('2 stars', 'user-notifications'); ?></span>
            </button>
            <button type="button" class="user-notifications-support__star dashicons dashicons-star-empty" data-rating="3" aria-haspopup="dialog">
              <span class="screen-reader-text"><?php esc_html_e('3 stars', 'user-notifications'); ?></span>
            </button>
            <a class="user-notifications-support__star dashicons dashicons-star-empty" data-rating="4" href="<?php echo esc_url($review_url); ?>" target="_blank" rel="noopener noreferrer">
              <span class="screen-reader-text"><?php esc_html_e('4 stars', 'user-notifications'); ?></span>
            </a>
            <a class="user-notifications-support__star dashicons dashicons-star-empty" data-rating="5" href="<?php echo esc_url($review_url); ?>" target="_blank" rel="noopener noreferrer">
              <span class="screen-reader-text"><?php esc_html_e('5 stars', 'user-notifications'); ?></span>
            </a>
          </div>
          <a class="user-notifications-support__button" href="<?php echo esc_url($review_url); ?>" target="_blank" rel="noopener noreferrer">
            <?php esc_html_e('Leave a Review', 'user-notifications'); ?>
          </a>
        </article>
      </div>
      <dialog class="user-notifications-review-prompt" aria-labelledby="user-notifications-review-prompt-title" aria-describedby="user-notifications-review-prompt-text">
        <h2 class="user-notifications-review-prompt__title" id="user-notifications-review-prompt-title"><?php esc_html_e('We strive to provide top-notch plugins', 'user-notifications'); ?></h2>
        <p class="user-notifications-review-prompt__text" id="user-notifications-review-prompt-text"><?php esc_html_e("We'd love to try to make your experience better. Would you be willing to reach out directly to us for help?", 'user-notifications'); ?></p>
        <div class="user-notifications-review-prompt__actions">
          <a class="user-notifications-support__button user-notifications-review-prompt__close" href="<?php echo esc_url($review_url); ?>" target="_blank" rel="noopener noreferrer">
            <?php esc_html_e('Not Now', 'user-notifications'); ?>
          </a>
          <a class="user-notifications-support__button user-notifications-review-prompt__reach" href="<?php echo esc_url('https://www.dogbytemarketing.com/contact/'); ?>" target="_blank" rel="noopener noreferrer">
            <?php esc_html_e('Reach Out', 'user-notifications'); ?>
          </a>
        </div>
      </dialog>
    </div>
    <?php
  }

  /**
   * Render options page
   *
   * @return void
   */
  public function options_page() {
    ?>
    <div class="wrap user-notifications-settings">
      <?php // Core inserts admin notices after this. The title lives inside the header row, so without this marker notices sit beside Save Changes. ?>
      <hr class="wp-header-end" />
      <form action="options.php" method="post">
        <?php settings_fields('user_notifications_dogbytemarketing'); ?>
        <div class="user-notifications-settings__layout">
          <div class="user-notifications-settings__workspace">
            <div class="user-notifications-settings__header">
              <div class="user-notifications-settings__logo">
                <img class="user-notifications-settings__logo-img" src="<?php echo esc_url(plugins_url('assets/img/logo.png', $this->plugin)); ?>" alt="<?php esc_attr_e('User Notifications', 'user-notifications'); ?>" width="150" height="50" />
                <img class="user-notifications-settings__logo-icon" src="<?php echo esc_url(plugins_url('assets/img/icon.png', $this->plugin)); ?>" alt="" width="48" height="48" />
              </div>
              <div class="user-notifications-settings__heading">
                <div class="user-notifications-settings__heading-text">
                  <h1 class="user-notifications-settings__title" id="user-notifications-active-title"><?php esc_html_e('General', 'user-notifications'); ?></h1>
                  <p class="user-notifications-settings__summary" id="user-notifications-active-summary"><?php esc_html_e('Turn notifications on and choose how the bell behaves.', 'user-notifications'); ?></p>
                </div>
                <button type="submit" class="user-notifications-save"><?php esc_html_e('Save Changes', 'user-notifications'); ?></button>
              </div>
            </div>
            <div class="user-notifications-settings__body">
              <nav class="user-notifications-nav" aria-label="<?php esc_attr_e('Settings sections', 'user-notifications'); ?>">
                <div class="user-notifications-nav__list" role="tablist">
                  <button type="button" class="user-notifications-nav__tab is-active" id="user-notifications-tab-general" role="tab" data-tab="#user-notifications-panel-general" data-description="<?php esc_attr_e('Turn notifications on and choose how the bell behaves.', 'user-notifications'); ?>" aria-controls="user-notifications-panel-general" aria-selected="true">
                    <span class="dashicons dashicons-admin-settings" aria-hidden="true"></span>
                    <span class="user-notifications-nav__label"><?php esc_html_e('General', 'user-notifications'); ?></span>
                  </button>
                  <button type="button" class="user-notifications-nav__tab" id="user-notifications-tab-woocommerce" role="tab" data-tab="#user-notifications-panel-woocommerce" data-description="<?php esc_attr_e('Send notifications when order status changes.', 'user-notifications'); ?>" aria-controls="user-notifications-panel-woocommerce" aria-selected="false">
                    <span class="dashicons dashicons-cart" aria-hidden="true"></span>
                    <span class="user-notifications-nav__label"><?php esc_html_e('WooCommerce', 'user-notifications'); ?></span>
                  </button>
                  <button type="button" class="user-notifications-nav__tab" id="user-notifications-tab-privacy" role="tab" data-tab="#user-notifications-panel-privacy" data-description="<?php esc_attr_e('Choose what visitor and user data notifications may store.', 'user-notifications'); ?>" aria-controls="user-notifications-panel-privacy" aria-selected="false">
                    <span class="dashicons dashicons-shield" aria-hidden="true"></span>
                    <span class="user-notifications-nav__label"><?php esc_html_e('Privacy', 'user-notifications'); ?></span>
                  </button>
                  <button type="button" class="user-notifications-nav__tab" id="user-notifications-tab-advanced" role="tab" data-tab="#user-notifications-panel-advanced" data-description="<?php esc_attr_e('Control notifications added by other plugins.', 'user-notifications'); ?>" aria-controls="user-notifications-panel-advanced" aria-selected="false">
                    <span class="dashicons dashicons-admin-generic" aria-hidden="true"></span>
                    <span class="user-notifications-nav__label"><?php esc_html_e('Advanced', 'user-notifications'); ?></span>
                  </button>
                  <button type="button" class="user-notifications-nav__tab" id="user-notifications-tab-support" role="tab" data-tab="#user-notifications-panel-support" data-description="<?php esc_attr_e('Get help with User Notifications, report issues, or share your feedback.', 'user-notifications'); ?>" aria-controls="user-notifications-panel-support" aria-selected="false">
                    <span class="dashicons dashicons-sos" aria-hidden="true"></span>
                    <span class="user-notifications-nav__label"><?php esc_html_e('Support', 'user-notifications'); ?></span>
                  </button>
                </div>
              </nav>
              <div class="user-notifications-main">
                <section id="user-notifications-panel-general" class="user-notifications-panel is-active" role="tabpanel" aria-labelledby="user-notifications-active-title">
                  <div class="user-notifications-general__columns">
                    <div class="user-notifications-general__settings">
                      <?php
                      $this->render_setting_card('enabled', 'user_notifications_settings_dogbytemarketing[general][enabled]', __('Enabled?', 'user-notifications'), 'general', 'enabled');
                      $this->render_setting_card('disable_no_scroll', 'user_notifications_settings_dogbytemarketing[general][disable_no_scroll]', __('Disable No Scroll?', 'user-notifications'), 'general', 'disable_no_scroll', __('This will disable adding the no-scroll class after the notification icon is clicked as this could conflict with some themes.', 'user-notifications'));
                      $this->render_setting_card('bootstrap_icons', 'user_notifications_settings_dogbytemarketing[general][bootstrap_icons]', __('BootStrap Icons', 'user-notifications'), 'general', 'bootstrap_icons', __('If you use another icon set or already use BootStrap Icons, disable this option.', 'user-notifications'));
                      ?>
                    </div>
                    <div class="user-notifications-general__column user-notifications-implement">
                      <h2 class="user-notifications-general__title"><?php esc_html_e('Display Your Notification Bell', 'user-notifications'); ?></h2>
                      <p class="user-notifications-general__intro"><?php esc_html_e('Use the following shortcode in your navigation menu or a shortcode compatible area of your website.', 'user-notifications'); ?></p>
                      <div class="user-notifications-implement__code">
                        <code>[notifications_dogbytemarketing class="your-icon-class"]</code>
                        <button type="button" class="user-notifications-support__button user-notifications-support__button--ghost user-notifications-support__copy" data-copy="[notifications_dogbytemarketing class=&quot;your-icon-class&quot;]" data-copied="<?php esc_attr_e('Copied', 'user-notifications'); ?>">
                          <span class="user-notifications-support__copy-label"><?php esc_html_e('Copy', 'user-notifications'); ?></span>
                        </button>
                      </div>
                      <p class="user-notifications-implement__note"><?php esc_html_e('Using a custom icon library? You can specify your own icon classes using the class attribute.', 'user-notifications'); ?></p>
                    </div>
                  </div>
                </section>
                <section id="user-notifications-panel-woocommerce" class="user-notifications-panel" role="tabpanel" aria-labelledby="user-notifications-active-title" hidden>
                  <div class="user-notifications-general__settings">
                    <?php $this->render_setting_card('order_notifications', 'user_notifications_settings_dogbytemarketing[woocommerce][order_notifications]', __('Order Notifications', 'user-notifications'), 'woocommerce', 'order_notifications', __('Send notifications on order status changes.', 'user-notifications')); ?>
                  </div>
                </section>
                <section id="user-notifications-panel-privacy" class="user-notifications-panel" role="tabpanel" aria-labelledby="user-notifications-active-title" hidden>
                  <div class="user-notifications-general__settings">
                    <div class="user-notifications-callout">
                      <p><?php esc_html_e('These options collect data from guests/users. Ensure you are in compliance with data protection regulations before activation. Proceed at your own risk.', 'user-notifications'); ?></p>
                    </div>
                    <?php
                    $this->render_setting_card('read_tracking', 'user_notifications_settings_dogbytemarketing[privacy][read_tracking]', __('Read Tracking', 'user-notifications'), 'privacy', 'read_tracking', __('This allows users to mark notifications as read. This will add the notification IDs to a list of IDs that have been read in the user\'s meta.', 'user-notifications'));
                    $this->render_setting_card('user_targeting', 'user_notifications_settings_dogbytemarketing[privacy][user_targeting]', __('User Targeting', 'user-notifications'), 'privacy', 'user_targeting', __('This will allow you to send notifications to specific users. This processes existing user data to fetch their ID and username.', 'user-notifications'));
                    ?>
                  </div>
                </section>
                <section id="user-notifications-panel-advanced" class="user-notifications-panel" role="tabpanel" aria-labelledby="user-notifications-active-title" hidden>
                  <div class="user-notifications-general__settings">
                    <?php $this->render_setting_card('disable_third_party', 'user_notifications_settings_dogbytemarketing[advanced][disable_third_party]', __('Disable Third-Party Notifications', 'user-notifications'), 'advanced', 'disable_third_party', __('Stop other plugins from adding notifications to the admin bell and the front end.', 'user-notifications')); ?>
                  </div>
                </section>
                <section id="user-notifications-panel-support" class="user-notifications-panel" role="tabpanel" aria-labelledby="user-notifications-active-title" hidden>
                  <?php $this->support_panel_render(); ?>
                </section>
              </div>
              <div class="user-notifications-settings__savebar">
                <button type="submit" class="user-notifications-save"><?php esc_html_e('Save Changes', 'user-notifications'); ?></button>
              </div>
            </div>
          </div>
          <div class="user-notifications-cta-stack">
            <aside class="user-notifications-cta">
              <div class="user-notifications-cta__brand">
                <div class="user-notifications-cta__brand-top">
                  <img class="user-notifications-cta__logo" src="<?php echo esc_url(plugins_url('assets/img/dog-byte-logo-white.png', $this->plugin)); ?>" alt="<?php esc_attr_e('Dog Byte Marketing', 'user-notifications'); ?>" width="120" />
                </div>
                <h2 class="user-notifications-cta__title"><?php esc_html_e('Marketing Beyond Notifications', 'user-notifications'); ?></h2>
                <p class="user-notifications-cta__desc"><?php esc_html_e('Connect with customers, boost engagement, and drive sales with effective digital marketing.', 'user-notifications'); ?></p>
              </div>
              <div class="user-notifications-cta__body">
                <ul class="user-notifications-cta__benefits">
                  <li><span class="dashicons dashicons-yes" aria-hidden="true"></span><?php esc_html_e('Email marketing and automation', 'user-notifications'); ?></li>
                  <li><span class="dashicons dashicons-yes" aria-hidden="true"></span><?php esc_html_e('Social media marketing', 'user-notifications'); ?></li>
                  <li><span class="dashicons dashicons-yes" aria-hidden="true"></span><?php esc_html_e('Lead capture and conversion optimization', 'user-notifications'); ?></li>
                </ul>
                <a class="user-notifications-cta__button" href="<?php echo esc_url('https://www.dogbytemarketing.com/contact/'); ?>" target="_blank" rel="noopener noreferrer">
                  <?php esc_html_e('Let\'s Talk', 'user-notifications'); ?>
                  <span class="dashicons dashicons-arrow-right-alt" aria-hidden="true"></span>
                </a>
                <a class="user-notifications-cta__button user-notifications-cta__button--call" href="<?php echo esc_url('tel:+14237248922'); ?>">
                  <span class="dashicons dashicons-phone" aria-hidden="true"></span>
                  <?php esc_html_e('(423) 724-8922', 'user-notifications'); ?>
                </a>
                <hr class="user-notifications-cta__rule" />
                <p class="user-notifications-cta__location"><?php esc_html_e('USA Based · Serving Businesses Nationwide', 'user-notifications'); ?></p>
              </div>
            </aside>
            <aside class="user-notifications-plugins" aria-roledescription="carousel" aria-label="<?php esc_attr_e('Other plugins', 'user-notifications'); ?>">
              <div class="user-notifications-cta__brand user-notifications-plugins__header">
                <h2 class="user-notifications-cta__title"><?php esc_html_e('Explore Our Other Plugins', 'user-notifications'); ?></h2>
              </div>
              <div class="user-notifications-plugins__viewport">
                <div class="user-notifications-plugins__track">
                  <div class="user-notifications-plugins__slide">
                    <img class="user-notifications-plugins__icon" src="<?php echo esc_url(plugins_url('assets/img/plugins/projectplot.png', $this->plugin)); ?>" alt="" width="72" height="72" />
                    <h3 class="user-notifications-plugins__name"><?php esc_html_e('ProjectPlot', 'user-notifications'); ?></h3>
                    <p class="user-notifications-plugins__summary"><?php esc_html_e('Simplify task management and keep your team organized directly within WordPress.', 'user-notifications'); ?></p>
                    <ul class="user-notifications-cta__benefits">
                      <li><span class="dashicons dashicons-yes" aria-hidden="true"></span><?php esc_html_e('Create and manage tasks with ease', 'user-notifications'); ?></li>
                      <li><span class="dashicons dashicons-yes" aria-hidden="true"></span><?php esc_html_e('Track task priorities and progress', 'user-notifications'); ?></li>
                      <li><span class="dashicons dashicons-yes" aria-hidden="true"></span><?php esc_html_e('Keep your team organized', 'user-notifications'); ?></li>
                    </ul>
                    <a class="user-notifications-cta__button" href="https://wordpress.org/plugins/projectplot/" target="_blank" rel="noopener noreferrer">
                      <?php esc_html_e('Check Out Plugin', 'user-notifications'); ?>
                      <span class="dashicons dashicons-arrow-right-alt" aria-hidden="true"></span>
                    </a>
                  </div>
                  <div class="user-notifications-plugins__slide">
                    <img class="user-notifications-plugins__icon" src="<?php echo esc_url(plugins_url('assets/img/plugins/faq-schema-shortcode.png', $this->plugin)); ?>" alt="" width="72" height="72" />
                    <h3 class="user-notifications-plugins__name"><?php esc_html_e('FAQ Schema Shortcode', 'user-notifications'); ?></h3>
                    <p class="user-notifications-plugins__summary"><?php esc_html_e('Add FAQ sections with structured data so search engines can understand your content.', 'user-notifications'); ?></p>
                    <ul class="user-notifications-cta__benefits">
                      <li><span class="dashicons dashicons-yes" aria-hidden="true"></span><?php esc_html_e('Add FAQ sections with shortcodes', 'user-notifications'); ?></li>
                      <li><span class="dashicons dashicons-yes" aria-hidden="true"></span><?php esc_html_e('Generate JSON-LD structured data', 'user-notifications'); ?></li>
                      <li><span class="dashicons dashicons-yes" aria-hidden="true"></span><?php esc_html_e('Improve search visibility', 'user-notifications'); ?></li>
                    </ul>
                    <a class="user-notifications-cta__button" href="https://wordpress.org/plugins/faq-schema-shortcode/" target="_blank" rel="noopener noreferrer">
                      <?php esc_html_e('Check Out Plugin', 'user-notifications'); ?>
                      <span class="dashicons dashicons-arrow-right-alt" aria-hidden="true"></span>
                    </a>
                  </div>
                  <div class="user-notifications-plugins__slide">
                    <img class="user-notifications-plugins__icon" src="<?php echo esc_url(plugins_url('assets/img/plugins/sync-mautic.png', $this->plugin)); ?>" alt="" width="72" height="72" />
                    <h3 class="user-notifications-plugins__name"><?php esc_html_e('Sync Mautic', 'user-notifications'); ?></h3>
                    <p class="user-notifications-plugins__summary"><?php esc_html_e('Connect WordPress and WooCommerce activity to your Mautic contacts.', 'user-notifications'); ?></p>
                    <ul class="user-notifications-cta__benefits">
                      <li><span class="dashicons dashicons-yes" aria-hidden="true"></span><?php esc_html_e('Send leads into Mautic', 'user-notifications'); ?></li>
                      <li><span class="dashicons dashicons-yes" aria-hidden="true"></span><?php esc_html_e('Tag contacts from orders', 'user-notifications'); ?></li>
                      <li><span class="dashicons dashicons-yes" aria-hidden="true"></span><?php esc_html_e('Track checkout opt-ins', 'user-notifications'); ?></li>
                    </ul>
                    <a class="user-notifications-cta__button" href="https://wordpress.org/plugins/sync-mautic/" target="_blank" rel="noopener noreferrer">
                      <?php esc_html_e('Check Out Plugin', 'user-notifications'); ?>
                      <span class="dashicons dashicons-arrow-right-alt" aria-hidden="true"></span>
                    </a>
                  </div>
                </div>
              </div>
              <div class="user-notifications-plugins__dots">
                <button type="button" class="user-notifications-plugins__dot is-active" aria-label="<?php esc_attr_e('Show ProjectPlot', 'user-notifications'); ?>" aria-selected="true"></button>
                <button type="button" class="user-notifications-plugins__dot" aria-label="<?php esc_attr_e('Show FAQ Schema Shortcode', 'user-notifications'); ?>" aria-selected="false"></button>
                <button type="button" class="user-notifications-plugins__dot" aria-label="<?php esc_attr_e('Show Sync Mautic', 'user-notifications'); ?>" aria-selected="false"></button>
              </div>
              <div class="user-notifications-plugins__foot">
                <hr class="user-notifications-cta__rule" />
                <p class="user-notifications-cta__location"><?php esc_html_e('Free & Open Source · WordPress.org', 'user-notifications'); ?></p>
              </div>
            </aside>
          </div>
        </div>
      </form>
    </div>
    <?php
  }

  /**
   * Sanitize Options
   *
   * Known checkboxes are updated from the form. Any other stored keys stay as they are.
   *
   * @param  array $input Array of option inputs
   * @return array $sanitary_values Array of sanitized options
   */
  public function sanitize($input) {
    $existing        = get_option('user_notifications_settings_dogbytemarketing');
    $sanitary_values = is_array($existing) ? $existing : array();

    if (!is_array($input)) {
      return $sanitary_values;
    }

    $sections = array('general', 'woocommerce', 'privacy', 'advanced');

    foreach ($sections as $section) {
      if (!isset($sanitary_values[$section]) || !is_array($sanitary_values[$section])) {
        $sanitary_values[$section] = array();
      }
    }

    $sanitary_values['general']['enabled']                     = isset($input['general']['enabled']) && 'on' === $input['general']['enabled'];
    $sanitary_values['general']['disable_no_scroll']           = isset($input['general']['disable_no_scroll']) && 'on' === $input['general']['disable_no_scroll'];
    $sanitary_values['general']['bootstrap_icons']             = isset($input['general']['bootstrap_icons']) && 'on' === $input['general']['bootstrap_icons'];
    $sanitary_values['woocommerce']['order_notifications']     = isset($input['woocommerce']['order_notifications']) && 'on' === $input['woocommerce']['order_notifications'];
    $sanitary_values['privacy']['user_targeting']              = isset($input['privacy']['user_targeting']) && 'on' === $input['privacy']['user_targeting'];
    $sanitary_values['privacy']['read_tracking']               = isset($input['privacy']['read_tracking']) && 'on' === $input['privacy']['read_tracking'];
    $sanitary_values['advanced']['disable_third_party']        = isset($input['advanced']['disable_third_party']) && 'on' === $input['advanced']['disable_third_party'];

    return $sanitary_values;
  }

}

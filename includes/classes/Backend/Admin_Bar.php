<?php

namespace DogByteMarketing\User_Notifications\Backend;

use DogByteMarketing\User_Notifications\Notifications;

class Admin_Bar {

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
   * The bell belongs in wp-admin only, beside the username.
   *
   * @return void
   */
  public function init() {
    if (!is_admin()) {
      return;
    }

    add_action('admin_bar_menu', array($this, 'add_bell'), 9990);
    add_action('admin_footer', array($this, 'render_panel'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue'));
  }

  /**
   * Add the bell to the admin toolbar.
   *
   * @param \WP_Admin_Bar $wp_admin_bar Admin bar instance.
   * @return void
   */
  public function add_bell($wp_admin_bar) {
    $count = Notifications::count_unread_admin_notifications();
    $title  = '<span class="user-notifications-admin-bell__icon">';
    $title .= '<span class="ab-icon dashicons dashicons-bell" aria-hidden="true"></span>';

    if ($count > 0) {
      $title .= '<span class="user-notifications-admin-count">' . esc_html($count) . '</span>';
    }

    $title .= '</span>';

    $title .= '<span class="screen-reader-text">' . esc_html__('Notifications', 'user-notifications') . '</span>';

    $wp_admin_bar->add_node(array(
      'id'     => 'user-notifications',
      'parent' => 'top-secondary',
      'title'  => $title,
      'href'   => '#',
      'meta'   => array(
        'class' => 'user-notifications-admin-bell',
        'title' => __('Notifications', 'user-notifications'),
      ),
    ));
  }

  /**
   * Render the notification sidebar.
   *
   * @return void
   */
  public function render_panel() {
    $notifications = Notifications::get_visible_notifications('admin_bar');
    $read_ids      = Notifications::get_read_ids();
    $count         = 0;

    foreach ($notifications as $notification) {
      if (!in_array($notification['id'], $read_ids, true)) {
        $count++;
      }
    }

    ?>
    <div id="user-notifications-admin-panel" class="user-notifications-admin-panel" role="dialog" aria-label="<?php esc_attr_e('Notifications', 'user-notifications'); ?>">
      <div class="user-notifications-admin-panel__sidebar">
        <div class="notification-panel__header">
          <div class="notification-panel__heading">
            <div class="notification-panel__title-row">
              <h2 class="notification-panel__title"><?php esc_html_e('Notifications', 'user-notifications'); ?></h2>
              <?php if ($count) : ?>
                <span class="notification-panel__badge"><?php echo esc_html(sprintf(
                  /* translators: %d: number of unread notifications */
                  _n('%d new', '%d new', $count, 'user-notifications'),
                  $count
                )); ?></span>
              <?php endif; ?>
            </div>
            <p class="notification-panel__subtitle"><?php esc_html_e('Your latest updates and offers.', 'user-notifications'); ?></p>
          </div>
          <button type="button" class="close-notifications" aria-label="<?php esc_attr_e('Close notifications', 'user-notifications'); ?>">
            <span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
          </button>
        </div>
        <div class="user-notifications-admin-panel__notifications">
          <?php if ($notifications) : ?>
            <?php foreach ($notifications as $notification) : ?>
              <?php $this->render_panel_item($notification, !in_array($notification['id'], $read_ids, true)); ?>
            <?php endforeach; ?>
            <p class="notification-panel__end"><?php esc_html_e('No more notifications', 'user-notifications'); ?></p>
          <?php else : ?>
            <p class="user-notifications-admin-panel__empty"><?php esc_html_e('You have no notifications', 'user-notifications'); ?></p>
          <?php endif; ?>
        </div>
        <?php if ($notifications) : ?>
          <div class="user-notifications-admin-panel__footer">
            <button type="button" class="user-notifications-mark-all-read"><?php esc_html_e('Mark All Read', 'user-notifications'); ?></button>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <?php
  }

  /**
   * One row in the admin notification sidebar.
   *
   * @param  array $notification Notification from the admin bar context.
   * @param  bool  $unread       Whether this user has not marked it read.
   * @return void
   */
  private function render_panel_item($notification, $unread) {
    $classes = array('user-notifications-admin-panel__item');
    $classes[] = $unread ? 'is-unread' : 'is-read';

    if (!$notification['message']) {
      $classes[] = 'no-desc';
    }
    ?>
    <div class="<?php echo esc_attr(implode(' ', $classes)); ?>" data-id="<?php echo esc_attr($notification['id']); ?>">
      <div class="notification-preview">
        <?php if ($unread) : ?>
          <span class="notification-item__dot" aria-hidden="true"></span>
        <?php endif; ?>
        <span class="notification-item__title">
          <?php if ($notification['url']) : ?>
            <a href="<?php echo esc_url($notification['url']); ?>"><?php echo esc_html($notification['title']); ?></a>
          <?php else : ?>
            <?php echo esc_html($notification['title']); ?>
          <?php endif; ?>
        </span>
        <span class="notification-item__time"><?php echo esc_html(Notifications::notification_time_ago($notification)); ?></span>
        <span class="notification-item__actions">
          <?php if ($unread) : ?>
            <button type="button" class="notification-item__read" aria-label="<?php esc_attr_e('Mark as read', 'user-notifications'); ?>">
              <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
            </button>
          <?php endif; ?>
          <?php if ($notification['message']) : ?>
            <span class="dashicons dashicons-arrow-down-alt2 notification-item__chevron" aria-hidden="true"></span>
          <?php endif; ?>
        </span>
      </div>
      <?php if ($notification['message']) : ?>
        <div class="notification-full"><?php echo wp_kses_post($notification['message']); ?></div>
      <?php endif; ?>
    </div>
    <?php
  }

  /**
   * Enqueue the toolbar bell assets.
   *
   * @return void
   */
  public function enqueue() {
    wp_enqueue_style('user-notifications-admin-bar', plugins_url('/assets/css/backend/admin-bar.css', $this->plugin), array('dashicons'), $this->version);
    wp_enqueue_script('user-notifications-admin-bar', plugins_url('/assets/js/backend/admin-bar.js', $this->plugin), array('jquery', 'wp-i18n'), $this->version, true);
    wp_localize_script(
      'user-notifications-admin-bar',
      'user_notifications_admin',
      array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('user-notifications-nonce'),
      )
    );
    wp_set_script_translations('user-notifications-admin-bar', 'user-notifications', plugin_dir_path($this->plugin) . '/languages/');
  }

}

<?php

namespace DogByteMarketing\User_Notifications;

class Notifications {

  /**
   * User meta key for dismissed notification ids.
   *
   * @var string
   */
  const DISMISSED_META = '_dismissed_user_notifications';

  /**
   * User meta key for admin notifications marked read.
   *
   * @var string
   */
  const READ_META = '_read_admin_notifications';

  /**
   * Option that remembers when an admin notification was first seen.
   *
   * @var string
   */
  const SEEN_OPTION = 'user_notifications_admin_seen';

  /**
   * Guest cookie that stores dismissed notification ids.
   *
   * @var string
   */
  const DISMISSED_COOKIE = 'user_notifications_dismissed';

  /**
   * How long the guest cookie lasts.
   *
   * @var int
   */
  const COOKIE_TTL = YEAR_IN_SECONDS;

  /**
   * Cap stored ids so meta and the cookie stay small.
   *
   * @var int
   */
  const DISMISSED_LIMIT = 50;

  /**
   * Init
   *
   * @return void
   */
  public function init() {
    add_action('wp_ajax_user_notifications_dismiss', array($this, 'dismiss_notification'));
    add_action('wp_ajax_nopriv_user_notifications_dismiss', array($this, 'dismiss_notification'));
    add_action('wp_ajax_user_notifications_dismiss_all', array($this, 'dismiss_all_notifications'));
    add_action('wp_ajax_nopriv_user_notifications_dismiss_all', array($this, 'dismiss_all_notifications'));
    add_action('wp_ajax_user_notifications_mark_read', array($this, 'mark_notification_read'));
    add_action('wp_ajax_user_notifications_mark_all_read', array($this, 'mark_all_notifications_read'));
  }

  /**
   * Notifications other plugins registered for one context.
   *
   * @param  string $context admin_bar or frontend.
   * @return array
   */
  public static function get_notifications($context) {
    static $cache = array();

    if (isset($cache[$context])) {
      return $cache[$context];
    }

    // Other plugins add items through the filter. This setting turns that off without touching this plugin's own notices.
    if (Utils::get_option('advanced', 'disable_third_party')) {
      $cache[$context] = array();

      return array();
    }

    $notifications = apply_filters('user_notifications_notifications', array(), $context);

    if (!is_array($notifications)) {
      $cache[$context] = array();

      return array();
    }

    $prepared = array();
    $seen     = array();

    foreach ($notifications as $notification) {
      $item = self::prepare_notification($notification);

      if (!$item || isset($seen[$item['id']])) {
        continue;
      }

      $seen[$item['id']] = true;
      $prepared[]         = $item;
    }

    $cache[$context] = $prepared;

    return $prepared;
  }

  /**
   * Registered notifications the current visitor has not dismissed.
   *
   * @param  string $context admin_bar or frontend.
   * @return array
   */
  public static function get_visible_notifications($context) {
    $dismissed = self::get_dismissed_ids();
    $visible   = array();

    foreach (self::get_notifications($context) as $notification) {
      if (!in_array($notification['id'], $dismissed, true)) {
        $visible[] = $notification;
      }
    }

    return $visible;
  }

  /**
   * Ids this visitor has dismissed.
   *
   * Logged-in users are stored on the account. Guests are stored in the cookie.
   *
   * @return array
   */
  public static function get_dismissed_ids() {
    if (is_user_logged_in()) {
      $stored = get_user_meta(get_current_user_id(), self::DISMISSED_META, true);
      $ids    = is_array($stored) ? $stored : array();
    } else {
      $ids = self::get_cookie_ids();
    }

    return self::sanitize_ids($ids);
  }

  /**
   * Admin notifications the current user has not marked read.
   *
   * @return int
   */
  public static function count_unread_admin_notifications() {
    $read_ids = self::get_read_ids();
    $count    = 0;

    foreach (self::get_visible_notifications('admin_bar') as $notification) {
      if (!in_array($notification['id'], $read_ids, true)) {
        $count++;
      }
    }

    return $count;
  }

  /**
   * Admin notification ids this user has marked read.
   *
   * @return array
   */
  public static function get_read_ids() {
    if (!is_user_logged_in()) {
      return array();
    }

    $stored = get_user_meta(get_current_user_id(), self::READ_META, true);

    return self::sanitize_ids(is_array($stored) ? $stored : array());
  }

  /**
   * Relative time for one admin notification.
   *
   * A plugin can pass a unix time. Otherwise the first time this id showed up is kept.
   *
   * @param  array $notification Prepared notification.
   * @return string
   */
  public static function notification_time_ago($notification) {
    $timestamp = !empty($notification['time']) ? (int) $notification['time'] : self::seen_timestamp($notification['id']);

    return Utils::time_ago($timestamp);
  }

  /**
   * Ajax callback for dismissing one hooked notification.
   *
   * @return void
   */
  public function dismiss_notification() {
    $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

    if (!wp_verify_nonce($nonce, 'user-notifications-nonce')) {
      Utils::send_error(__('Authentication failed.', 'user-notifications'), 401);
    }

    $id = isset($_POST['id']) && is_string($_POST['id']) ? sanitize_key(wp_unslash($_POST['id'])) : '';

    if (!$id || !self::is_registered_id($id)) {
      Utils::send_error(__('Notification not found.', 'user-notifications'), 404);
    }

    self::store_dismissed_ids(array($id));

    Utils::send_success(__('Notification dismissed.', 'user-notifications'));
  }

  /**
   * Ajax callback for dismissing every visible notification in one context.
   *
   * @return void
   */
  public function dismiss_all_notifications() {
    $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

    if (!wp_verify_nonce($nonce, 'user-notifications-nonce')) {
      Utils::send_error(__('Authentication failed.', 'user-notifications'), 401);
    }

    $context = isset($_POST['context']) && is_string($_POST['context']) ? sanitize_key(wp_unslash($_POST['context'])) : '';

    if (!in_array($context, array('admin_bar', 'frontend'), true)) {
      Utils::send_error(__('Notification not found.', 'user-notifications'), 404);
    }

    $ids = array();

    foreach (self::get_visible_notifications($context) as $notification) {
      $ids[] = $notification['id'];
    }

    if ($ids) {
      self::store_dismissed_ids($ids);
    }

    Utils::send_success(__('Notification dismissed.', 'user-notifications'));
  }

  /**
   * Ajax callback for marking one admin notification read.
   *
   * @return void
   */
  public function mark_notification_read() {
    $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

    if (!wp_verify_nonce($nonce, 'user-notifications-nonce')) {
      Utils::send_error(__('Authentication failed.', 'user-notifications'), 401);
    }

    if (!get_current_user_id()) {
      Utils::send_error(__('Invalid user or password.', 'user-notifications'), 401);
    }

    $id = isset($_POST['id']) && is_string($_POST['id']) ? sanitize_key(wp_unslash($_POST['id'])) : '';

    if (!$id || !self::is_registered_id($id)) {
      Utils::send_error(__('Notification not found.', 'user-notifications'), 404);
    }

    self::store_read_ids(array($id));

    Utils::send_success(__('Notification marked as read.', 'user-notifications'));
  }

  /**
   * Ajax callback for marking every visible admin notification read.
   *
   * @return void
   */
  public function mark_all_notifications_read() {
    $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

    if (!wp_verify_nonce($nonce, 'user-notifications-nonce')) {
      Utils::send_error(__('Authentication failed.', 'user-notifications'), 401);
    }

    if (!get_current_user_id()) {
      Utils::send_error(__('Invalid user or password.', 'user-notifications'), 401);
    }

    $ids = array();

    foreach (self::get_visible_notifications('admin_bar') as $notification) {
      $ids[] = $notification['id'];
    }

    if ($ids) {
      self::store_read_ids($ids);
    }

    Utils::send_success(__('Your notifications have been marked as read.', 'user-notifications'));
  }

  /**
   * Keep only valid notification ids.
   *
   * @param  mixed $notification Raw filter item.
   * @return array
   */
  private static function prepare_notification($notification) {
    if (!is_array($notification)) {
      return array();
    }

    $id    = isset($notification['id']) ? sanitize_key($notification['id']) : '';
    $title = isset($notification['title']) ? sanitize_text_field($notification['title']) : '';

    if (!$id || '' === $title) {
      return array();
    }

    $message = isset($notification['message']) ? wp_kses_post($notification['message']) : '';
    $url     = isset($notification['url']) ? esc_url_raw($notification['url']) : '';
    $time    = isset($notification['time']) ? absint($notification['time']) : 0;

    return array(
      'id'      => $id,
      'title'   => $title,
      'message' => $message,
      'url'     => $url,
      'time'    => $time,
    );
  }

  /**
   * Whether a plugin currently registered this id.
   *
   * @param  string $id Notification id.
   * @return bool
   */
  private static function is_registered_id($id) {
    $contexts = array('admin_bar', 'frontend');

    foreach ($contexts as $context) {
      foreach (self::get_notifications($context) as $notification) {
        if ($notification['id'] === $id) {
          return true;
        }
      }
    }

    return false;
  }

  /**
   * Unix time for the first time this admin notification was seen.
   *
   * @param  string $id Notification id.
   * @return int
   */
  private static function seen_timestamp($id) {
    $stored = get_option(self::SEEN_OPTION, array());
    $seen   = is_array($stored) ? $stored : array();

    if (isset($seen[$id]) && is_numeric($seen[$id])) {
      return (int) $seen[$id];
    }

    $seen[$id] = time();

    if (count($seen) > self::DISMISSED_LIMIT) {
      $seen = array_slice($seen, -1 * self::DISMISSED_LIMIT, null, true);
    }

    update_option(self::SEEN_OPTION, $seen, false);

    return (int) $seen[$id];
  }

  /**
   * Store admin notification ids this user has marked read.
   *
   * @param  array $ids Notification ids.
   * @return void
   */
  private static function store_read_ids($ids) {
    if (!is_user_logged_in()) {
      return;
    }

    $stored = self::limit_ids(array_merge(self::get_read_ids(), $ids));

    update_user_meta(get_current_user_id(), self::READ_META, $stored);
  }

  /**
   * Store dismissed ids for the current visitor.
   *
   * Logged-in users are stored on the account. Guests are stored in the cookie.
   *
   * @param  array $ids Notification ids.
   * @return void
   */
  private static function store_dismissed_ids($ids) {
    $stored = self::limit_ids(array_merge(self::get_dismissed_ids(), $ids));

    if (is_user_logged_in()) {
      update_user_meta(get_current_user_id(), self::DISMISSED_META, $stored);
      return;
    }

    $value   = implode(',', $stored);
    $options = array(
      'expires'  => time() + self::COOKIE_TTL,
      'path'     => COOKIEPATH ? COOKIEPATH : '/',
      'secure'   => is_ssl(),
      'httponly' => true,
      'samesite' => 'Lax',
    );

    if (COOKIE_DOMAIN) {
      $options['domain'] = COOKIE_DOMAIN;
    }

    setcookie(self::DISMISSED_COOKIE, $value, $options);

    $_COOKIE[self::DISMISSED_COOKIE] = $value;
  }

  /**
   * Dismissed ids from the guest cookie.
   *
   * @return array
   */
  private static function get_cookie_ids() {
    if (!isset($_COOKIE[self::DISMISSED_COOKIE]) || !is_string($_COOKIE[self::DISMISSED_COOKIE])) {
      return array();
    }

    $raw = sanitize_text_field(wp_unslash($_COOKIE[self::DISMISSED_COOKIE]));

    if ('' === $raw) {
      return array();
    }

    return self::sanitize_ids(explode(',', $raw));
  }

  /**
   * Sanitize a list of notification ids.
   *
   * @param  array $ids Raw ids.
   * @return array
   */
  private static function sanitize_ids($ids) {
    $clean = array();

    if (!is_array($ids)) {
      return $clean;
    }

    foreach ($ids as $id) {
      $id = sanitize_key($id);

      if ($id) {
        $clean[] = $id;
      }
    }

    return array_values(array_unique($clean));
  }

  /**
   * Keep the newest ids when the list grows past the cap.
   *
   * @param  array $ids Notification ids.
   * @return array
   */
  private static function limit_ids($ids) {
    $ids = self::sanitize_ids($ids);

    if (count($ids) > self::DISMISSED_LIMIT) {
      $ids = array_slice($ids, -1 * self::DISMISSED_LIMIT);
    }

    return $ids;
  }

}

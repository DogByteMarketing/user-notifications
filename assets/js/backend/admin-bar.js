jQuery(document).ready(function ($) {

  const { __, _n, sprintf } = wp.i18n;
  let markingRead = false;

  togglePanel();
  toggleNotification();
  markAllRead();

  function togglePanel() {
    $(document).on('click', '#wp-admin-bar-user-notifications > a', function (e) {
      e.preventDefault();
      e.stopPropagation();

      setPanelOpen(!$('#user-notifications-admin-panel').hasClass('is-open'));
    });

    $(document).on('click', '#user-notifications-admin-panel .close-notifications', function (e) {
      e.preventDefault();

      setPanelOpen(false);
    });

    $(document).on('click', function (e) {
      if (!$(e.target).closest('#user-notifications-admin-panel, #wp-admin-bar-user-notifications').length) {
        setPanelOpen(false);
      }
    });

    $(document).on('keydown', function (e) {
      if (e.key === 'Escape') {
        setPanelOpen(false);
      }
    });
  }

  function setPanelOpen(open) {
    $('#user-notifications-admin-panel').toggleClass('is-open', open);
    $('html').toggleClass('no-scroll', open);
  }

  function toggleNotification() {
    $(document).on('click', '#user-notifications-admin-panel .notification-preview', function (e) {
      if ($(e.target).closest('a, .notification-item__read').length) {
        return;
      }

      let item    = $(this).closest('.user-notifications-admin-panel__item');
      let opening = !item.hasClass('is-open');

      item.find('.notification-full').slideToggle();
      item.toggleClass('is-open');

      if (opening && item.find('.notification-full').length) {
        markNotificationRead(item);
      }
    });

    $(document).on('click', '#user-notifications-admin-panel .notification-item__read', function (e) {
      e.preventDefault();
      e.stopPropagation();

      markNotificationRead($(this).closest('.user-notifications-admin-panel__item'));
    });
  }

  function markAllRead() {
    $(document).on('click', '#user-notifications-admin-panel .user-notifications-mark-all-read', function (e) {
      e.preventDefault();
      e.stopPropagation();

      if (markingRead) {
        return;
      }

      markingRead = true;

      $.post(user_notifications_admin.ajax_url, {
        action: 'user_notifications_mark_all_read',
        nonce: user_notifications_admin.nonce
      }).done(function () {
        $('#user-notifications-admin-panel .user-notifications-admin-panel__item.is-unread').each(function () {
          applyReadState($(this));
        });

        syncUnreadCount();
        setPanelOpen(false);
      }).fail(function (xhr) {
        let response = xhr.responseText ? JSON.parse(xhr.responseText) : {};

        alert(response.data ? response.data.message : __('Unknown error', 'user-notifications'));
      }).always(function () {
        markingRead = false;
      });
    });
  }

  function markNotificationRead(item) {
    let id = item.attr('data-id');

    if (!id || !item.hasClass('is-unread') || item.hasClass('is-marking')) {
      return;
    }

    item.addClass('is-marking');

    $.post(user_notifications_admin.ajax_url, {
      action: 'user_notifications_mark_read',
      nonce: user_notifications_admin.nonce,
      id: id
    }).done(function () {
      applyReadState(item);
      syncUnreadCount();
    }).fail(function (xhr) {
      let response = xhr.responseText ? JSON.parse(xhr.responseText) : {};

      item.removeClass('is-marking');
      alert(response.data ? response.data.message : __('Unknown error', 'user-notifications'));
    });
  }

  function applyReadState(item) {
    item.removeClass('is-unread is-marking').addClass('is-read');
    item.find('.notification-item__dot, .notification-item__read').remove();
  }

  function syncUnreadCount() {
    let count = $('#user-notifications-admin-panel .user-notifications-admin-panel__item.is-unread').length;
    let badge = $('.user-notifications-admin-panel .notification-panel__badge');

    if (!count) {
      badge.remove();
      $('.user-notifications-admin-count').remove();
      return;
    }

    badge.text(sprintf(_n('%d new', '%d new', count, 'user-notifications'), count));
    $('.user-notifications-admin-count').text(count);
  }

});

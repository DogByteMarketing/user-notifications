jQuery(document).ready(function ($) {

  const { __, _n, sprintf } = wp.i18n;
  let markingRead           = false;

  toggleNotifications();
  markRead();
  dismissNotification();

  function toggleNotifications() {
    $(".notification-container").on("click", function(e) {
      e.preventDefault();
      e.stopPropagation();

      $("html").addClass("no-scroll");
      $(".notification-sidebar-container").addClass("is-open");
    });

    $(".notification-sidebar-container").on("click", function(e) {
      e.stopPropagation();
    });

    $(".close-notifications").on("click", function(e) {
      e.preventDefault();

      closeNotifications();
    });

    $(document).on("click", function() {
      if ($(".notification-sidebar-container").hasClass("is-open")) {
        closeNotifications();
      }
    });

    $(".notification-preview").on("click", function(e) {
      if ($(e.target).closest("a, .notification-item__read").length) {
        return;
      }

      let item    = $(this).closest(".notification");
      let opening = !item.hasClass("is-open");

      item.find(".notification-full").slideToggle();
      item.toggleClass("is-open");

      if (opening && item.find(".notification-full").length) {
        markNotificationRead(item);
      }
    });

    $(".notification-sidebar-container").on("click", ".notification-item__read", function(e) {
      e.preventDefault();
      e.stopPropagation();

      markNotificationRead($(this).closest(".notification"));
    });
  }

  function markRead() {
    $(document).on('click', '.read-notifications', function () {
      if (markingRead) {
        return;
      }

      markingRead = true;

      let ajax_url = user_notifications_object.ajax_url;
      let nonce    = user_notifications_object.nonce;

      $(this).html(`<span class="loader"></span>`);

      $.post(ajax_url, {
        action: 'mark_notifications_read',
        nonce: nonce
      }, function(response) {
        closeNotifications();
      }).fail(function(xhr, status, error) {
        let response = JSON.parse(xhr.responseText);
        markingRead = false;

        $(".read-notifications").html(__('Mark All Read', 'user-notifications'));

        alert(response.data ? response.data.message : __('Unknown error', 'user-notifications'));
      }).done(function() {
        markingRead = false;

        $(".read-notifications").html(__('Mark All Read', 'user-notifications'));

        $(".notification-sidebar-container .notification.is-unread").not(".user-notifications-external").each(function() {
          applyReadState($(this));
        });

        syncUnreadCount();
      });
    });
  }

  function markNotificationRead(item) {
    let id = item.attr("data-notification-id");

    if (!id || !item.hasClass("is-unread") || item.hasClass("is-marking")) {
      return;
    }

    item.addClass("is-marking");

    $.post(user_notifications_object.ajax_url, {
      action: "mark_notification_read",
      nonce: user_notifications_object.nonce,
      id: id
    }).done(function() {
      applyReadState(item);
      syncUnreadCount();
    }).fail(function(xhr) {
      let response = xhr.responseText ? JSON.parse(xhr.responseText) : {};

      item.removeClass("is-marking");
      alert(response.data ? response.data.message : __("Unknown error", "user-notifications"));
    });
  }

  function applyReadState(item) {
    item.removeClass("is-unread is-marking").addClass("is-read");
    item.find(".notification-item__dot, .notification-item__read").remove();
  }

  function syncUnreadCount() {
    let count = $(".notification-sidebar-container .notification.is-unread").length;
    let badge = $(".notification-panel__badge");

    if (!count) {
      badge.remove();
      $(".notification-bubble").remove();
      return;
    }

    badge.text(sprintf(_n("%d new", "%d new", count, "user-notifications"), count));
    $(".notification-bubble").text(count);
  }

  function dismissNotification() {
    $(document).on('click', '.user-notifications-external a', function (e) {
      e.stopPropagation();
    });

    $(document).on('click', '.user-notifications-dismiss', function (e) {
      e.preventDefault();

      let button = $(this);
      let item   = button.closest('.user-notifications-external');
      let id     = item.attr('data-id');

      if (!id || button.hasClass('is-dismissing')) {
        return;
      }

      button.addClass('is-dismissing');

      $.post(user_notifications_object.ajax_url, {
        action: 'user_notifications_dismiss',
        nonce: user_notifications_object.nonce,
        id: id
      }).done(function () {
        item.remove();
        updateBubble();

        if (!$('.notification-sidebar-container .notification').length) {
          $('.notification-sidebar-container .notifications').append(
            '<div class="no-notifications">' + __('You have no notifications', 'user-notifications') + '</div>'
          );
        }
      }).fail(function (xhr) {
        let response = xhr.responseText ? JSON.parse(xhr.responseText) : {};

        button.removeClass('is-dismissing');
        alert(response.data ? response.data.message : __('Unknown error', 'user-notifications'));
      });
    });
  }

  function closeNotifications() {
    $("html").removeClass("no-scroll");
    $(".notification-sidebar-container").removeClass("is-open");
  }

  function updateBubble() {
    $('.notification-bubble').each(function () {
      let bubble = $(this);
      let count  = parseInt(bubble.text(), 10);

      if (!count || count <= 1) {
        bubble.remove();
        return;
      }

      bubble.text(count - 1);
    });
  }
  
});
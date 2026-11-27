jQuery(document).ready(function ($) {

  const { __, _x, _n, _nx } = wp.i18n;
  let disable_no_scroll     = user_notifications_object.disable_no_scroll;
  let markingRead           = false;

  toggleNotifications();
  markRead();

  function toggleNotifications() {
    $(".notification-container").on("click", function(e) {
      e.preventDefault();

      if (!disable_no_scroll) {
        $("html").addClass("no-scroll");
      }

      $(".notification-sidebar-container").show();
    });

    $(".close-notifications").on("click", function(e) {
      e.preventDefault();

      $("html").removeClass("no-scroll");

      $(".notification-sidebar-container").hide();
    });

    $(".notification-preview").on("click", function(){
      $(this).closest(".notification").find(".notification-full").slideToggle();
      let expand = $(this).closest(".notification").find(".expand-notification svg");

      $(expand).each(function() {
        let svg = $(this)

        if (svg.hasClass("active")) {
          $(this).toggleClass("active");
        } else {
          $(this).toggleClass("active");
        }
      });
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
        $("html").removeClass("no-scroll");

        $(".notification-sidebar-container").hide();
        $('.notification-bubble').remove();
      }).fail(function(xhr, status, error) {
        let response = JSON.parse(xhr.responseText);
        markingRead = false;

        $(".read-notifications").html(__('MARK ALL READ', 'user-notifications'));

        alert(response.data ? response.data.message : __('Unknown error', 'user-notifications'));
      }).done(function() {
        markingRead = false;

        $(".read-notifications").html(__('MARK ALL READ', 'user-notifications'));
      });
    });
  }
  
});
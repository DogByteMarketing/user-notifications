jQuery(document).ready(function ($) {

  const { __, _x, _n, _nx } = wp.i18n;

  initTabs();
  privacyChecks();

  function initTabs() {
    $(".nav-links li a").on("click", function() {
      let selected = $(this).data('section');

      $(".nav-links li a").each(function() {
        let section = $(this).data('section');

        $(this).removeClass("active");

        if (selected == section) {
          $(this).addClass("active");
        }
      });

      $(".tabs .tab").each(function() {
        let tab = $(this);

        $(this).hide();

        if (tab.hasClass(selected)) {
          tab.show();
        }
      });
    });
  }

  function privacyChecks() {
    $(document).on('change', '#order_notifications', function () {
      // When "Order Notifications" is enabled
      if ($(this).is(':checked')) {
        var userTargetingEnabled = $('#user_targeting').is(':checked');

        // Check if "User Targeting" is enabled
        if (!userTargetingEnabled) {
          Swal.fire({
            title: "Error!",
            text: __('You need to enable User Targeting under the Privacy section before enabling Order Notifications.', 'user-notifications'),
            icon: "error",
            confirmButtonColor: "#DD3544",
          });

          // Reset the checkbox
          $(this).prop('checked', false);
        }
      }
    });

    $(document).on('change', '#user_targeting', function () {
      if (!$(this).is(':checked')) {
        var orderNotificationsEnabled = $('#order_notifications').is(':checked');

        if (orderNotificationsEnabled) {
          Swal.fire({
            title: "Error!",
            text: __('Order Notifications requires User Targeting.', 'user-notifications'),
            icon: "error",
            confirmButtonColor: "#DD3544",
          });
          
          $(this).prop('checked', true);
        }
      }
    });
  }

});
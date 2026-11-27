jQuery(document).ready(function ($) {

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
      if ($(this).is(':checked')) { // When "Order Notifications" is enabled
        var userTargetingEnabled = $('#user_targeting').is(':checked'); // Check if "User Targeting" is enabled
        
        if (!userTargetingEnabled) {
          Swal.fire({
            title: "Error!",
            text: "You need to enable User Targeting under the Privacy section before enabling Order Notifications.",
            icon: "error",
            confirmButtonColor: "#DD3544",
          });

          $(this).prop('checked', false); // Reset the checkbox
        }
      }
    });

    $(document).on('change', '#user_targeting', function () {
      if (!$(this).is(':checked')) {
        var orderNotificationsEnabled = $('#order_notifications').is(':checked');

        if (orderNotificationsEnabled) {
          Swal.fire({
            title: "Error!",
            text: "Order Notifications requires User Targeting.",
            icon: "error",
            confirmButtonColor: "#DD3544",
          });
          
          $(this).prop('checked', true);
        }
      }
    });
  }

});
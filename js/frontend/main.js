jQuery(document).ready(function ($) {

  $(".notification-container").on("click", function(e) {
    e.preventDefault();

    $("html").addClass("no-scroll");
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
  
});
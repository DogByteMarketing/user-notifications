jQuery(document).ready(function($) {
  var $root = $('.user-notifications-settings');

  if (!$root.length) {
    return;
  }

  initTabs();
  initPluginSlider();
  initSupport();
  privacyChecks();

  function initTabs() {
    function tabHash($tab) {
      return $tab.attr('id').replace('user-notifications-tab-', '');
    }

    function showTab($tab, updateHash) {
      var target = $tab.data('tab');
      var hash = tabHash($tab);

      $root.find('.user-notifications-nav__tab').removeClass('is-active').attr('aria-selected', 'false');
      $tab.addClass('is-active').attr('aria-selected', 'true');

      $root.find('.user-notifications-panel').removeClass('is-active').attr('hidden', 'hidden');
      $root.find(target).addClass('is-active').removeAttr('hidden');

      $root.find('.user-notifications-settings__title').text($.trim($tab.find('.user-notifications-nav__label').text()));
      $root.find('.user-notifications-settings__summary').text($tab.attr('data-description'));

      if (!updateHash || !window.history || !window.history.replaceState) {
        return;
      }

      window.history.replaceState(null, '', window.location.pathname + window.location.search + '#' + hash);
    }

    function tabFromHash() {
      var hash = window.location.hash.replace('#', '');

      if (!/^[a-z0-9-]+$/i.test(hash)) {
        return $();
      }

      return $root.find('#user-notifications-tab-' + hash);
    }

    $root.on('click', '.user-notifications-nav__tab', function(e) {
      e.preventDefault();
      showTab($(this), true);
    });

    $(window).on('hashchange.userNotificationsTabs', function() {
      var $tab = tabFromHash();

      if ($tab.length) {
        showTab($tab, false);
      }
    });

    var $initial = tabFromHash();

    if ($initial.length) {
      showTab($initial, false);
    }
  }

  function initSupport() {
    var prompt = $root.find('.user-notifications-review-prompt').get(0);
    var promptTrigger = null;

    $root.on('click', '.user-notifications-support__star', function(e) {
      var rating = parseInt(this.getAttribute('data-rating'), 10);

      if (!prompt || rating > 3) {
        return;
      }

      e.preventDefault();
      promptTrigger = this;

      if (typeof prompt.showModal === 'function' && !prompt.open) {
        prompt.showModal();
      }
    });

    if (prompt) {
      $(prompt).on('click', function(e) {
        if (e.target === prompt) {
          prompt.close();
        }
      });

      $(prompt).on('click', '.user-notifications-review-prompt__close, .user-notifications-review-prompt__reach', function() {
        window.setTimeout(function() {
          if (prompt.open) {
            prompt.close();
          }
        }, 0);
      });

      $(prompt).on('close', function() {
        if (promptTrigger) {
          promptTrigger.focus();
          promptTrigger = null;
        }
      });
    }

    $root.on('click', '.user-notifications-support__copy', function() {
      var button = this;
      var text = button.getAttribute('data-copy');
      var $label = $(button).find('.user-notifications-support__copy-label');
      var original = $label.text();

      function copied() {
        $label.text(button.getAttribute('data-copied'));
        window.setTimeout(function() {
          $label.text(original);
        }, 2000);
      }

      function fallbackCopy() {
        var area = document.createElement('textarea');
        area.value = text;
        document.body.appendChild(area);
        area.select();
        document.execCommand('copy');
        document.body.removeChild(area);
        copied();
      }

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(copied).catch(fallbackCopy);
        return;
      }

      fallbackCopy();
    });
  }

  function initPluginSlider() {
    var $slider = $root.find('.user-notifications-plugins');
    var $track = $slider.find('.user-notifications-plugins__track');
    var $viewport = $slider.find('.user-notifications-plugins__viewport');
    var $dots = $slider.find('.user-notifications-plugins__dot');
    var count = $dots.length;
    var position = 1;
    var timer = null;
    var settleTimer = null;
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!$slider.length || count < 2) {
      return;
    }

    var $slides = $track.children('.user-notifications-plugins__slide');

    $track.prepend($slides.last().clone().attr('aria-hidden', 'true'));
    $track.append($slides.first().clone().attr('aria-hidden', 'true'));

    function showSlide(next, animate) {
      var width = $viewport.innerWidth();

      if (!width) {
        return;
      }

      if (reduceMotion) {
        if (next < 1) {
          next = count;
        } else if (next > count) {
          next = 1;
        }

        animate = false;
      } else if (next > count + 1) {
        next = ((next - 1) % count) + 1;
      } else if (next < 0) {
        next = count;
      }

      // Resize and dragging cancel the animation, so transitionend never fires. Do not stop on a clone.
      if (animate === false && (next === 0 || next === count + 1)) {
        next = next === 0 ? count : 1;
      }

      position = next;
      clearTimeout(settleTimer);

      if (animate === false) {
        $track.css('transition', 'none');
      }

      $track.css('transform', 'translateX(' + (position * -width) + 'px)');

      if (animate === false) {
        $track[0].offsetHeight;
        $track.css('transition', '');
      }

      var dot = position - 1;

      if (position === 0) {
        dot = count - 1;
      } else if (position > count) {
        dot = 0;
      }

      $dots.removeClass('is-active').attr('aria-selected', 'false');
      $dots.eq(dot).addClass('is-active').attr('aria-selected', 'true');

      if (position === 0 || position === count + 1) {
        settleTimer = setTimeout(finishLoop, 400);
      }
    }

    function finishLoop() {
      if (position === 0) {
        showSlide(count, false);
      } else if (position === count + 1) {
        showSlide(1, false);
      }
    }

    $track.on('transitionend', function(e) {
      if (e.target !== $track[0] || e.originalEvent.propertyName !== 'transform') {
        return;
      }

      finishLoop();
    });

    function queue() {
      if (reduceMotion) {
        return;
      }

      clearInterval(timer);
      timer = setInterval(function() {
        if (position === 0 || position === count + 1) {
          showSlide(position === 0 ? count : 1, false);
        }

        showSlide(position + 1);
      }, 6000);
    }

    showSlide(1, false);

    $(window).on('resize.userNotificationsPlugins', function() {
      showSlide(position, false);
    });

    $slider.on('click', '.user-notifications-plugins__dot', function() {
      showSlide($(this).index() + 1);
      queue();
    });

    $slider.on('mouseenter', function() {
      clearInterval(timer);
    });

    $slider.on('mouseleave', queue);

    var dragging = false;
    var startX = 0;
    var deltaX = 0;
    var width = 0;
    var pointerId = null;
    var suppressClick = false;

    $viewport.on('pointerdown', function(e) {
      if (e.pointerType === 'mouse' && e.button !== 0) {
        return;
      }

      if ($(e.target).closest('a, button').length) {
        return;
      }

      dragging = true;
      startX = e.clientX;
      deltaX = 0;
      width = $viewport.outerWidth();
      pointerId = e.pointerId;
      clearInterval(timer);
      $viewport.addClass('is-dragging');
      $track.css('transition', 'none');

      if ($viewport[0].setPointerCapture) {
        try {
          $viewport[0].setPointerCapture(e.pointerId);
        } catch (err) {
          // Some pointer events cannot be captured. Dragging still follows the move events.
        }
      }
    });

    $viewport.on('pointermove', function(e) {
      var offset = 0;

      if (!dragging || e.pointerId !== pointerId) {
        return;
      }

      deltaX = e.clientX - startX;
      offset = deltaX;

      $track.css('transform', 'translateX(' + ((position * -$viewport.innerWidth()) + offset) + 'px)');
    });

    $viewport.on('pointerup pointercancel', function(e) {
      var threshold = 0;

      if (!dragging || e.pointerId !== pointerId) {
        return;
      }

      dragging = false;
      pointerId = null;
      $viewport.removeClass('is-dragging');
      $track.css('transition', '');
      $track[0].offsetHeight;
      threshold = Math.max(40, width * 0.15);

      if (deltaX <= -threshold) {
        showSlide(position + 1);
      } else if (deltaX >= threshold) {
        showSlide(position - 1);
      } else {
        showSlide(position);
      }

      if (Math.abs(deltaX) > 8) {
        suppressClick = true;
      }

      if (e.pointerType !== 'mouse') {
        queue();
      }
    });

    $viewport.on('click', 'a, button', function(e) {
      if (!suppressClick) {
        return;
      }

      e.preventDefault();
      e.stopPropagation();
      suppressClick = false;
    });

    $viewport.on('dragstart', function(e) {
      e.preventDefault();
    });

    queue();
  }

  function privacyChecks() {
    const { __ } = wp.i18n;

    $(document).on('change', '#order_notifications', function () {
      if ($(this).is(':checked') && !$('#user_targeting').is(':checked')) {
        Swal.fire({
          title: 'Error!',
          text: __('You need to enable User Targeting under the Privacy section before enabling Order Notifications.', 'user-notifications'),
          icon: 'error',
          confirmButtonColor: '#DD3544',
        });

        $(this).prop('checked', false);
      }
    });

    $(document).on('change', '#user_targeting', function () {
      if (!$(this).is(':checked') && $('#order_notifications').is(':checked')) {
        Swal.fire({
          title: 'Error!',
          text: __('Order Notifications requires User Targeting.', 'user-notifications'),
          icon: 'error',
          confirmButtonColor: '#DD3544',
        });

        $(this).prop('checked', true);
      }
    });
  }

});

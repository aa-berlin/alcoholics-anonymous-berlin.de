;jQuery(function ($) {
    $('.main-navigation .menu-item .fa-home').toggleClass('fa-home fa-coffee zenzero-aa-nav-icon');
    $('.main-navigation .menu-item a[href*="/help"]').prepend('<i class="fa spaceRight fa-question-circle zenzero-aa-nav-icon">').wrapInner('<span class="zenzero-aa-nav-item-background">');

    const icon = $('<span class="zenzero-aa-stream-icon">').load('/wp-content/plugins/aa-berlin-addons/assets/images/phones.svg', function () {
        $('.main-navigation a[href*="type=ONL"]').each(function () {
            $(this).prepend(icon.clone());
        });
    });
});

jQuery(function ($) {
    const topButtons = $('.zenzero-aa-button.to-top');
    const header = $('#masthead');

    const update = function () {
        const shouldBeActive = $(window).scrollTop() > header.outerHeight();
        const isActive = topButtons.hasClass('is-active');

        if (shouldBeActive && !isActive) {
            topButtons.addClass('is-active');
        } else if (!shouldBeActive && isActive) {
            topButtons.removeClass('is-active');
        }
    };

    topButtons.on('click', function (e) {
        e.preventDefault();
        scrollTo(0, 0);
    });

    $(window).on('scroll resize', update);

    update();
});

jQuery(function ($) {
    // replicated parent theme functionality
    $('.widget-area').addClass('loaded');
    const buttons = $('.show-sidebar').on('click', function () {
        $(this).toggleClass('close');
        $('body').toggleClass('menu-opened');
    });

    var sidebar = $('#secondary').nanoScroller({ preventPageScrolling: true });

    const update = function () {
        const isVisible = sidebar.css('visibility') === 'visible';
        const shouldBeActive = !isVisible || ($('body').hasClass('menu-opened') && isVisible);
        const isActive = buttons.hasClass('is-active');

        if (shouldBeActive && !isActive) {
            buttons.addClass('is-active');
        } else if (!shouldBeActive && isActive) {
            buttons.removeClass('is-active');
        }
    };

    $(window).on('scroll resize', update);
    update();
});

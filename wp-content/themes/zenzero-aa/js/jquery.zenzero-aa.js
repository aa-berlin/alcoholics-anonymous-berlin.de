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
    const topButtons = $('.zenzero-aa-to-top');
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

    $(window).on('scroll', update);

    update();
});

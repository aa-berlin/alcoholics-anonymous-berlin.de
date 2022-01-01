;jQuery(function ($) {
    $('.main-navigation .menu-item .fa-home').toggleClass('fa-home fa-coffee zenzero-aa-nav-icon');
    $('.main-navigation .menu-item a[href*="/help"]').prepend('<i class="fa spaceRight fa-question-circle zenzero-aa-nav-icon">').wrapInner('<span class="zenzero-aa-nav-item-background">');

    const icon = $('<span class="zenzero-aa-stream-icon">').load('/wp-content/plugins/aa-berlin-addons/assets/images/phones.svg', function () {
        $('.main-navigation a[href*="type=ONL"], .tsml-widget-upcoming .type-onl a').each(function () {
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
    $('.menu-toggle').on('click', function () {
        $('body').toggleClass('menu-opened');
    }).one('click', function () {
        const secondary = $('#secondary ').appendTo(document.body);
        const nav = $('<nav class="mobile-nav widget">');
        const mobilePanel = secondary.find('.nano-content');

        $('<button class="close-secondary">').on('click', function () {
            $('body').removeClass('menu-opened');
            $('.main-navigation').removeClass('toggled');
        }).html('<i class="fa fa-close"></i>' + $('#close-search').text()).appendTo(nav);

        $('#menu-main-menu').clone().attr('id', 'menu-main-menu-mobile').appendTo(nav);
        nav.appendTo(mobilePanel);

        $('#sidebar .sidebar-content').clone().children().appendTo(mobilePanel);
    });
});

jQuery(function ($) {
    const sidebars = $('#sidebar, #secondary');

    sidebars.find('a[data-target-url]').attr({
        role: 'link',
        tabindex: 0
    });

    sidebars.on(
        'focus.restoreHref mouseover.restoreHref touchstart.restoreHref touchmove.restoreHref',
        'a[data-target-url]',
        function () {
            const link = $(this);
            link.off('.restoreHref');
            link.attr('href', link.data('targetUrl')).removeAttr('data-target-url');
        }
    )
});

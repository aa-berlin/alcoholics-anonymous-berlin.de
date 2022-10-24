;jQuery(function ($) {
    const headphones = '<i class="zenzero-aa-meeting-icon" data-feather="headphones">';
    const users = '<i class="zenzero-aa-meeting-icon" data-feather="users">';

    $('.main-navigation .menu-item .fa-home').replaceWith('<i class="zenzero-aa-nav-icon" data-feather="coffee">');
    $('.main-navigation .menu-item a[href*="/help"]').prepend('<i class="zenzero-aa-nav-icon" data-feather="help-circle">')
        .wrapInner('<span class="zenzero-aa-nav-item-background">');

    $('.main-navigation').find('a[href*="type=ONL"], a[href*="option=online"]').prepend('<i class="zenzero-aa-nav-icon" data-feather="headphones">');
    $('.main-navigation a[href*="/contact"]').prepend('<i class="zenzero-aa-nav-icon" data-feather="mail">');

    $('.tsml-widget-upcoming').each(function (i, meetings) {
        meetings = $(meetings);

        meetings.find('.attendance-online a').prepend(headphones);
        meetings.find('.attendance-in_person a').prepend(users);
        meetings.find('.attendance-hybrid a').prepend(headphones).prepend(users);
    });

    $('body.attendance-online .page-header h1').prepend(headphones);
    $('body.attendance-in_person .page-header h1').prepend(users);
    $('body.attendance-hybrid .page-header h1').prepend(headphones).prepend(users);

    $('.list-group-item-meetings').each(function (i, meetings) {
        meetings = $(meetings);

        meetings.find('.meeting.attendance-online > a').prepend(headphones);
        meetings.find('.meeting.attendance-in_person > a').prepend(users);
        meetings.find('.meeting.attendance-hybrid > a').prepend(headphones).prepend(users);
    });

    $('.entry-content, .meeting-notes').find('a[href]').each(function (i, link) {
        link = $(link);

        link.filter('[href*="type=ONL"]').prepend(headphones);
        link.filter('[href*="attendance_option=online"]').prepend(headphones);
        link.filter('[href*="zoom.us/j/"]').prepend(headphones);
        link.filter('[href*="attendance_option=in_person"]').prepend(users);
    });
});

jQuery(function ($) {
    const prependIconInResults = function (tbody) {
        tbody.find('.type-onl td.name > a').prepend('<i class="zenzero-aa-meeting-icon" data-feather="headphones">');
        tbody.find('.attendance-in_person, .attendance-hybrid').find('td.name > a').prepend('<i class="zenzero-aa-meeting-icon" data-feather="users">');
        feather.replace();
    };

    $('#meetings_tbody').on('tsml_meetings_updated', function (e, data) {
        prependIconInResults(data.tbody);
    }).each(function (i, tbody) {
        tbody = $(tbody);
        prependIconInResults(tbody);
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

    const body = $('body').on('click', function (e) {
        if (body.hasClass('menu-opened') && !$(e.target).closest('#secondary, .menu-toggle').length) {
            closeSecondary();
        }
    });

    $('.menu-toggle').on('click', function () {
        $('body').toggleClass('menu-opened');
    }).one('click', function () {
        const secondary = $('#secondary ').appendTo(document.body);
        const nav = $('<nav class="mobile-nav widget">');
        const mobilePanel = secondary.find('.nano-content');

        $('<button class="close-secondary">').on('click', closeSecondary).html('<i class="fa fa-close"></i>' + $('#close-search').text()).appendTo(nav);

        $('#menu-main-menu').clone().attr('id', 'menu-main-menu-mobile').appendTo(nav);
        nav.appendTo(mobilePanel);

        $('#sidebar .sidebar-content').clone().children().appendTo(mobilePanel);
    });

    function closeSecondary() {
        body.removeClass('menu-opened');
        $('.main-navigation').removeClass('toggled');
    }
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

jQuery(function ($) {
    feather.replace();
});

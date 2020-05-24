;jQuery(function ($) {
    $('.main-navigation .menu-item .fa-home').toggleClass('fa-home fa-coffee zenzero-aa-nav-icon');
    $('.main-navigation .menu-item a[href*="/help"]').prepend('<i class="fa spaceRight fa-question-circle zenzero-aa-nav-icon">').wrapInner('<span class="zenzero-aa-nav-item-background">');

    const icon = $('<span class="zenzero-aa-stream-icon">').load('/wp-content/plugins/aa-berlin-addons/assets/images/phones.svg', function () {
        $('.main-navigation a[href*="type=ONL"]').each(function () {
            $(this).prepend(icon.clone());
        });
    });
});

;jQuery(function ($) {
    $('.main-navigation .menu-item-home .fa-home').toggleClass('fa-home fa-coffee');

    const icon = $('<span class="zenzero-aa-stream-icon">').load('/wp-content/themes/zenzero-aa/images/phones.svg');

    $('.main-navigation a[href*="type=ONLINE"]').prepend(icon);
});

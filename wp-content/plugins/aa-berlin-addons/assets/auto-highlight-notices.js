jQuery(function ($) {
    $('p:contains("IMPORTANT:"), p:contains("UPDATE:")').each(function (i, paragraph) {
        paragraph = $(paragraph);
        paragraph.addClass('aa-berlin-addons-auto-highlight-notice');

        if (paragraph.is(':contains("IMPORTANT:")')) {
            paragraph.addClass('type-warning');
        } else {
            paragraph.addClass('type-success');
        }
    });
});

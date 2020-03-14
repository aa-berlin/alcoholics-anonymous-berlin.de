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

    $('p:contains("https://")').each(function (i, paragraph) {
        paragraph = $(paragraph);

        if (paragraph.children().length) {
            // will not try replace urls with auto links, if other markup present (an editor wrote it this way)
            return;
        }
        paragraph.addClass('aa-berlin-addons-contains-auto-link');

        let html = paragraph.text();
        html = html.replace(/https:\/\/([^/]+)([\S]+)/ig, function (link, domain, uri) {
            link = link.replace(/[.?!]$/, '');

            var isExternal = domain !== location.host;

            return [
                '<a href="',
                link,
                '" title="',
                isExternal ? 'External link to ' + domain : '',
                '" class="aa-berlin-addons-auto-link">',
                domain,
                '</a>'
            ].join('');
        });

        paragraph.html(html);
    });
});

(function (jQuery, __) {

    const markerTextImportant = __('IMPORTANT:', 'aa-berlin-addons');
    const markerTextUpdate = __('UPDATE:', 'aa-berlin-addons');

    jQuery(function ($) {
        $('p:contains("' + markerTextImportant + '"), p:contains("' + markerTextUpdate + '")').each(function (i, paragraph) {
            paragraph = $(paragraph);
            paragraph.addClass('aa-berlin-addons-auto-highlight-notice');

            if (paragraph.is(':contains("' + markerTextImportant + '")')) {
                paragraph.addClass('type-warning');
            } else {
                paragraph.addClass('type-success');
            }
        });

        const augmentedLinkHintTemplate = $('#aa-berlin-addons-hint-for-augmented-links');
        $('p:contains("https://")').each(function (i, paragraph) {
            paragraph = $(paragraph);

            if (paragraph.children().length) {
                // will not try replace urls with auto links, if other markup present (an editor wrote it this way)
                return;
            }
            paragraph.addClass('aa-berlin-addons-contains-auto-link');

            let html = paragraph.text();
            html = html.replace(/https:\/\/([^/]+)([\S]+)/ig, function (link, domain, uri) {
                // translators: %s is the link's generated text (usually it's host part)
                const externalLinkText = sprintf(__('External link to %s', 'aa-berlin-addons'), domain);

                link = link.replace(/[.?!]$/, '');

                const isExternal = domain !== location.host;

                return [
                    '<a href="',
                    link,
                    '" title="',
                    isExternal ? externalLinkText : '',
                    '" class="aa-berlin-addons-auto-link">',
                    domain,
                    '</a>'
                ].join('');
            });

            paragraph.html(html);
            $(augmentedLinkHintTemplate.html()).insertAfter(paragraph);
        });

        $('.wp-block-latest-posts').each(function (i, latestPosts) {
            latestPosts = $(latestPosts);

            if (latestPosts.children().length !== 1) {
                return;
            }

            latestPosts.find('> * > a[href]:first-child').insertBefore(latestPosts).wrap('<h2 class="aa-berlin-addons-auto-headline">');
        });
    });

})(jQuery, wp.i18n.__, wp.i18n.sprintf);

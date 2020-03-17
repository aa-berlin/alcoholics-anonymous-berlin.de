(function (jQuery, __) {

    const msBeforeActivationOfStreams = 30 * 60 * 1000;
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

        $('.list-group-item.meeting-info').each(function (i, meetingInfo) {
            meetingInfo = $(meetingInfo);

            let time = meetingInfo.find('.meeting-time').attr('content');
            time = new Date(time);
            const now = new Date();

            if (isNaN(time.getTime())) {
                // no start time, do nothing
                return;
            }

            const link = meetingInfo.find('a[href*="//zoom.us/"]');

            if (!link.length) {
                return;
            }

            if (time.getDay() !== now.getDay()) {
                return;
            }

            let msTillActivation = time.getTime() - now;
            // date probably has been rendered for the meeting next week
            msTillActivation = msTillActivation % (24 * 3600 * 1000);
            msTillActivation = msTillActivation - msBeforeActivationOfStreams;

            if (msTillActivation <= 0) {
                return;
            }

            link.attr({
                'stream-href': link.attr('href'),
                'role': 'link',
                'aria-disabled': true,
            });
            link.removeAttr('href');
            link.parent().addClass('aa-berlin-addons-contains-disabled-auto-link');

            setTimeout(function () {
                link.attr('href', link.attr('stream-href'));
                link.removeAttr('aria-disabled');
                link.parent().removeClass('aa-berlin-addons-contains-disabled-auto-link');
            }, msTillActivation)
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

(function (jQuery, __) {

    const msBeforeActivationOfStreams = 30 * 60 * 1000;
    const markerTextImportant = __('IMPORTANT:', 'aa-berlin-addons');
    const markerTextUpdate = __('UPDATE:', 'aa-berlin-addons');
    const onlineIconTitle = __('You can join this meeting online.', 'aa-berlin-addons');
    const msPerDay = 24 * 3600 * 1000;

    const onlineIconHtml = '<span class="glyphicon glyphicon-headphones" role="presentation" title="' + onlineIconTitle + '"></span>';

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
                    domain === 'zoom.us' ? onlineIconHtml : '',
                    domain,
                    '</a>'
                ].join('');
            });

            paragraph.html(html);
            $(augmentedLinkHintTemplate.html()).insertAfter(paragraph);
        });

        const deactivateLink = function (link) {
            link.attr({
                'stream-href': link.attr('href'),
                'role': 'link',
                'aria-disabled': true,
            });
            link.removeAttr('href');
            link.parent().addClass('aa-berlin-addons-contains-disabled-auto-link');
        };

        $('.list-group-item.meeting-info').each(function (i, meetingInfo) {
            meetingInfo = $(meetingInfo);

            const link = meetingInfo.find('a[href*="//zoom.us/"]');

            if (!link.length) {
                return;
            }

            const now = new Date().getTime();

            let startTime = meetingInfo.find('.meeting-time').attr('content');
            startTime = new Date(startTime);

            let endTime = meetingInfo.find('.meeting-time').attr('data-end-date');
            endTime = new Date(endTime);

            if (isNaN(startTime.getTime()) || isNaN(endTime.getTime())) {
                // no start or end time, do nothing
                return;
            }

            startTime = startTime.getTime() - 7 * msPerDay;
            startTime = startTime - msBeforeActivationOfStreams;

            endTime = endTime.getTime() - 7 * msPerDay;
            endTime = endTime + msBeforeActivationOfStreams;

            if (endTime < startTime) {
                endTime += msPerDay;
            }

            const isActive = now >= startTime && now <= endTime;

            const duration = endTime - startTime;
            const msTillActivation = startTime - msBeforeActivationOfStreams - now;
            const msTillDeactivationAgain = msTillActivation + duration + msBeforeActivationOfStreams;

            if (!isActive) {
                deactivateLink(link);
            }

            if (msTillActivation > 0) {
                setTimeout(function () {
                    link.attr('href', link.attr('stream-href'));
                    link.removeAttr('aria-disabled');
                    link.parent().removeClass('aa-berlin-addons-contains-disabled-auto-link');
                }, msTillActivation);
            }

            if (msTillDeactivationAgain > 0) {
                setTimeout(function () {
                    deactivateLink(link);
                }, msTillDeactivationAgain);
            }
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

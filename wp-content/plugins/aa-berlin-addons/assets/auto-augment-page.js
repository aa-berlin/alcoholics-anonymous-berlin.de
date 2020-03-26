(function (jQuery, __) {

    const regexQuotes = /"/g;
    const options = aa_berlin_addons_options;
    const msBeforeActivationOfStreams = 30 * 60 * 1000;
    const markerTextWarning = String(options.warning_prefix).replace(regexQuotes, '');
    const markerTextSuccess = String(options.success_prefix).replace(regexQuotes, '');
    const markerTextInfo = String(options.info_prefix).replace(regexQuotes, '');
    const onlineIconTitle = __('You can join this meeting online.', 'aa-berlin-addons');
    const msPerDay = 24 * 3600 * 1000;
    const streamDomains = String(options.stream_domains_pattern).split(/\s*,\s*/g);

    const isStream = function (domain) {
        for (let i = 0, length = streamDomains.length; i < length; i ++) {
            if (domain === streamDomains[i]) {
                return true;
            }
        }

        return false;
    };

    const onlineIconHtml = '<span class="aa-berlin-addons-stream-icon glyphicon glyphicon-headphones" role="presentation" title="' + onlineIconTitle + '"></span>';

    jQuery(function ($) {
        options.insert_notices && $('p:contains("' + markerTextWarning + '"), p:contains("' + markerTextSuccess + '"), p:contains("' + markerTextInfo + '")').each(function (i, paragraph) {
            paragraph = $(paragraph);
            paragraph.addClass('aa-berlin-addons-auto-highlight-notice');

            if (paragraph.is(':contains("' + markerTextWarning + '")')) {
                paragraph.addClass('type-warning');
            } else if (paragraph.is(':contains("' + markerTextSuccess + '")')) {
                paragraph.addClass('type-success');
            } else {
                paragraph.addClass('type-info');
            }
        });

        const augmentedLinkHintTemplate = $('#aa-berlin-addons-hint-for-augmented-links');
        options.insert_links && $('p:contains("https://")').each(function (i, paragraph) {
            paragraph = $(paragraph);

            if (paragraph.children().length) {
                // will not try replace urls with auto links, if other markup present (an editor wrote it this way)
                return;
            }

            let domainEncountered = null;
            let linkWasSubstituted = false;
            let html = paragraph.text();
            html = html.replace(/https:\/\/([^/\s]+)([\S]*)/ig, function (link, domain, uri) {
                linkWasSubstituted = true;
                domainEncountered = domain;

                // translators: %s is the link's generated text (usually it's host part)
                const externalLinkText = sprintf(__('External link to %s', 'aa-berlin-addons'), domain);
                link = link.replace(/[.?!]$/, '');

                const isExternal = domain !== location.host;

                return [
                    '<a href="',
                    link,
                    '" title="',
                    isExternal ? externalLinkText : '',
                    '" class="aa-berlin-addons-auto-link" ',
                    isExternal ? 'target="_blank"' : '',
                    '>',
                    options.prepend_stream_icons && isStream(domain) ? onlineIconHtml : '',
                    domain,
                    '</a>'
                ].join('');
            });

            if (linkWasSubstituted) {
                const hints = $(augmentedLinkHintTemplate.html()).filter(':has([data-if-link-domain-is="' + domainEncountered + '"])');

                paragraph.addClass('aa-berlin-addons-contains-auto-link');
                paragraph.html(html);
                hints.insertAfter(paragraph);
            }
        });

        const deactivateLink = function (link) {
            // FIXME: ran into trouble with daylight saving time of future date, disabling
            //return;

            link.attr({
                'stream-href': link.attr('href'),
                'role': 'link',
                'aria-disabled': true,
            });
            link.removeAttr('href');
            link.parent().addClass('aa-berlin-addons-contains-disabled-auto-link');
        };

        options.disable_outside_schedule && $('.list-group-item.meeting-info').each(function (i, meetingInfo) {
            meetingInfo = $(meetingInfo);

            const link = meetingInfo.find('a[href]').filter(function (i, link) {
                const domain = new URL(link.href).host;

                return isStream(domain);
            });

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

            const msTillActivation = startTime - now;
            const msTillDeactivationAgain = endTime - now;

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

        if (options.add_stream_icon_to_online_meetings) {
            $('body.tsml-type-online .page-header h1').prepend(onlineIconHtml);
            $('.list-group-item-meetings .meeting.type-online > a').prepend(onlineIconHtml);

            const prependStreamIconInResults = function (tbody) {
                tbody.find('.type-online td.name > a').prepend(onlineIconHtml);
            };

            $('#meetings_tbody').on('tsml_meetings_updated', function (e, data) {
                prependStreamIconInResults(data.tbody);
            }).each(function (i, tbody) {
                prependStreamIconInResults($(tbody));
            });
        }

        options.wrap_single_entry_links_with_h2 && $('.wp-block-latest-posts').each(function (i, latestPosts) {
            latestPosts = $(latestPosts);

            if (latestPosts.children().length !== 1) {
                return;
            }

            latestPosts.find('> * > a[href]:first-child').insertBefore(latestPosts).wrap('<h2 class="aa-berlin-addons-auto-headline">');
        });

        const standaloneStreamIcon = $('<span class="aa-berlin-addons-standalone-stream-icon">').load('/wp-content/plugins/aa-berlin-addons/assets/images/phones.svg', function () {
            $('.entry-content a[href]:not(.aa-berlin-addons-auto-link)').each(function (i, link) {
                const needsOnlineIcon = isStream(new URL(link.href).host) || link.href.indexOf('type=ONLINE') !== -1;

                if (needsOnlineIcon) {
                    $(link).prepend(standaloneStreamIcon.clone());
                }
            });
        });
    });

})(jQuery, wp.i18n.__, wp.i18n.sprintf);

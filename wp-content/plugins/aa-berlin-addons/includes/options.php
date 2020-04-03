<?php

if (!class_exists('RationalOptionPages')) {
    require __DIR__ . '/RationalOptionPages/RationalOptionPages.php';
}

$pages = array(
    'aa_berlin_addons_options' => array(
        'page_title' => __('AA Berlin Addons', 'aa-berlin-addons'),
        'menu_slug' => 'aa_berlin_addons_options',
        // no parent -> top-level item
        // 'parent_slug' => 'options-general.php',
        'icon_url' => 'dashicons-carrot',
        'position' => 61,
        'sections' => array(

            'links' => array(
                'title' => __('Link substitution', 'aa-berlin-addons'),
                'text' => '<p>' . __('Configures the automatic substitution of anchor elements for fully qualified secure urls.', 'aa-berlin-addons') . '</p>',

                'fields' => array(
                    array(
                        'title' => __('Insert links', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'insert_links',
                    ),

                    array(
                        'title' => __('Prepend "headphones" icon to online meeting links', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'prepend_stream_icons',
                    ),

                    array(
                        'title' => __('Treat links to these domains as "online meeting" links', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'zoom.us',
                        'text' => 'Separate multiple domains by comma, case-insensitive',
                        'id' => 'stream_domains_pattern',
                    ),

                    array(
                        'title' => __('Extract meeting id from zoom.us links and append to paragraph as well', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'text' => 'Finds the meeting id in each zoom link and writes it into the containing paragraph to help people with accessing the meeting via phone.',
                        'id' => 'append_zoom_meeting_id',
                    ),

                    array(
                        'title' => __('Disable online meeting links, if meeting is not currently on', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'text' => 'Feature currently BROKEN due to its handling of daylight savings time; Disables online meeting links 30min before and after the meetings scheduled start and end time.',
                        'id' => 'disable_outside_schedule',
                    ),

                    array(
                        'title' => __('Common hint to be shown after each online meeting link', 'aa-berlin-addons'),
                        'type' => 'wp_editor',
                        'text' => 'Inserted after each online meeting link.',
                        'id' => 'stream_link_hint',
                    ),

                    array(
                        'title' => __('Show the following hint text as a notice', 'aa-berlin-addons'),
                        'type' => 'select',
                        'choices' => array(
                            '' => __('Disable hint', 'aa-berlin-addons'),
                            'warning' => __('Warning', 'aa-berlin-addons'),
                            'success' => __('Success', 'aa-berlin-addons'),
                            'info' => __('Info', 'aa-berlin-addons'),
                        ),
                        'id' => 'stream_link_hint_type',
                    ),
                ),
            ),

            'automatic_notices' => array(
                'title' => __('Automatic Notices', 'aa-berlin-addons'),
                'text' => '<p>' . __('Configures the automatic insertion of highlighting notices in paragraphs that do not contain any other tags; e.g. in the meeting notes on the meeting detail.', 'aa-berlin-addons') . '</p>',

                'fields' => array(
                    array(
                        'title' => __('Insert notices', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'text' => 'Case-sensitive, no double quotes',
                        'id' => 'insert_notices',
                    ),

                    array(
                        'title' => __('Warning prefix', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'IMPORTANT:',
                        'text' => 'Case-sensitive, no double quotes',
                        'id' => 'warning_prefix',
                    ),

                    array(
                        'title' => __('Success prefix', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'UPDATE:',
                        'text' => 'Case-sensitive, no double quotes',
                        'id' => 'success_prefix',
                    ),

                    array(
                        'title' => __('Info prefix', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'INFO:',
                        'text' => 'Case-sensitive, no double quotes',
                        'id' => 'info_prefix',
                    ),
                ),
            ),

            'type_online' => array(
                'title' => __('Handling of meeting type Online', 'aa-berlin-addons'),
                'text' => '<p>' . __('Adds some custom behaviour of meetings with type ONL (ONLINE).', 'aa-berlin-addons') . '</p>',

                'fields' => array(
                    array(
                        'title' => __('Rename meeting type ONL(-INE)', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'add_type_online',
                    ),

                    array(
                        'title' => __('Allow manual type ONL(-INE)', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'text' => 'If checked, you can mark a post as ONL(-INE) without also providing phone or link details.',
                        'id' => 'allow_type_online_without_link',
                    ),

                    array(
                        'title' => __('Add "headphones" icon to Online meetings', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'add_stream_icon_to_online_meetings',
                    ),

                    array(
                        'title' => __('Label of meeting type ONL(-INE)', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'Join Online',
                        'id' => 'label_type_online',
                    ),

                    array(
                        'title' => __('Disable map on meeting detail', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'disable_map_if_online',
                    ),

                    array(
                        'title' => __('Text of map overlay', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'This meeting ONLY meets online until further notice.',
                        'text' => 'Should describe why the map is rendered inaccessible; no double-quotes',
                        'id' => 'disable_map_text_online',
                    ),
                ),
            ),

            'temporary_closure' => array(
                'title' => __('Handling of meeting type Temporary Closure', 'aa-berlin-addons'),

                'fields' => array(
                    array(
                        'title' => __('Disable map on meeting detail', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'disable_map_if_tc',
                    ),

                    array(
                        'title' => __('Text of map overlay', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'This meeting is suspended until further notice.',
                        'text' => 'Should describe why the map is rendered inaccessible; no double-quotes',
                        'id' => 'disable_map_text_tc',
                    ),
                ),
            ),

            'misc' => array(
                'title' => __('Miscellaneous', 'aa-berlin-addons'),

                'fields' => array(
                    array(
                        'title' => __('Wrap leading link of single-post Latest Posts in h2', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'wrap_single_entry_links_with_h2',
                    ),

                    array(
                        'title' => __('Add custom set of type flags', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'M,W,TC,ONL',
                        'text' => 'List of meeting types (keys only) that should be promoted as textual flags next to the meeting name; separated by comma',
                        'id' => 'custom_type_flags_add',
                    ),

                    array(
                        'title' => __('Remove from set of type flags', 'aa-berlin-addons'),
                        'type' => 'text',
                        'text' => 'List of meeting types (keys only) that should never be promoted as textual flags next to the meeting name; separated by comma; use this to switch certain types off',
                        'id' => 'custom_type_flags_remove',
                    ),
                ),
            ),
        )
    ),
);

$option_page = new RationalOptionPages($pages);

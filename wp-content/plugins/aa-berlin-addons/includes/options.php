<?php

if (!class_exists('RationalOptionPages')) {
    require __DIR__ . '/RationalOptionPages/RationalOptionPages.php';
}

$pages = array(
    'aa_berlin_addons_options' => array(
        'page_title' => __('AA Berlin Addons', 'aa-berlin-addons'),
        'menu_slug' => 'aa_berlin_addons_options',
        'parent_slug' => 'options-general.php',
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
                        'title' => __('Prepend stream icons to zoom.us links', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'prepend_stream_icons',
                    ),

                    array(
                        'title' => __('Disable zoom.us links if meeting is not scheduled', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'disable_outside_schedule',
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
                        'id' => 'insert_notices',
                    ),

                    array(
                        'title' => __('Warning pattern', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => '^IMPORTANT:',
                        'id' => 'warning_pattern',
                    ),

                    array(
                        'title' => __('Success pattern', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => '^UPDATE:',
                        'id' => 'success_pattern',
                    ),

                    array(
                        'title' => __('Info pattern', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => '^INFO:',
                        'id' => 'info_pattern',
                    ),
                ),
            ),
        )
    ),
);

$option_page = new RationalOptionPages($pages);

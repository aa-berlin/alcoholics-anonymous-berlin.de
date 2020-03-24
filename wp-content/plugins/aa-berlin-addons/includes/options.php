<?php

if (!class_exists('RationalOptionPages')) {
    require __DIR__ . '/RationalOptionPages/RationalOptionPages.php';
}

$pages = array(
    'aa_berlin_addons_options' => array(
        'page_title' => __('AA Berlin Addons', 'aa-berlin-addons'),
        'parent_slug' => 'options-general.php',
        'icon_url' => 'dashicons-carrot',
        'position' => 61,
        'sections' => array(

            'links' => array(
                'title' => __('Link substitution', 'aa-berlin-addons'),
                'text' => '<p>' . __('Configures the automatic substitution of anchor elements for fully qualified secure urls.', 'aa-berlin-addons') . '</p>',

                'fields' => array(
                    'insert_links' => array(
                        'title' => __('Insert links', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                    ),

                    'prepend_stream_icons' => array(
                        'title' => __('Prepend stream icons to zoom.us links', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                    ),

                    'disable_outside_schedule' => array(
                        'title' => __('Disable zoom.us links if meeting is not scheduled', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                    ),
                ),
            ),

            'automatic_notices' => array(
                'title' => __('Automatic Notices', 'aa-berlin-addons'),
                'text' => '<p>' . __('Configures the automatic insertion of highlighting notices in paragraphs that do not contain any other tags; e.g. in the meeting notes on the meeting detail.', 'aa-berlin-addons') . '</p>',

                'fields' => array(
                    'insert_notices' => array(
                        'title' => __('Insert notices', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                    ),

                    'warning_prefix' => array(
                        'title' => __('Warning prefix', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'IMPORTANT:',
                    ),

                    'success_prefix' => array(
                        'title' => __('Success prefix', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'UPDATE:',
                    ),

                    'info_prefix' => array(
                        'title' => __('Info prefix', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'INFO:',
                    ),
                ),
            ),
        )
    ),
);

$option_page = new RationalOptionPages($pages);

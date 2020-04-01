<?php

$pages = array(
    'aa_berlin_zoombot_options' => array(
        'page_title' => __('Zoombot', 'aa-berlin-zoombot'),
        'menu_slug' => 'aa_berlin_zoombot_options',
        'parent_slug' => 'aa_berlin_addons_options',
        'position' => 10,
        'sections' => array(
            'auth' => array(
                'title' => __('Authentication', 'aa-berlin-zoombot'),
                'text' => '<p>' . __('Configures credentials so this Zoombot can access your Zoom account.', 'aa-berlin-zoombot') . '</p>',

                'fields' => array(
                    array(
                        'title' => __('Zoom App Client ID', 'aa-berlin-zoombot'),
                        'type' => 'text',
                        'value' => '',
                        'text' => __('Find it in your Zoom App settings under "App Credentials"', 'aa-berlin-zoombot'),
                        'id' => 'zoom_client_id',
                    ),

                    array(
                        'title' => __('Zoom App Client Secret', 'aa-berlin-zoombot'),
                        'type' => 'text',
                        'value' => '',
                        'text' => __('Find it in your Zoom App settings under "App Credentials"', 'aa-berlin-zoombot'),
                        'id' => 'zoom_client_secret',
                    ),

                    array(
                        'title' => __('Zoom Bot JID', 'aa-berlin-zoombot'),
                        'type' => 'text',
                        'value' => '',
                        'text' => __('Find it in your Zoom App settings under "Features", once you have configured them', 'aa-berlin-zoombot'),
                        'id' => 'zoom_bot_jid',
                    ),

                    array(
                        'title' => __('Zoom Verification Token', 'aa-berlin-zoombot'),
                        'type' => 'text',
                        'value' => '',
                        'text' => __('Find it in your Zoom App settings under "Features", once you have configured them', 'aa-berlin-zoombot'),
                        'id' => 'zoom_verification_token',
                    ),
                ),
            ),
        )
    ),
);

$option_page = new RationalOptionPages($pages);

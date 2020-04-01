<?php

/**
 * Plugin Name: AA Berlin Zoombot
 * Author: AA Berlin <ecomm.berlin@aamail.org>
 * Description: Allows for integration with Zoom online meetings providing e.g. coin ceremony features.
 * Text Domain: aa-berlin-zoombot
 * Domain Path: /languages/
 * Version: 0.1.0
 */

define('AA_BERLIN_ZOOMBOT_VERSION', '0.1.0');

register_activation_hook(__FILE__, 'aa_berlin_zoombot_activate');
add_action('init', 'aa_berlin_zoombot_init');
add_action('rest_api_init', 'aa_berlin_zoombot_rest_api_init');

function aa_berlin_zoombot_options($key = null) {
    $options = get_option('aa_berlin_zoombot_options', array());

    if (!$key) {
        return $options;
    }

    if (isset($options[$key])) {
        return $options[$key];
    }

    return null;
}

function aa_berlin_zoombot_activate() {
    if (!function_exists('aa_berlin_addons_init')) {
        deactivate_plugins(plugin_basename(__FILE__ ));

        wp_die(
            __('Please install and activate AA Berlin Addons before activating AA Berlin Zoombot.', 'aa-berlin-zoombot'),
            'Plugin dependency check',
            array('back_link' => true)
        );

        return;
    }
}

function aa_berlin_zoombot_init() {
    require __DIR__ . '/includes/options.php';
}

function aa_berlin_zoombot_rest_api_init() {
    $namespace = 'aa-berlin-zoombot/v1';

    register_rest_route(
        $namespace,
        '/authorize',
        array(
            'methods'  => WP_REST_Server::READABLE,
            'callback' => 'aa_berlin_zoombot_route_authorize',
        )
    );

    // TODO: make a top-level route, text-only
    register_rest_route(
        $namespace,
        '/zoomverify/verifyzoom.html',
        array(
            'methods'  => WP_REST_Server::READABLE,
            'callback' => 'aa_berlin_zoombot_route_zoomverify',
        )
    );

    register_rest_route(
        $namespace,
        '/deauthorize',
        array(
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => 'aa_berlin_zoombot_route_deauthorize',
        )
    );
}

function aa_berlin_zoombot_route_authorize() {
    $url = 'https://zoom.us/launch/chat?jid=robot_' . aa_berlin_zoombot_options('zoom_bot_jid');

    $response = rest_ensure_response([
        'location' => $url,
        'status' => 302,
    ]);

    $response->set_status(302);
    $response->header('Location', $url);

    return $response;
}

function aa_berlin_zoombot_route_deauthorize(WP_REST_Request $request) {
    $response = new WP_REST_Response();
    $response->set_status(200);

    if ($request->get_header('Authorization') !== aa_berlin_addons_options('zoom_verification_token')) {
        $response->set_status(401);
        $response->set_data([
            'status' => 401,
            'message' => 'Authorization did not match zoom_verification_token',
        ]);

        return $response;
    }

    $compliance_url = 'https://api.zoom.us/oauth/data/compliance';

    $body = $request->get_json_params();
    $payload = $body['payload'];

    $compliance_body = [
        'client_id' => $payload['client_id'],
        'user_id' => $payload['user_id'],
        'account_id' => $payload['account_id'],
        'deauthorization_event_received' => $payload,
    ];

    $zoom_client_id = aa_berlin_addons_options('zoom_client_id');
    $zoom_client_secret = aa_berlin_addons_options('zoom_client_secret');

    $compliance_response = wp_remote_post(
        $compliance_url,
        [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($zoom_client_id . ':' . $zoom_client_secret),
                'Cache-Control' => 'no-cache',
            ],
            'body' => json_encode($compliance_body),
        ]
    );

    $response->set_data($compliance_response);

    return $response;
}

function aa_berlin_zoombot_route_zoomverify() {
    return rest_ensure_response(aa_berlin_zoombot_options('zoom_verification_token'));
}

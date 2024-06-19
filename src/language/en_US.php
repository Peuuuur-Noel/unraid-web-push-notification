<?php

/*
 * This file is part of Web Push Notification Agent plugin for Unraid.
 *
 * (c) Peuuuur Noel
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

$wpm_lang = [
    // web-push-notification.page
    'help_agent_function' => <<<'EOD'
First, request permission, then register to enable push notification on this browser.
You need to browse this page with each devices you want to use push notification and register them.

Computer (desktop/laptop): you must keep your browser open to receive notifications. If it's closed, notifications will be pending and arrive when you start it.
iPhone/iPad: you need to add this page to your device's Home Screen to enable push notifications.
EOD,
    'registered_devices'              => 'Registered devices:',
    'button_show_list'                => 'Show list',
    'button_hide_list'                => 'Hide list',
    'button_show_advanced_settings'   => 'Show advanced settings',
    'button_hide_advanced_settings'   => 'Hide advanced settings',
    'vapid_public_key'                => 'VAPID public key:',
    'vapid_private_key'               => 'VAPID private key:',
    'help_vapid_public_key'           => 'Key needed to encrypt and send notification to push notification service.',
    'help_vapid_private_key'          => 'Key needed to authenticate and send notification to push notification service.',
    'must_be_generated'               => 'Must be generated',
    'button_generate_vapid_keys'      => 'Generate VAPID keys',
    'help_generate_vapid_keys'        => 'Generating a new VAPID will revoke registered devices. You will need to register them again.',
    'permission'                      => 'Permission:',
    'permission_status'               => 'Click the button below to request permission and register to push notification or check status.',
    'help_permission'                 => 'Click the button below to request permission and register to push notification or check status. You need to browse this page with each devices you want to use push notification and click on the button below.',
    'error'                           => 'Error:',
    'button_request_register'         => 'Request permission and Register',
    'help_notifications_silent_level' => '<strong>Silent notifications:</strong> check if you want notifications to be silent (no vibrate/sound) when received.<br><br> <strong>Lowest notification level:</strong> lowest notification level to send to the device.<br> Available levels will depend on the levels selected in the "Agents" column in the "Notification Entity" option at the top of this page.',
    'http_plugin_disabled'            => 'Works only over a trusted HTTPS connection/certificate. Goto <a href="/Settings/ManagementAccess">Management Access</a>.',
    'test_event'                      => 'Unraid Status',
    'test_subject'                    => 'Testing',
    'test_description'                => 'Is it working?',

    // actions.php
    'error_msg_default'    => 'Maybe or not OK, but something happened...',
    'device_removed'       => 'Device removed.',
    'no_message_to_push'   => 'No notification to push.',
    'no_registered_device' => 'No registered device to push notification to.',
    'device_not_found'     => 'Device not found.',
    'push_to_x_devices'    => 'Notification pushed to %1$d device%2$s.',
    'unknown_action'       => 'Unknown action.',

    // web-push-notification.js
    'no_service_worker_support'             => 'No browser support for service worker.',
    'no_push_api_support'                   => 'No browser support for Push API.',
    'safari_ios_home_screen'                => 'You need to add this page to your device\'s Home Screen to enable push notifications.',
    'permissions_granted_sw_not_registered' => 'Permission granted but service worker not registered.',
    'permissions_granted_no_subscription'   => 'Permission granted but not registered to push service.',
    'permissions_granted_registered'        => 'Permission granted and registered to push notification.',
    'error_retrieving_subscription'         => 'Error thrown while retrieving push notification subscription:',
    'error_retrieving_registrations'        => 'Error thrown while retrieving service workers registrations:',
    'permissions_denied'                    => 'Permission denied for notification. Allow notification for this website in browser preferences.',
    'permissions_not_granted'               => 'Permission not granted for notification.',
    'error_subscribing'                     => 'Error thrown while subscribing to push notification.',
    'error_registering'                     => 'Error thrown while registering service worker.',
    'error_unregistering'                   => 'Error thrown while unregistering service worker:',
    'error_unsubscribing'                   => 'Error thrown while unsubscribing from push notification:',
    'action'                                => 'Action',
    'device_info'                           => 'Device info',
    'notification_settings'                 => 'Notification settings',
    'device_name'                           => 'Name:',
    'date'                                  => 'Date:',
    'user_agent'                            => 'User Agent:',
    'ip_address'                            => 'IP Address:',
    'loading'                               => 'Loading...',
    'no_devices'                            => 'No device.',
    'test'                                  => 'Test',
    'remove'                                => 'Remove',
    'remove_device'                         => 'Remove this device?',
    'current_device'                        => 'Current device',
    'rename'                                => 'Rename',
    'notification_level_lowest'             => 'Lowest notification level:',
    'notification_level_default'            => 'Default',
    'notification_level_notices'            => 'Notices',
    'notification_level_warnings'           => 'Warnings',
    'notification_level_alerts'             => 'Alerts / Unraid OS update',
    'silent_notifications'                  => 'Silent notifications:',
    'unsupported_firefox'                   => 'Not supported on Firefox',
    'save'                                  => 'Save',
    'registration_progress'                 => 'Doing a lot of stuff...',
    'generate_vapid_keys'                   => 'Do you want to generate VAPID keys?',
    'error_while_generating'                => 'Error while generating',
];

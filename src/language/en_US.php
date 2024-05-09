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
    // index.page
    'help_agent_function' => <<<'EOD'
First, request permission, then register to enable push notification on this browser.

You need to browse this page with each devices you want to use push notification and register them.
You must keep your browser open to receive notifications. If it's closed, notifications will be pending and arrive when you start it.

On mobile, you may need to add this page to your device's Home Screen to enable push notifications.
EOD,
    'button_show_registered_devices' => 'Show registered devices',
    'button_hide_registered_devices' => 'Hide registered devices',
    'button_show_advanced_settings' => 'Show advanced settings',
    'button_hide_advanced_settings' => 'Hide advanced settings',
    'vapid_public_key' => 'VAPID public key:',
    'vapid_private_key' => 'VAPID private key:',
    'help_vapid_public_key' => 'Key needed to encrypt and send notification to push notification service.',
    'help_vapid_private_key' => 'Key needed to authenticate your message and send notification to push notification service.',
    'must_be_generated' => 'Must be generated',
    'button_generate_vapid_keys' => 'Generate VAPID keys',
    'help_generate_vapid_keys' => 'Generating a new VAPID will revoke registered devices. You will need to register them again.',
    'permission' => 'Permission:',
    'permission_status' => 'Click the button below to request permission and register to push notification or check status.',
    'help_permission' => 'Click the button below to request permission and register to push notification or check status. You need to browse this page with each devices you want to use push notification and click on the button below.',
    'error' => 'Error:',
    'button_request_register' => 'Request permission and Register',
    'silent_notification' => 'Silent notification:',
    'help_silent_notification' => 'Select which notification levels you want them to be silent (no vibrate/sound) when received.',

    // actions.php
    'error_msg_default' => 'Maybe or not OK, but something happened...',
    'device_removed' => 'Device removed.',
    'no_message_to_push' => 'No message to push.',
    'no_registered_device' => 'No registered device to push notification to.',
    'push_to_x_devices' => 'Push to %1$d device%2$s.',
    'unknown_action' => 'Unknown action.',

    // index.js
    'no_service_worker_support' => 'No Service Worker browser support.',
    'no_push_api_support' => 'No Push API browser support.',
    'permissions_granted_not_registered' => 'Permissions granted but not registered. Register to push notification.',
    'permissions_granted_registered' => 'Permissions granted and registered to push notification.',
    'error_retrieving_subscription' => 'Error thrown while retrieving push notification subscription:',
    'error_retrieving_registrations' => 'Error thrown while retrieving service workers registrations:',
    'permissions_denied' => 'Permissions denied for notification. Allow notification for this website in browser preferences.',
    'permissions_not_granted' => 'Permissions not granted for notification.',
    'error_subscribing' => 'Error thrown while subscribing to push notification.',
    'error_registering' => 'Error thrown while registering service worker.',
    'error_unregistering' => 'Error thrown while unregistering service worker:',
    'error_unsubscribing' => 'Error thrown while unsubscribing from push notification:',
    'action' => 'Action',
    'date' => 'Date',
    'user_agent' => 'User Agent',
    'ip_address' => 'IP Address',
    'loading' => 'Loading...',
    'no_devices' => 'No devices.',
    'remove' => 'Remove',
    'remove_device' => 'Remove this device?',
    'current_device' => 'Current device',
    'registration_progress' => 'Doing a lot of stuff...',
    'generate_vapid_keys' => 'Do you want to generate VAPID keys?',
    'error_while_generating' => 'Error while generating',
];

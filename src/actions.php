<?php

/*
 * This file is part of Web Push Notification Agent plugin for Unraid.
 *
 * (c) Peuuuur Noel
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require_once __DIR__ . '/include/loader.php';

use WebPushNotification\Libraries\ExceptionToConsole;
use WebPushNotification\Libraries\Push;
use WebPushNotification\Models\Config;
use WebPushNotification\Models\Devices;
use WebPushNotification\Models\Notification;
use WebPushNotification\Models\VAPID;

$out = [
    'errno'  => WPN_LEVEL_WARNING,
    'errmsg' => wpm__('error_msg_default'),
    'data'   => [],
];

try {
    $action  = null;
    $options = [];
    if (PHP_SAPI == 'cli' && $argc <= 1) {
        wpm_usage();

        exit;
    }
    if (PHP_SAPI == 'cli' && $argc > 1) {
        $action  = 'push';
        $options = getopt('e:i:s:d:c:l:t:o:', ['event:', 'importance:', 'subject:', 'description:', 'content:', 'link:', 'timestamp:', 'sound:']);
    } elseif (isset($_GET['action'])) {
        $action  = $_GET['action'];
        $options = $_GET['options'];
    }

    switch ($action) {
        case 'config':
            $config = new Config();

            if (isset($_POST['wpn-enable'])) {
                if ('enable' == $_POST['wpn-enable']) {
                    $config->enableAgent();
                } elseif ('disable' == $_POST['wpn-enable']) {
                    $config->disableAgent();
                }
            }

            $out['errno']  = WPN_NO_ERROR;
            $out['errmsg'] = 'ok';

            break;

        case 'get_csrf_token':
            $stateVar = @parse_ini_file(WPN_DOCROOT . 'state/var.ini');

            $out['errno']  = WPN_NO_ERROR;
            $out['errmsg'] = 'ok';
            $out['data']   = [
                'csrf_token' => $stateVar['csrf_token'],
            ];

            break;

        case 'generate_vapid':
            // Remove any registered devices
            $devices     = new Devices();
            $devicesList = $devices->getAll();
            if ($devicesList) {
                $notification = new Notification();
                $notification->setTitle(gethostname());
                $notification->setData([
                    'type' => 'remove',
                ]);

                $push = new Push();
                $push->queueDevices($notification, $devicesList);
                $count = $push->send();

                $devices->clear();
            }

            $vapid = new VAPID();
            $vapid->generateKeys();

            $out['errno']  = WPN_NO_ERROR;
            $out['errmsg'] = 'ok';
            $out['data']   = [
                'publicKey'  => $vapid->getPublicKey(),
                'privateKey' => $vapid->getPrivateKey(),
            ];

            break;

        case 'get_vapid_public_key':
            $vapid     = new VAPID();
            $publicKey = $vapid->getPublicKey();

            if (!$publicKey) {
                throw new ExceptionToConsole('[ACTIONS] VAPID public key not found', WPN_LEVEL_ERROR);
            }

            $out['errno']  = WPN_NO_ERROR;
            $out['errmsg'] = 'ok';
            $out['data']   = [
                'publicKey' => $publicKey,
            ];

            break;

        case 'save_device':
            $subscription = json_decode($_POST['subscription'] ?? '', true) ?: [];

            if (!$subscription) {
                throw new ExceptionToConsole('[ACTIONS] Error Processing Request', WPN_LEVEL_ERROR);
            }

            $devices = new Devices();

            if (!$devices->register($subscription, $_SERVER['HTTP_USER_AGENT'], $_SERVER['REMOTE_ADDR'])) {
                throw new ExceptionToConsole('[ACTIONS] Unable to register device', WPN_LEVEL_ERROR);
            }

            $out['errno']  = WPN_NO_ERROR;
            $out['errmsg'] = 'ok';

            break;

        case 'set_device_name':
            $endpoint = $_POST['endpoint'] ?: null;
            $name     = $_POST['name'] ?? null;

            if (!$endpoint) {
                throw new ExceptionToConsole('[ACTIONS] Error Processing Request', WPN_LEVEL_ERROR);
            }

            $devices = new Devices();
            $devices->setDeviceName($endpoint, $name);

            $out['errno']  = WPN_NO_ERROR;
            $out['errmsg'] = 'ok';

            break;

        case 'set_device_notifications':
            $endpoint            = $_POST['endpoint'] ?: null;
            $silentNotifications = json_decode($_POST['silent-notifications'] ?? '', true) ?: false;
            $notificationLevel   = $_POST['notification-level'] ?? null;

            if (!$endpoint) {
                throw new ExceptionToConsole('[ACTIONS] Error Processing Request', WPN_LEVEL_ERROR);
            }

            $devices = new Devices();
            $devices->setDeviceNotifications($endpoint, $silentNotifications, $notificationLevel);

            $out['errno']  = WPN_NO_ERROR;
            $out['errmsg'] = 'ok';

            break;

        case 'remove_device':
            $endpoint = $_POST['endpoint'] ?: null;

            $devices = new Devices();
            $device  = $devices->getByEndpoint($endpoint);

            $count = -1;
            if (isset($_POST['remote_delete']) && $_POST['remote_delete']) {
                $notification = new Notification();
                $notification->setTitle(gethostname());
                $notification->setData([
                    'type' => 'remove',
                ]);

                $push = new Push();
                $push->queueDevice($notification, $device);
                $count = $push->send();
            }

            $return = $devices->unregister($endpoint);

            $out['errno']  = WPN_NO_ERROR;
            $out['errmsg'] = wpm__('device_removed');

            break;

        case 'get_devices_list':
            $devices = new Devices();
            $devices->getAll();

            $out['errno']  = WPN_NO_ERROR;
            $out['errmsg'] = 'ok';
            $out['data']   = $devices->getAll();

            break;

        case 'test':
            $endpoint = $_POST['endpoint'] ?: null;

            if (!$endpoint) {
                throw new ExceptionToConsole('[ACTIONS] Error Processing Request', WPN_LEVEL_ERROR);
            }

            $isTest = true;

            $options['event']       = wpm__('test_event');
            $options['importance']  = 'warning';
            $options['subject']     = wpm__('test_subject');
            $options['description'] = wpm__('test_description');

            $devices     = new Devices();
            $device      = $devices->getByEndpoint($endpoint);
            $devicesList = [];
            if ($device) {
                $devicesList[] = $device;
            } else {
                $out['errno']  = WPN_LEVEL_ERROR;
                $out['errmsg'] = wpm__('device_not_found');

                break;
            }

            // no break
        case 'push':
            $event       = $options['e'] ?? $options['event'] ?? null;
            $importance  = $options['i'] ?? $options['importance'] ?? 'unknown';
            $subject     = $options['s'] ?? $options['subject'] ?? null;
            $description = $options['d'] ?? $options['description'] ?? null;
            $content     = $options['c'] ?? $options['content'] ?? null;
            $link        = $options['l'] ?? $options['link'] ?? null;
            $timestamp   = $options['t'] ?? $options['timestamp'] ?? 'now';
            $sound       = $options['o'] ?? $options['sound'] ?? '';

            if (!$description) {
                $out['errno']  = WPN_NO_ERROR;
                $out['errmsg'] = wpm__('no_message_to_push');

                break;
            }

            if ('unraid' == $importance) {
                $importance = 'alert';
            }

            if (!isset($devicesList) || !$devicesList) {
                $devices     = new Devices();
                $devicesList = $devices->getAll();
            }

            if (!$devicesList) {
                $out['errno']  = WPN_NO_ERROR;
                $out['errmsg'] = wpm__('no_registered_device');

                break;
            }

            /*
             * Message format:
             * [LEVEL] EVENT - SUBJECT
             * DESCRIPTION
             * CONTENT
             */
            $tempBody    = [];
            $tempMessage = [];
            if ($event) {
                $tempMessage[] = $event;
            }
            if ($subject) {
                $tempMessage[] = $subject;
            }
            if ($tempMessage) {
                $tempBody[] = implode(' - ', $tempMessage);
            }

            $tempMessage = [];
            if ($description) {
                $tempMessage[] = $description;
            }
            if ($content) {
                $tempMessage[] = $content;
            }
            if ($tempMessage) {
                $tempBody[] = implode(PHP_EOL, $tempMessage);
            }

            $errorLevel = WPN_MESSAGE_ERROR_LEVEL[$importance] ?: WPN_MESSAGE_ERROR_LEVEL['unknown'];
            $body       = '[' . $importance . '] ' . implode(PHP_EOL, $tempBody);

            if (is_numeric($timestamp) && 10 == strlen($timestamp)) {
                $timestamp *= 1_000;
            } elseif (is_numeric($timestamp) && 10 != strlen($timestamp)) {
                $timestamp = 'now';
            } elseif ($timestamp && !is_numeric($timestamp)) {
                $timestamp = date_timestamp_get(date_create($timestamp)) * 1_000;
            }

            $notification = new Notification();
            $notification->setTitle(gethostname());
            $notification->setBody($body);
            $notification->setIcon($errorLevel['icon']);
            $notification->setBadge(WPN_NOTIFICATION_BADGE);
            $notification->setTimestamp($timestamp);
            $notification->setData(['type' => 'version', 'version' => WPN_SW_VERSION]);
            $notification->setSound($sound);

            if ($link) {
                $notification->setData(['type' => 'url', 'url' => $link]);
            }

            $push = new Push();

            foreach ($devicesList as $device) {
                $notificationLevel = $device->getNotificationLevel();

                if (('' !== $notificationLevel || null !== $notificationLevel) && !isset($isTest) && $errorLevel['errorno'] < $notificationLevel) {
                    continue;
                }

                $notification->setSilent($device->getSilentNotifications());

                $push->queueDevice($notification, $device);
            }

            $count = $push->send();

            if (PHP_SAPI == 'cli' && $argc > 1) {
                exit;
            }

            $out['errno']  = WPN_NO_ERROR;
            $out['errmsg'] = wpm__('push_to_x_devices', $count, $count > 1 ? 's' : '');

            break;

        default:
            $out['errno']  = WPN_NO_ERROR;
            $out['errmsg'] = wpm__('unknown_action');

            break;
    }
} catch (ExceptionToConsole $e) {
    $out['errno']  = $e->getCode();
    $out['errmsg'] = $e->getMessage();
} finally {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($out);
}

<?php
require_once 'include/loader.php';

use WebPushNotification\Libraries\ExceptionToConsole;
use WebPushNotification\Libraries\Push;
use WebPushNotification\Models\Devices;
use WebPushNotification\Models\Notification;
use WebPushNotification\Models\VAPID;

$out = [
    'errno' => WPN_LEVEL_WARNING,
    'errmsg' => wpm__('Maybe or not OK, but something happened...'),
    'data' => [],
];

try {
    $action = null;
    if (PHP_SAPI == 'cli' && $argc <= 1) {
        wpm_usage();
        exit;
    } else if (PHP_SAPI == 'cli' && $argc > 1) {
        $options = getopt('e:i:s:d:c:l:t:o:', ['event:', 'importance:', 'subject:', 'description:', 'content:', 'link:', 'timestamp:', 'sound:']);
        $action = 'push';
    } else if (isset($_GET['action'])) {
        $action = $_GET['action'];
    }

    switch ($action) {
        case 'enable':
            if (!is_file(WPN_AGENT_PATH . '/agents/' . WPN_AGENT_NAME . '.sh') && !is_file(WPN_AGENT_PATH . '/agents-disabled/' . WPN_AGENT_NAME . '.sh')) {
                $agent = <<<'EOF'
#!/bin/bash
############
# Quick test with default values:
#   bash /boot/config/plugins/dynamix/notifications/agents/WebPushNotification.sh
# Quick test with values set through environment (all vars are optional)
#   EVENT="My Event" IMPORTANCE="alert" SUBJECT="My Subject" DESCRIPTION="My Description" CONTENT="My Message" LINK="/Dashboard" bash /boot/config/plugins/dynamix/notifications/agents/WebPushNotification.sh
# Full test of notification system (at least one param is required)
#   /usr/local/emhttp/webGui/scripts/notify -e "My Event" -s "My Subject" -d "My Description"  -m "My Message" -i "alert" -l "/Dashboard"
#
# If a notification does not go through, check the /var/log/notify_WebPushNotification file for hints
############
############
# Available fields from notification system
# HOSTNAME
# EVENT (notify -e)
# IMPORTANCE (notify -i)
# SUBJECT (notify -s)
# DESCRIPTION (notify -d)
# CONTENT (notify -m)
# LINK (notify -l)
# TIMESTAMP (seconds from epoch)
# SOUND (seconds from epoch)

SCRIPTNAME=$(basename "$0")
LOG="/var/log/notify_${SCRIPTNAME%.*}"

# for quick test, setup environment to mimic notify script
[[ -z "${EVENT}" ]] && EVENT='Unraid Status'
[[ -z "${IMPORTANCE}" ]] && IMPORTANCE='warning'
[[ -z "${SUBJECT}" ]] && SUBJECT='Testing'
[[ -z "${DESCRIPTION}" ]] && DESCRIPTION='Working?'
[[ -z "${CONTENT}" ]] && CONTENT=''
[[ -z "${LINK}" ]] && LINK=''
[[ -z "${TIMESTAMP}" ]] && TIMESTAMP=$(date +%s)
[[ -z "${SOUND}" ]] && SOUND=''

bash -c "php /usr/local/emhttp/plugins/web-push-notification/actions.php -e \"${EVENT}\" -i \"${IMPORTANCE}\" -s \"${SUBJECT}\" -d \"${DESCRIPTION}\" -c \"${CONTENT}\" -l \"${LINK}\" -t \"${TIMESTAMP}\" -o \"${SOUND}\""
EOF;
                if (!is_dir(WPN_AGENT_PATH . '/agents-disabled/'))
                    mkdir(WPN_AGENT_PATH . '/agents-disabled/', 0700, true);

                file_put_contents(WPN_AGENT_PATH . '/agents-disabled/' . WPN_AGENT_NAME . '.sh', $agent);
            }

            exec(WPN_DOCROOT . 'webGui/scripts/agent enable ' . WPN_AGENT_NAME . '.sh');

            $out['errno'] = WPN_NO_ERROR;
            $out['errmsg'] = 'ok';
            break;

        case 'disable':
            exec(WPN_DOCROOT . 'webGui/scripts/agent disable ' . WPN_AGENT_NAME . '.sh');

            $out['errno'] = WPN_NO_ERROR;
            $out['errmsg'] = 'ok';
            break;

        case 'test':
            exec(WPN_DOCROOT . 'webGui/scripts/agent test ' . WPN_AGENT_NAME . '.sh');

            $out['errno'] = WPN_NO_ERROR;
            $out['errmsg'] = 'ok';
            break;

        case 'get_csrf_token':
            $stateVar = @parse_ini_file(WPN_DOCROOT . 'state/var.ini');

            $out['errno'] = WPN_NO_ERROR;
            $out['errmsg'] = 'ok';
            $out['data'] = [
                'csrf_token' => $stateVar['csrf_token'],
            ];
            break;

        case 'generate_vapid':
            // Remove any registered devices
            $devices = new Devices();
            $devicesList = $devices->getAll();
            if ($devicesList) {
                $notification = new Notification();
                $notification->setTitle(gethostname());
                $notification->setData([
                    'type' => 'remove'
                ]);

                $push = new Push();
                $push->queueDevices($notification, $devicesList);
                $count = $push->send();

                $devices->clear();
            }

            $vapid = new VAPID();
            $vapid->generateKeys();

            $out['errno'] = WPN_NO_ERROR;
            $out['errmsg'] = 'ok';
            $out['data'] = [
                'publicKey' => $vapid->getPublicKey(),
                'privateKey' => $vapid->getPrivateKey(),
            ];
            break;

        case 'get_vapid_public_key':
            $vapid = new VAPID();
            $publicKey = $vapid->getPublicKey();

            if (!$publicKey)
                throw new ExceptionToConsole('[ACTIONS] VAPID public key not found', WPN_LEVEL_ERROR);

            $out['errno'] = WPN_NO_ERROR;
            $out['errmsg'] = 'ok';
            $out['data'] = [
                'publicKey' => $publicKey,
            ];
            break;

        case 'save_device':
            $subscription = json_decode($_POST['subscription'] ?? '', true) ?: [];

            // If no data or not JSON, throw an exception
            if (!$subscription)
                throw new ExceptionToConsole('[ACTIONS] Error Processing Request', WPN_LEVEL_ERROR);

            $devices = new Devices();

            if (!$devices->register($subscription, $_SERVER['HTTP_USER_AGENT'], $_SERVER['REMOTE_ADDR']))
                throw new ExceptionToConsole('[ACTIONS] Unable to register device', WPN_LEVEL_ERROR);

            $out['errno'] = WPN_NO_ERROR;
            $out['errmsg'] = 'ok';
            break;

        case 'remove_device':
            $endpoint = json_decode($_POST['subscription'] ?? '', true) ?: null;

            $devices = new Devices();
            $device = $devices->getByEndpoint($endpoint);

            $count = -1;
            if (isset($_POST['remote_delete']) && $_POST['remote_delete']) {
                $notification = new Notification();
                $notification->setTitle(gethostname());
                $notification->setData([
                    'type' => 'remove'
                ]);

                $push = new Push();
                $push->queueDevice($notification, $device);
                $count = $push->send();
            }

            $return = $devices->unregister($endpoint);

            $out['errno'] = WPN_NO_ERROR;
            $out['errmsg'] = wpm__('Device removed.');
            break;

        case 'get_devices_list':
            $devices = new Devices();
            $devices->getAll();

            $out['errno'] = WPN_NO_ERROR;
            $out['errmsg'] = 'ok';
            $out['data'] = $devices->getAll();
            break;

        case 'push':
            $event = $options['e'] ?? $options['event'] ?? null;
            $importance = $options['i'] ?? $options['importance'] ?? 'unknown';
            $subject = $options['s'] ?? $options['subject'] ?? null;
            $description = $options['d'] ?? $options['description'] ?? null;
            $content = $options['c'] ?? $options['content'] ?? null;
            $link = $options['l'] ?? $options['link'] ?? null;
            $timestamp = $options['t'] ?? $options['timestamp'] ?? 'now';
            $sound = $options['o'] ?? $options['sound'] ?? '';

            if (!$description) {
                $out['errno'] = WPN_NO_ERROR;
                $out['errmsg'] = wpm__('No message to push.');
                break;
            }

            $devices = new Devices();
            $devicesList = $devices->getAll();

            if (!$devicesList) {
                $out['errno'] = WPN_NO_ERROR;
                $out['errmsg'] = wpm__('No registered device to push notification to.');
                break;
            }

            /*
             * Message formating:
             * [LEVEL] EVENT - SUBJECT
             * DESCRIPTION
             * CONTENT
             */
            $temp_body = [];
            $temp_message = [];
            if ($event)
                $temp_message[] = $event;
            if ($subject)
                $temp_message[] = $subject;
            if ($temp_message)
                $temp_body[] = implode(' - ', $temp_message);

            $temp_message = [];
            if ($description)
                $temp_message[] = $description;
            if ($content)
                $temp_message[] = $content;
            if ($temp_message)
                $temp_body[] = implode(PHP_EOL, $temp_message);

            $error_level = WPN_MESSAGE_ERROR_LEVEL[$importance] ?: WPN_MESSAGE_ERROR_LEVEL['unknown'];
            $body = '[' . $importance . '] ' . implode(PHP_EOL, $temp_body);

            if (is_numeric($timestamp) && strlen($timestamp) == 10)
                $timestamp *= 1000;
            else if (is_numeric($timestamp) && strlen($timestamp) != 10)
                $timestamp = 'now';
            else if ($timestamp && !is_numeric($timestamp))
                $timestamp = date_timestamp_get(date_create($timestamp)) * 1000;

            $notification = new Notification();
            $notification->setTitle(gethostname());
            $notification->setBody($body);
            $notification->setIcon($error_level['icon']);
            $notification->setBadge(WPN_NOTIFICATION_BADGE);
            $notification->setTimestamp($timestamp);
            $notification->setData(['type' => 'version', 'version' => WPN_SW_VERSION,]);
            $notification->setSound($sound);
            // Silent normal notification
            $notification->setSilent($error_level['errorno'] < WPN_LEVEL_WARNING);

            if ($link)
                $notification->setData(['type' => 'url', 'url' => $link]);

            $push = new Push();
            $push->queueDevices($notification, $devicesList);
            $count = $push->send();

            $out['errno'] = WPN_NO_ERROR;
            $out['errmsg'] = wpm__('Push to %1$d device%2$s.', $count, $count > 1 ? 's' : '');

            if (PHP_SAPI == 'cli' && $argc > 1)
                exit;
            break;

        default:
            $out['errno'] = WPN_NO_ERROR;
            $out['errmsg'] = wpm__('Unknown action. Doing nothing.');
            break;
    }
} catch (ExceptionToConsole $e) {
    $out['errno'] = $e->getCode();
    $out['errmsg'] = $e->getMessage();
} finally {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($out);
}

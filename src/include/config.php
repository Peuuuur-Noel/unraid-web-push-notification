<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$dynamixVar = @parse_ini_file('/boot/config/plugins/dynamix/dynamix.cfg');

define('WPN_DOCROOT', ($_SERVER['DOCUMENT_ROOT'] ?: '/usr/local/emhttp') . '/');
define('WPN_SW_VERSION', '2024.03.08');
define('WPN_AGENT_NAME', 'WebPushNotification');
define('WPN_AGENT_PATH', '/boot/config/plugins/dynamix/notifications');
define('WPN_USB_DATA_FOLDER_PATH', '/boot/config/plugins/web-push-notification/private/');
define('WPN_PATH', __DIR__ . '/../');
define('WPN_DATA_FOLDER_PATH', WPN_DOCROOT . 'state/plugins/web-push-notification/');
define('WPN_VAPID_FILENAME', 'vapid.json');
define('WPN_DEVICES_FILENAME', 'devices.json');
define('WPN_LOCALE', isset($dynamixVar['locale']) && $dynamixVar['locale'] ? $dynamixVar['locale'] : 'en_US');
define('WPN_NO_ERROR', 0);
define('WPN_LEVEL_WARNING', 1);
define('WPN_LEVEL_ERROR', 2);
define('WPN_LEVEL_UNKNOWN', -1);
define('WPN_NOTIFICATION_BADGE', 'https://raw.githubusercontent.com/Peuuuur-Noel/unraid-web-push-notification/master/plugin/unraid-icon.png');
define('WPN_MESSAGE_ERROR_LEVEL', [
    'normal' => [
        'errorno' => 0,
        'level' => 'info',
        // 'icon' => "https://account.unraid.net/assets/favicon.svg",
        'icon' => "https://craftassets.unraid.net/uploads/discord/notify-normal.png",
        'push_urgency' => 'normal',
    ],
    'warning' => [
        'errorno' => 1,
        'level' => 'warning',
        // 'icon' => "https://account.unraid.net/assets/favicon.svg",
        'icon' => "https://craftassets.unraid.net/uploads/discord/notify-warning.png",
        'push_urgency' => 'normal',
    ],
    'alert' => [
        'errorno' => 2,
        'level' => 'error',
        // 'icon' => "https://account.unraid.net/assets/favicon.svg",
        'icon' => "https://craftassets.unraid.net/uploads/discord/notify-alert.png",
        'push_urgency' => 'high',
    ],
    'unknown' => [
        'errorno' => -1,
        'level' => 'unknown',
        // 'icon' => "https://account.unraid.net/assets/favicon.svg",
        'icon' => "https://craftassets.unraid.net/uploads/discord/notify-warning.png",
        'push_urgency' => 'normal',
    ],
]);

<?php

/*
 * This file is part of Web Push Notification Agent plugin for Unraid.
 *
 * (c) Peuuuur Noel
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace WebPushNotification\Models;

use WebPushNotification\Libraries\ExceptionToConsole;

class Config implements \JsonSerializable
{
    private ?array $devices = [];
    private ?array $silent  = [];
    private ?array $vapid   = [];

    public function __construct()
    {
        if (file_exists(WPN_DATA_FOLDER_PATH . WPN_CONFIG_FILENAME)) {
            $this->readFromFile();
        }
    }

    public function enableAgent(): void
    {
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
[[ -z "${DESCRIPTION}" ]] && DESCRIPTION='Is it working?'
[[ -z "${CONTENT}" ]] && CONTENT=''
[[ -z "${LINK}" ]] && LINK=''
[[ -z "${TIMESTAMP}" ]] && TIMESTAMP=$(date +%s)
[[ -z "${SOUND}" ]] && SOUND=''

bash -c "php /usr/local/emhttp/plugins/web-push-notification/actions.php -e \"${EVENT}\" -i \"${IMPORTANCE}\" -s \"${SUBJECT}\" -d \"${DESCRIPTION}\" -c \"${CONTENT}\" -l \"${LINK}\" -t \"${TIMESTAMP}\" -o \"${SOUND}\""
EOF;
            if (!is_dir(WPN_AGENT_PATH . '/agents-disabled/')) {
                mkdir(WPN_AGENT_PATH . '/agents-disabled/', 0700, true);
            }

            $agent = preg_replace('/\r\n/', PHP_EOL, $agent);
            file_put_contents(WPN_AGENT_PATH . '/agents-disabled/' . WPN_AGENT_NAME . '.sh', $agent);
        }

        exec(WPN_DOCROOT . 'webGui/scripts/agent enable ' . WPN_AGENT_NAME . '.sh');
    }

    public function disableAgent(): void
    {
        exec(WPN_DOCROOT . 'webGui/scripts/agent disable ' . WPN_AGENT_NAME . '.sh');
    }

    public function setDevices(array $devices = []): void
    {
        $this->devices = $devices;
    }

    public function getDevices(): array
    {
        return $this->devices ?? [];
    }

    public function setSilent(array $silent = []): void
    {
        $this->silent = $silent;
    }

    public function getSilent(): array
    {
        return $this->silent ?? [];
    }

    public function setVapid(array $vapid = []): void
    {
        $this->vapid = $vapid;
    }

    public function getVapid(): array
    {
        return $this->vapid ?? [];
    }

    public function toArray(): array
    {
        return [
            'devices' => $this->devices,
            'silent'  => $this->silent,
            'vapid'   => $this->vapid,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function writeToFile(): bool
    {
        if (!is_dir(WPN_DATA_FOLDER_PATH)) {
            mkdir(WPN_DATA_FOLDER_PATH, 0700, true);
        }

        $return = file_put_contents(WPN_DATA_FOLDER_PATH . WPN_CONFIG_FILENAME, json_encode($this, JSON_PRETTY_PRINT));

        if (false === $return) {
            throw new ExceptionToConsole('[Config] Unable to write file "' . WPN_DATA_FOLDER_PATH . WPN_CONFIG_FILENAME . '"', WPN_LEVEL_ERROR);
        }

        // Copy .dat file to USB device to keep data after a reboot
        if (!is_dir(WPN_USB_FOLDER_PATH)) {
            mkdir(WPN_USB_FOLDER_PATH, 0700, true);
        }

        $return = copy(WPN_DATA_FOLDER_PATH . WPN_CONFIG_FILENAME, WPN_USB_FOLDER_PATH . WPN_CONFIG_FILENAME);

        if (false === $return) {
            throw new ExceptionToConsole('[Config] Unable to copy file to "' . WPN_USB_FOLDER_PATH . WPN_CONFIG_FILENAME . '"', WPN_LEVEL_ERROR);
        }

        return $return;
    }

    private function readFromFile(): bool
    {
        if (!file_exists(WPN_DATA_FOLDER_PATH . WPN_CONFIG_FILENAME)) {
            throw new ExceptionToConsole('[Config] File not found "' . WPN_DATA_FOLDER_PATH . WPN_CONFIG_FILENAME . '"', WPN_LEVEL_ERROR);
        }

        $file = file_get_contents(WPN_DATA_FOLDER_PATH . WPN_CONFIG_FILENAME);

        if (false === $file) {
            throw new ExceptionToConsole('[Config] Unable to read file "' . WPN_DATA_FOLDER_PATH . WPN_CONFIG_FILENAME . '"', WPN_LEVEL_ERROR);
        }

        $config        = json_decode($file, true) ?: [];
        $this->devices = $config['devices'];
        $this->silent  = $config['silent'];
        $this->vapid   = $config['vapid'];

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new ExceptionToConsole('[Config] JSON error: ' . json_last_error_msg(), WPN_LEVEL_ERROR);
        }

        return true;
    }
}

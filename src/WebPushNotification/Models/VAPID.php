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

use JsonSerializable;
use WebPushNotification\Libraries\ExceptionToConsole;

class VAPID implements JsonSerializable
{
    private ?array $vapid = [];

    public function __construct()
    {
        if (file_exists(WPN_DATA_FOLDER_PATH . WPN_VAPID_FILENAME)) {
            $this->readFromFile();
        }
    }

    public function generateKeys(): bool
    {
        $this->vapid = \Minishlink\WebPush\VAPID::createVapidKeys();

        if (!$this->vapid) {
            throw new ExceptionToConsole('[VAPID] Error while generating keys', WPN_LEVEL_ERROR);
        }

        return $this->writeToFile();
    }

    public function getPublicKey(): ?string
    {
        return $this->vapid['publicKey'] ?? null;
    }

    public function getPrivateKey(): ?string
    {
        return $this->vapid['privateKey'] ?? null;
    }

    private function readFromFile(): bool
    {
        if (!file_exists(WPN_DATA_FOLDER_PATH . WPN_VAPID_FILENAME)) {
            throw new ExceptionToConsole('[VAPID] File not found "' . WPN_DATA_FOLDER_PATH . WPN_VAPID_FILENAME . '"', WPN_LEVEL_ERROR);
        }

        $file = file_get_contents(WPN_DATA_FOLDER_PATH . WPN_VAPID_FILENAME);

        if ($file === false) {
            throw new ExceptionToConsole('[VAPID] Unable to read file "' . WPN_DATA_FOLDER_PATH . WPN_VAPID_FILENAME . '"', WPN_LEVEL_ERROR);
        }

        $this->vapid = json_decode($file, true) ?: [];

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ExceptionToConsole('[VAPID] JSON error: ' . json_last_error_msg(), WPN_LEVEL_ERROR);
        }

        return true;
    }

    private function writeToFile(): bool
    {
        if (!is_dir(WPN_DATA_FOLDER_PATH)) {
            mkdir(WPN_DATA_FOLDER_PATH, 0700, true);
        }
        $return = file_put_contents(WPN_DATA_FOLDER_PATH . WPN_VAPID_FILENAME, json_encode($this, JSON_PRETTY_PRINT));

        if ($return === false) {
            throw new ExceptionToConsole('[VAPID] Unable to write file "' . WPN_DATA_FOLDER_PATH . WPN_VAPID_FILENAME . '"', WPN_LEVEL_ERROR);
        }

        // Copy .dat file to USB device to keep data after a reboot
        if (!is_dir(WPN_USB_DATA_FOLDER_PATH)) {
            mkdir(WPN_USB_DATA_FOLDER_PATH, 0700, true);
        }
        $return = copy(WPN_DATA_FOLDER_PATH . WPN_VAPID_FILENAME, WPN_USB_DATA_FOLDER_PATH . WPN_VAPID_FILENAME);

        if ($return === false) {
            throw new ExceptionToConsole('[VAPID] Unable to copy file to "' . WPN_USB_DATA_FOLDER_PATH . WPN_VAPID_FILENAME . '"', WPN_LEVEL_ERROR);
        }

        return $return;
    }

    public function jsonSerialize(): mixed
    {
        return $this->vapid;
    }
}

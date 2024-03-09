<?php

namespace WebPushNotification\Models;

use JsonSerializable;
use WebPushNotification\Libraries\ExceptionToConsole;
use WebPushNotification\Models\Device;
use WebPushNotification\Models\Subscription;

class Devices implements JsonSerializable
{
    private ?array $devices = [];

    public function __construct()
    {
        if (file_exists(WPN_DATA_FOLDER_PATH . WPN_DEVICES_FILENAME))
            $this->readFromFile();
    }

    public function hasRegisteredDevices(): bool
    {
        return $this->devices ? true : false;
    }

    public function getAll(): ?array
    {
        return $this->devices;
    }

    public function getByEndpoint(string $endpoint): ?Device
    {
        return current(array_filter($this->devices, fn ($var) => $var?->getSubscription()?->getEndpoint() == $endpoint) ?? []) ?: null;
    }

    public function clear(): bool
    {
        $this->devices = [];

        return $this->writeToFile();
    }

    public function register(array $data = [], ?string $userAgent = null, ?string $ipAddress = null): ?bool
    {
        if (!isset($data['endpoint']) || !isset($data['keys']))
            throw new ExceptionToConsole('[DEVICES] Error on subscription data', WPN_LEVEL_ERROR);

        $subscription = new Subscription($data['endpoint'], $data['expirationTime'], $data['keys']);
        $endpoint = $subscription->getEndpoint();

        // Check if there is no duplicate
        $isDuplicate = false;
        if ($this->devices)
            $isDuplicate = $this->getByEndpoint($endpoint);

        if (!$isDuplicate) {
            // If no duplicate, append to file
            $device = new Device($subscription, date_format(date_create(), 'c'), $userAgent, $ipAddress);
            $this->devices = [...$this->devices, ...[$device]];

            return $this->writeToFile();
        }

        return true;
    }

    public function unregister(?string $endpoint = null): bool
    {
        if (!$endpoint)
            return false;

        $this->devices = array_values(array_filter($this->devices, fn ($var) => $var?->getSubscription()?->getEndpoint() != $endpoint) ?? []);

        return $this->writeToFile();
    }



    private function readFromFile(): bool
    {
        if (!file_exists(WPN_DATA_FOLDER_PATH . WPN_DEVICES_FILENAME))
            throw new ExceptionToConsole('[DEVICES] File not found "' . WPN_DATA_FOLDER_PATH . WPN_DEVICES_FILENAME . '"', WPN_LEVEL_ERROR);

        $file = file_get_contents(WPN_DATA_FOLDER_PATH . WPN_DEVICES_FILENAME);

        if ($file === false)
            throw new ExceptionToConsole('[DEVICES] Unable to read file "' . WPN_DATA_FOLDER_PATH . WPN_DEVICES_FILENAME . '"', WPN_LEVEL_ERROR);

        $devices = json_decode($file, true) ?: [];

        foreach ($devices as $device) {
            $subscription = new Subscription($device['subscription']['endpoint'], $device['subscription']['expirationTime'], $device['subscription']['keys']);
            $this->devices[] = new Device($subscription, $device['datetime'], $device['user_agent'], $device['ip_address']);
        }

        if (json_last_error() !== JSON_ERROR_NONE)
            throw new ExceptionToConsole('[DEVICES] JSON error: ' . json_last_error_msg(), WPN_LEVEL_ERROR);

        return true;
    }

    private function writeToFile(): bool
    {
        if (!is_dir(WPN_DATA_FOLDER_PATH))
            mkdir(WPN_DATA_FOLDER_PATH, 0700, true);
        $return = file_put_contents(WPN_DATA_FOLDER_PATH . WPN_DEVICES_FILENAME, json_encode($this, JSON_PRETTY_PRINT));

        if ($return === false)
            throw new ExceptionToConsole('[DEVICES] Unable to write file "' . WPN_DATA_FOLDER_PATH . WPN_DEVICES_FILENAME . '"', WPN_LEVEL_ERROR);

        // Copy .dat file to USB device to keep data after a reboot
        if (!is_dir(WPN_USB_DATA_FOLDER_PATH))
            mkdir(WPN_USB_DATA_FOLDER_PATH, 0700, true);
        $return = copy(WPN_DATA_FOLDER_PATH . WPN_DEVICES_FILENAME, WPN_USB_DATA_FOLDER_PATH . WPN_DEVICES_FILENAME);

        if ($return === false)
            throw new ExceptionToConsole('[DEVICES] Unable to copy file to "' . WPN_USB_DATA_FOLDER_PATH . WPN_DEVICES_FILENAME . '"', WPN_LEVEL_ERROR);

        return $return;
    }

    public function jsonSerialize(): mixed
    {
        return $this->devices;
    }
}

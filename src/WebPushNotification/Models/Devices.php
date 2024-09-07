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

class Devices implements \JsonSerializable
{
    private Config $config;
    private ?array $devices = [];

    public function __construct()
    {
        $this->config = new Config();
        $devices      = $this->config->getDevices();

        foreach ($devices as $device) {
            $subscription    = new Subscription($device['subscription']['endpoint'], $device['subscription']['expirationTime'], $device['subscription']['keys']);
            $this->devices[] = new Device($subscription, $device['name'], $device['datetime'], $device['user_agent'], $device['ip_address'], $device['silentNotifications'], $device['notificationLevel']);
        }
    }

    public function getAll(): ?array
    {
        return $this->devices;
    }

    public function getByEndpoint(?string $endpoint = null): ?Device
    {
        if (!$endpoint) {
            throw new ExceptionToConsole('[DEVICES] Error on endpoint data', WPN_LEVEL_ERROR);
        }

        return current(array_filter($this->devices, fn ($var) => $var?->getSubscription()?->getEndpoint() == $endpoint) ?? []) ?: null;
    }

    public function clear(): bool
    {
        $this->devices = [];
        $this->config->setDevices($this->devices);

        return $this->config->writeToFile();
    }

    public function setDeviceName(?string $endpoint = null, ?string $name = null): ?bool
    {
        if (!$endpoint) {
            throw new ExceptionToConsole('[DEVICES] Error on endpoint data', WPN_LEVEL_ERROR);
        }

        foreach ($this->devices as &$device) {
            if ($device->getSubscription()->getEndpoint() == $endpoint) {
                $device->setName($name);

                break;
            }
        }

        $this->config->setDevices($this->devices);

        return $this->config->writeToFile();
    }

    public function setDeviceNotifications(?string $endpoint = null, bool $silentNotifications = false, ?string $notificationLevel = null): ?bool
    {
        if (!$endpoint) {
            throw new ExceptionToConsole('[DEVICES] Error on endpoint data', WPN_LEVEL_ERROR);
        }

        foreach ($this->devices as &$device) {
            if ($device->getSubscription()->getEndpoint() == $endpoint) {
                $device->setSilentNotifications($silentNotifications);
                $device->setNotificationLevel($notificationLevel);

                break;
            }
        }

        $this->config->setDevices($this->devices);

        return $this->config->writeToFile();
    }

    public function hasRegisteredDevices(): bool
    {
        return $this->devices ? true : false;
    }

    public function register(array $data = [], ?string $userAgent = null, ?string $ipAddress = null): ?bool
    {
        if (!isset($data['endpoint']) || !isset($data['keys'])) {
            throw new ExceptionToConsole('[DEVICES] Error on subscription data', WPN_LEVEL_ERROR);
        }

        $subscription = new Subscription($data['endpoint'], $data['expirationTime'], $data['keys']);
        $endpoint     = $subscription->getEndpoint();

        // Check if there is no duplicate device
        $isDuplicate = false;
        if ($this->devices) {
            $isDuplicate = $this->getByEndpoint($endpoint);
        }

        if (!$isDuplicate) {
            // If no duplicate device, append to file
            $dynamixVar = @parse_ini_file('/boot/config/plugins/dynamix/dynamix.cfg');
            $level      = match (true) {
                ($dynamixVar['normal'] & 4)  == 4 => '0',
                ($dynamixVar['warning'] & 4) == 4 => '1',
                ($dynamixVar['alert'] & 4)   == 4 => '2',
                default                           => null,
            };
            $device        = new Device($subscription, null, date_format(date_create(), 'c'), $userAgent, $ipAddress, false, $level);
            $this->devices = [...$this->devices, ...[$device]];
            $this->config->setDevices($this->devices);

            return $this->config->writeToFile();
        }

        return true;
    }

    public function unregister(?string $endpoint = null): bool
    {
        if (!$endpoint) {
            return false;
        }

        $this->devices = array_values(array_filter($this->devices, fn ($var) => $var?->getSubscription()?->getEndpoint() != $endpoint) ?? []);
        $this->config->setDevices($this->devices);

        return $this->config->writeToFile();
    }

    public function jsonSerialize(): mixed
    {
        return $this->devices;
    }
}

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
    private ?array $devices = [];

    public function __construct()
    {
        $this->config = new Config();
        $devices      = $this->config->getDevices();

        foreach ($devices as $device) {
            $subscription    = new Subscription($device['subscription']['endpoint'], $device['subscription']['expirationTime'], $device['subscription']['keys']);
            $this->devices[] = new Device($subscription, $device['datetime'], $device['user_agent'], $device['ip_address']);
        }
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
        $this->config->setDevices($this->devices);

        return $this->config->writeToFile();
    }

    public function register(array $data = [], ?string $userAgent = null, ?string $ipAddress = null): ?bool
    {
        if (!isset($data['endpoint']) || !isset($data['keys'])) {
            throw new ExceptionToConsole('[DEVICES] Error on subscription data', WPN_LEVEL_ERROR);
        }

        $subscription = new Subscription($data['endpoint'], $data['expirationTime'], $data['keys']);
        $endpoint     = $subscription->getEndpoint();

        // Check if there is no duplicate
        $isDuplicate = false;
        if ($this->devices) {
            $isDuplicate = $this->getByEndpoint($endpoint);
        }

        if (!$isDuplicate) {
            // If no duplicate, append to file
            $device        = new Device($subscription, date_format(date_create(), 'c'), $userAgent, $ipAddress);
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

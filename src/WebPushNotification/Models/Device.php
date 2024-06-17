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

class Device implements \JsonSerializable
{
    private ?string $name = null;
    private ?string $datetime;
    private ?string $ipAddress = null;
    private ?string $userAgent = null;
    private Subscription $subscription;
    private bool $silentNotifications  = false;
    private ?string $notificationLevel = null;

    public function __construct(Subscription $subscription, ?string $name = null, ?string $datetime = null, ?string $userAgent = null, ?string $ipAddress = null, bool $silentNotifications = false, ?string $notificationLevel = null)
    {
        $this->name                = $name;
        $this->datetime            = $datetime;
        $this->ipAddress           = $ipAddress;
        $this->userAgent           = $userAgent;
        $this->subscription        = $subscription;
        $this->silentNotifications = $silentNotifications;
        $this->notificationLevel   = $notificationLevel;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDatetime(): ?string
    {
        return $this->datetime;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function getSubscription(bool $toArray = false): array|Subscription
    {
        return $toArray ? $this->subscription->toArray() : $this->subscription;
    }

    public function getSilentNotifications(): bool
    {
        return $this->silentNotifications;
    }

    public function getNotificationLevel(): ?string
    {
        return $this->notificationLevel;
    }

    public function setName(?string $name = null): void
    {
        $this->name = $name;
    }

    public function setSilentNotifications(bool $silentNotifications = false): void
    {
        $this->silentNotifications = $silentNotifications;
    }

    public function setNotificationLevel(?string $notificationLevel = null): void
    {
        $this->notificationLevel = $notificationLevel;
    }

    public function toArray(): array
    {
        return [
            'name'                => $this->name,
            'datetime'            => $this->datetime,
            'ip_address'          => $this->ipAddress,
            'user_agent'          => $this->userAgent,
            'subscription'        => $this->getSubscription(true),
            'silentNotifications' => $this->silentNotifications,
            'notificationLevel'   => $this->notificationLevel,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}

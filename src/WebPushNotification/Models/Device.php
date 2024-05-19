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
    private ?string $datetime;
    private ?string $ipAddress = null;
    private ?string $userAgent = null;
    private Subscription $subscription;

    public function __construct(Subscription $subscription, ?string $datetime = null, ?string $userAgent = null, ?string $ipAddress = null)
    {
        $this->datetime     = $datetime;
        $this->ipAddress    = $ipAddress;
        $this->userAgent    = $userAgent;
        $this->subscription = $subscription;
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

    public function toArray(): array
    {
        return [
            'datetime'     => $this->datetime,
            'ip_address'   => $this->ipAddress,
            'user_agent'   => $this->userAgent,
            'subscription' => $this->getSubscription(true),
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}

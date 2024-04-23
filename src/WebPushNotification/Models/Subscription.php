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

class Subscription implements JsonSerializable
{
    private ?string $endpoint = null;
    private ?int $expirationTime = null;
    private array $keys = [];

    public function __construct(?string $endpoint = null, ?int $expirationTime = null, array $keys = [])
    {
        $this->endpoint = $endpoint;
        $this->expirationTime = $expirationTime;
        $this->keys = $keys;
    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    public function getExpirationTime(): ?int
    {
        return $this->expirationTime;
    }

    public function getKeys(): array
    {
        return $this->keys;
    }

    public function toArray(): array
    {
        return [
            'endpoint' => $this->endpoint,
            'expirationTime' => $this->expirationTime,
            'keys' => $this->keys,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}

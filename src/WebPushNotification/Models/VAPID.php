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

class VAPID implements \JsonSerializable
{
    private Config $config;
    private ?array $vapid = [];

    public function __construct()
    {
        $this->config = new Config();
        $this->vapid = $this->config->getVapid();
    }

    public function generateKeys(): void
    {
        $this->vapid = \Minishlink\WebPush\VAPID::createVapidKeys();

        if (!$this->vapid) {
            throw new ExceptionToConsole('[VAPID] Error while generating keys', WPN_LEVEL_ERROR);
        }

        $this->config->setVapid($this->vapid);
        $this->config->writeToFile();
    }

    public function getPublicKey(): ?string
    {
        return $this->vapid['publicKey'] ?? null;
    }

    public function getPrivateKey(): ?string
    {
        return $this->vapid['privateKey'] ?? null;
    }

    public function jsonSerialize(): mixed
    {
        return $this->vapid;
    }
}

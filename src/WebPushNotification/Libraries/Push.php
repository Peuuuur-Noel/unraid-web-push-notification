<?php

/*
 * This file is part of Web Push Notification Agent plugin for Unraid.
 *
 * (c) Peuuuur Noel
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace WebPushNotification\Libraries;

use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use WebPushNotification\Models\Device;
use WebPushNotification\Models\Devices;
use WebPushNotification\Models\Notification;
use WebPushNotification\Models\VAPID;

class Push
{
    private WebPush $webPush;

    public function __construct(string $urgency = 'high')
    {
        $vapid = new VAPID();

        $this->webPush = new WebPush([
            'VAPID' => [
                'subject'    => 'http://127.0.0.1', // Add localhost address to allow push on Apple push service
                'publicKey'  => $vapid->getPublicKey(),
                'privateKey' => $vapid->getPrivateKey(),
            ],
        ], [
            'urgency' => $urgency, // protocol defaults to "normal". (very-low, low, normal, or high)
        ]);

        // Fix for Firefox
        // https://github.com/web-push-libs/web-push-php#payload-length-security-and-performance
        $this->webPush->setAutomaticPadding(false);
    }

    public function queueDevice(Notification $notificaiton, Device $device): void
    {
        $this->webPush->queueNotification(Subscription::create($device->getSubscription(true)), json_encode($notificaiton));
    }

    public function queueDevices(Notification $notificaiton, array $devices = []): void
    {
        foreach ($devices as $device) {
            $this->queueDevice($notificaiton, $device);
        }
    }

    public function send(): int
    {
        $count             = 0;
        $unregisterDevices = [];

        foreach ($this->webPush->flush() as $report) {
            if (!$report->isSuccess() || $report->isSubscriptionExpired()) {
                $unregisterDevices[] = [
                    'endpoint' => $report->getEndpoint(),
                    'reason'   => $report->getReason(),
                ];
            } else {
                ++$count;
            }
        }

        if ($unregisterDevices) {
            $devices = new Devices();

            foreach ($unregisterDevices as $device) {
                wpm_log_to_console('[INFO] Unable to send message to endpoint: ' . $device['endpoint']);
                wpm_log_to_console('[INFO] Reason: ' . $device['reason']);
                wpm_log_to_console('[INFO] Removing it from devices list');

                $devices->unregister($device['endpoint']);
            }
        }

        return $count;
    }
}

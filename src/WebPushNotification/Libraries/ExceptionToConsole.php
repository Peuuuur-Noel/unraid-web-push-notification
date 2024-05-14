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

class ExceptionToConsole extends \Exception
{
    public function __construct($message, $code = 0, ?\Throwable $previous = null)
    {
        $error_level = current(array_filter(WPN_MESSAGE_ERROR_LEVEL, fn ($var) => $code == $var['errorno'])) ?: WPN_MESSAGE_ERROR_LEVEL['unknown'];
        wpm_log_to_console('[' . $error_level['level'] . ']' . $message);

        parent::__construct($message, $code, $previous);
    }
}

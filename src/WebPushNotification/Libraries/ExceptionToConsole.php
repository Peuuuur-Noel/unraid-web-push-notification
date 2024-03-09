<?php

namespace WebPushNotification\Libraries;

use Exception;
use Throwable;

class ExceptionToConsole extends Exception
{
    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        $error_level = current(array_filter(WPN_MESSAGE_ERROR_LEVEL, fn ($var) => $code == $var['errorno'])) ?: WPN_MESSAGE_ERROR_LEVEL['unknown'];
        wpm_log_to_console('[' . $error_level['level'] . ']' . $message);

        parent::__construct($message, $code, $previous);
    }
}

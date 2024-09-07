<?php

/*
 * This file is part of Web Push Notification Agent plugin for Unraid.
 *
 * (c) Peuuuur Noel
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$wpmLocalFilePath = WPN_PATH . '/language/' . WPN_LOCALE . '.php';
if (file_exists($wpmLocalFilePath)) {
    require_once $wpmLocalFilePath;
} else {
    require_once WPN_PATH . '/language/en_US.php';
}

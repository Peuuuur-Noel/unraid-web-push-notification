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

$wpm_local_file_path = __DIR__ . '/../language/' . WPN_LOCALE . '.php';
if (file_exists($wpm_local_file_path)) {
    require_once $wpm_local_file_path;
} else {
    $wpm_lang = [];
}

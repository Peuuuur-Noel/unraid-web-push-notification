<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$wpm_local_file_path = __DIR__ . '/../language/' . WPN_LOCALE . '.php';
if (file_exists($wpm_local_file_path))
    require_once $wpm_local_file_path;
else
    $wpm_lang = [];

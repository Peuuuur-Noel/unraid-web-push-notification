<?php

/*
 * This file is part of Web Push Notification Agent plugin for Unraid.
 *
 * (c) Peuuuur Noel
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

spl_autoload_register(fn ($className) => require_once WPN_PATH . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php');

function wpm_log_to_console(string $message = ''): void
{
    exec('logger -t WebPushNotification -- ' . escapeshellarg($message));
}

function wpm__(string $text = '', int|string ...$args): string
{
    global $wpmLang;

    return nl2br(sprintf($wpmLang[$text] ?? $text, ...$args));
}

function wpm_e(string $text = '', int|string ...$args): void
{
    echo wpm__($text, ...$args);
}

function wpm_usage(): int
{
    echo <<<'EOT'
    php actions.php [-e "event"] [-i "normal|warning|alert"] [-s "subject"] [-d "description"] [-c "message"] [-l "link"] [-t "timestamp"] [-o "sound"]

      Push a web notification

      use -e to specify the event
      use -i to specify the severity
      use -s to specify a subject
      use -d to specify a short description
      use -c to specify the content of the message (long description)
      use -l to specify a link (clicking the notification will take you to that location)
      use -t to specify notification timestamp (in seconds)
      use -o to specify an URL for notification sound

      All options are optional

    EOT;

    return 1;
}

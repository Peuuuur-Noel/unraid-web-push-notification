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

class Notification implements \JsonSerializable
{
    private string $title = '';
    private string $body = '';
    private string $icon = '';
    private string $image = '';
    private string $badge = '';
    private string $dir = 'auto';
    private int $timestamp = 0;
    private array $data = [];
    private string $tag = '';
    private bool $renotify = false;
    private array $vibrate = [];
    private string $sound = '';
    private bool $silent = false;

    public function setTitle(string $title = ''): void
    {
        $this->title = $title;
    }

    public function setBody(string $body = ''): void
    {
        $this->body = $body;
    }

    public function setIcon(string $icon = ''): void
    {
        $this->icon = $icon;
    }

    public function setImage(string $image = ''): void
    {
        $this->image = $image;
    }

    public function setBadge(string $badge = ''): void
    {
        $this->badge = $badge;
    }

    public function setDir(string $dir = 'auto'): void
    {
        $this->dir = $dir;
    }

    public function setTimestamp(int $timestamp = 0): void
    {
        $this->timestamp = $timestamp;
    }

    public function setData(?array $data = null): void
    {
        $this->data[] = $data;
    }

    public function setTag(string $tag = ''): void
    {
        $this->tag = $tag;
    }

    public function setRenotify(bool $renotify = false): void
    {
        $this->renotify = $renotify;
    }

    public function setVibrate(array $vibrate = []): void
    {
        $this->vibrate = $vibrate;
    }

    public function setSound(string $sound = ''): void
    {
        $this->sound = $sound;
    }

    public function setSilent(bool $silent = false): void
    {
        $this->silent = $silent;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'icon' => $this->icon,
            'image' => $this->image,
            'badge' => $this->badge,
            'dir' => $this->dir,
            'timestamp' => $this->timestamp,
            'data' => $this->data,
            'tag' => $this->tag,
            'renotify' => $this->renotify,
            'vibrate' => $this->vibrate,
            'sound' => $this->sound,
            'silent' => $this->silent,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}

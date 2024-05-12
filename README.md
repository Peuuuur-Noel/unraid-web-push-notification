# Web Push Notification Agent plugin for Unraid

An Unraid plugin to add push notifications to your browser.

## What is it?

You know, that's the notification you receive in the top/bottom right corner of your desktop computer or in the notification panel on your mobile device.

Take a quick look to this [Push notifications overview](https://web.dev/articles/push-notifications-overview) article.

## Usage

1. Browse the Unraid notifications settings page with the device you want to register and receive notifications.
2. Request for notification to allow it in your browser and register to push service.

Notification will be send to your devices through push service. Both your Unraid and devices must have access to internet. No need to keep Unraid interface open.

On desktop, your browser must be open to receive notifications, otherwise they will be pending until opened.

On Apple iPhone/iPad, to enable push notifications, add Unraid notifications settings page to the Home Screen.

**If you have issue, try to revoke notification permissions and clear all browser cache or specific site.**

## Browser compatibility

### Tested on

* Desktop:
  * Chromium based:
    * Chrome 50+
    * Vivaldi 6.7+
    * Edge 17+
    * Opera 42+
  * Firefox 44+
  * Safari 16.4+
* Mobile (Android):
  * Chromium based:
    * Chrome 124+
    * Vivaldi 6.7+
    * Samsung Internet 24+
  * Firefox 125+

More details on [Can I use](https://caniuse.com/push-api).

### Not tested on

* Mobile (Apple):
  * Safari

I don't have access to an iPhone/iPad, so I can't test Safari on it.

## Mobile devices

Notifications may not arrive instantly. It will depend on several factors: OS deep sleep, power management profile, frozen application when idle, battery level, network usage, ... and the way notifications are implemented (see this [comment](https://issues.chromium.org/issues/41351071#comment57) for Android on Chromium Issue Tracker).

## Ressources

[minishlink/web-push](https://github.com/web-push-libs/web-push-php): Web Push library for PHP
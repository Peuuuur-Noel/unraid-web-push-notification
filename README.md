# Web Push Notification Agent plugin for Unraid

An Unraid plugin to add push notifications to your browser.

## What is it?

You know, that's the notification you receive in the top/bottom right corner of your desktop computer or in the notification panel on your mobile device.

Take a quick look to this [Push notifications overview](https://web.dev/articles/push-notifications-overview) article.

## Usage

Works like any default Unraid notification agents.

1. Browse the Unraid notifications settings page with the device you want to register and receive notifications.
2. Request for notification to allow it in your browser and register to push service.

Notification will be send to your devices through push service. Both your Unraid and devices must have access to internet. No need to keep Unraid interface open.

On desktop, your browser must be open to receive notifications, otherwise they will be pending until opened.

## Browser compatibility

### Tested on (Windows, macOS, Android, Linux)

* Chromium based:
  * Chrome 50+
  * Edge 17+
  * Opera 42+
  * ...
* Firefox 44+
* Safari 16.4+ (desktop)

More details on [Can I use](https://caniuse.com/push-api).

### Not tested on

* Safari (mobile)

I don't have access to an Apple mobile device, so I can't test Safari on it.

### Apple restrictions

To enable push notifications, add unraid notifications settings page to Home Screen in iOS 16.4 or later.

### Mobile devices

Notifications may not arrive instantly. It will depend on several factors: OS deep sleep, power management profile, application frozen when idle, battery level, network usage, ... and the way notifications are implemented (see this [comment](https://issues.chromium.org/issues/41351071#comment57) for Android on Chromium Issue Tracker).

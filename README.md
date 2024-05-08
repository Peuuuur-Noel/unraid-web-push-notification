# Web Push Notification Agent plugin for Unraid

Add push notification agent to Unraid to receive notifications in your browser.

## What is it?

You know, that's the notification you receive in the top/bottom right corner of your desktop computer or in the notification panel on your mobile device.

Take a quick look to this [Push notifications overview](https://web.dev/articles/push-notifications-overview) article.

## Usage

Works like any default Unraid notification agents.

Browse the Unraid notifications settings page with every devices you want to register and receive notifications.

1. On first use, generate VAPID keys. This keys will encrypt notification content when sent through push services.
2. Request for notification to allow them in your browser and register to push service.

You must have access to the Unraid interface with your devices for the registration. Notification will be send to your devices through push service. Both your Unraid and devices must have access to internet.

Your browser must be open to receive notifications, otherwise they will be pending until opened.

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

I don't have access to any Apple mobile devices, so I can't test Safari on them.

### Apple restrictions

To enable push notifications, add unraid notifications settings page to Home Screen in iOS 16.4 or later.

### Android devices

Notifications may not arrive instantly. It will depend on several factors: OS deep sleep, power management profile, application frozen when idle, battery level, network usage, ... and the way notifications are implemented (see this [comment](https://issues.chromium.org/issues/41351071#comment57) on Chromium Issue Tracker).

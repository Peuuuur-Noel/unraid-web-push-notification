# Web Push Notification Agent plugin for Unraid

Add push notification agent to Unraid to receive notifications in your browser.

## What is it?

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

More details on [Can I use](https://caniuse.com/push-api).

### Not tested on

* Safari

I don't have access to any Apple devices, so I can't test Safari on them. I tried on a virtual machine with macOS 14.3, but it seems there is an issue with Safari 17.3 on notifications permission request (stuck on pending state) or with the VM itself.

It *should* work on Safari 16+ on macOS and Safari 16.4+ on iOS. Older versions use Apple's custom Push API which requires signing up to Apple services and subscribe to an Apple developer license... ([see documentation](https://developer.apple.com/library/archive/documentation/NetworkingInternet/Conceptual/NotificationProgrammingGuideForWebsites/PushNotifications/PushNotifications.html)).

### Apple restrictions

To enable push notifications, add unraid notifications settings page to Home Screen in iOS 16.4 or later and Webpages/Dock/App in Safari 16 for macOS 13 or later.

### Android devices

You *may* need to add unraid notifications page to Home Screen in Android to enable push notification.

Notifications may not arrive instantly. It will depend on several factors: OS deep sleep, power management profile, application frozen when idle, battery level, network usage, ... and the way notifications are implemented (see this [comment](https://issues.chromium.org/issues/41351071#comment57) on chromium issues tracker).

<?php
require_once __DIR__ . '/../../include/loader.php';
header('Content-type: application/javascript; charset=utf-8;');
?>

const WPM_SW_VERSION = '<?php echo WPN_SW_VERSION; ?>';

const onInstall = (event) => {
    event.waitUntil(self.skipWaiting());
};

const onActivate = (event) => {
    event.waitUntil(self.clients.claim());
};

const onPush = (event) => {
    if (!event.data) {
        console.log('Push event but no data');
        return;
    }

    Promise.resolve(event.data.json()).then((data) => {
        const options = {
            title: data.title,
            body: data.body,
            icon: data.icon,
            // image: data.image,
            badge: data.badge,
            // dir: data.dir,
            timestamp: data.timestamp,
            data: [],
            // tag: data.tag,
            // renotify: data.renotify,
            // vibrate: data.vibrate,
            sound: data.sound,
            silent: data.silent,
        };

        let isRemove = false;

        data.data.forEach(element => {
            switch (element.type) {
                case 'version':
                    if (element.version != WPM_SW_VERSION) {
                        self.registration.update().catch((e) => {
                            const options = {
                                title: data.title,
                                body: '<?php wpm_e('Failed to update notification service. Connect this device to the same network as your Unraid server and send a test to update it again.'); ?>',
                                icon: 'https://craftassets.unraid.net/uploads/discord/notify-warning.png',
                                // image: data.image,
                                badge: data.badge,
                                // dir: data.dir,
                                timestamp: data.timestamp,
                                data: [],
                                // tag: data.tag,
                                // renotify: data.renotify,
                                // vibrate: data.vibrate,
                                sound: data.sound,
                                silent: data.silent,
                            };

                            showNotification(event, options);
                        });
                    }
                    break;

                case 'remove':
                    isRemove = true;
                    this.registration.pushManager.getSubscription()
                        .then((subscription) => subscription.unsubscribe());
                    this.registration.unregister();
                    break;

                case 'url':
                    options.data.push(element);
                    break;
            }
        });

        if (!isRemove)
            showNotification(event, options);
    });
}

const showNotification = (event, options) => {
    event.waitUntil(self.registration.showNotification(options.title, options));
}

const onNotificationclick = (event) => {
    const notificationclick = (event) => {
        event.notification.close();

        const url = event.notification.data.filter(x => x.type == 'url')[0]?.url || '<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] ?>';
        const clientList = self.clients.matchAll({ type: "window", includeUncontrolled: true });

        for (let i = 0; i < clientList.length; i++) {
            const client = clientList[i];

            if (client.url === url && "focus" in client)
                return client.focus();
        }

        if (self.clients.openWindow)
            return self.clients.openWindow(url);
    };

    event.waitUntil(notificationclick(event));
};

self.addEventListener("install", onInstall);
self.addEventListener("activate", onActivate);
self.addEventListener('push', onPush);
self.addEventListener("notificationclick", onNotificationclick);

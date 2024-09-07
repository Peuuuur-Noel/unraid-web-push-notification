/*
 * This file is part of Web Push Notification Agent plugin for Unraid.
 *
 * (c) Peuuuur Noel
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

class WebPushNotification {
    pluginUrl = '/plugins/web-push-notification/';
    csrfToken = null;
    constructor() {
        this.bindEvents();
        this.addPwaManifest();
    }
    __(text = '') {
        return wpm_lng[text] || text;
    }
    checkAPI() {
        if (!('serviceWorker' in navigator)) {
            this.error(this.__('no_service_worker_support'));
            return false;
        }

        if (!('PushManager' in window)) {
            this.error(this.__('no_push_api_support'));
            return false;
        }

        return true;
    }
    checkIOSHomeScreen() {
        const deviceUserAgent = this.parseUserAgent();
        if ((deviceUserAgent.indexOf('iPadOS') != -1 || deviceUserAgent.indexOf('iOS') != -1) && deviceUserAgent.indexOf('Safari') != -1 && window.navigator.standalone !== true) {
            this.error(this.__('safari_ios_home_screen'));
            return false;
        }

        return true;
    }
    checkResgistration() {
        // Get service worker registrations
        navigator.serviceWorker.getRegistrations()
            .then((registrations) => {
                if (!registrations || !registrations.length) {
                    document.querySelector('#wpn-permission-status').innerText = this.__('permissions_granted_sw_not_registered');
                    document.querySelector('#wpn-permission-status').setAttribute('data-status', 'orange');
                    return;
                }

                registrations.forEach((registration) => {
                    registration?.pushManager.getSubscription()
                        .then(async (subscription) => {
                            if (!subscription?.endpoint) {
                                this.subscribeToPushService(registration);
                            } else {
                                Promise.resolve(this.getDevicesList())
                                    .then(async (devices) => {
                                        // Check if subscription in server device list
                                        const list = devices?.data?.filter((device) => device?.subscription?.endpoint && device.subscription.endpoint == subscription.endpoint);
                                        if (!list.length) {
                                            // Unsubscribe
                                            subscription.unsubscribe().then(() => {
                                                // Register again
                                                this.subscribeToPushService(registration);
                                            }).catch((e) => {
                                                this.error(this.__('error_unsubscribing'), e);
                                            });
                                        } else {
                                            document.querySelector('#wpn-permission-status').innerText = this.__('permissions_granted_registered');
                                            document.querySelector('#wpn-permission-status').setAttribute('data-status', 'green');

                                            if (!document.querySelector('#wpn-device-list').hidden) {
                                                this.displayDevicesList();
                                            }
                                        }
                                    });
                            }
                        }).catch((e) => {
                            this.error(this.__('error_retrieving_subscription'), e);
                        });
                });
            }).catch((e) => {
                this.error(this.__('error_retrieving_registrations'), e);
            });
    }
    displayWebPushNotificationStatus() {
        switch (Notification.permission) {
            case 'granted':
                this.checkResgistration();
                break;

            case 'denied':
                document.querySelector('#wpn-permission-status').innerText = this.__('permissions_denied');
                document.querySelector('#wpn-permission-status').setAttribute('data-status', 'red');
                break;

            case 'default':
                document.querySelector('#wpn-permission-status').innerText = this.__('permissions_not_granted');
                document.querySelector('#wpn-permission-status').setAttribute('data-status', 'orange');
                break;
        }
    };
    urlBase64ToUint8Array(base64String) {
        const padding = (4 - base64String.length % 4) % 4;
        const base64 = base64String.padEnd(padding, '=').replace(/\-/g, '+').replace(/_/g, '/');
        const rawData = atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (var i = 0; i < rawData.length; ++i)
            outputArray[i] = rawData.charCodeAt(i);

        return outputArray;
    }
    async getCSRFToken() {
        try {
            if (this.csrfToken)
                return this.csrfToken;

            const response = await fetch(this.pluginUrl + 'actions.php?action=get_csrf_token');
            const result = await response.json();

            if (result.errno)
                throw result.errmsg;

            this.csrfToken = result;

            return result;
        } catch (e) {
            this.error(e);
        }
    }
    async getDevicesList() {
        try {
            const response = await fetch(this.pluginUrl + 'actions.php?action=get_devices_list');
            const result = await response.json();

            if (result.errno)
                throw result.errmsg;

            return result;
        } catch (e) {
            this.error(e);
        }
    }
    async generateVapid() {
        try {
            const response = await fetch(this.pluginUrl + 'actions.php?action=generate_vapid');
            const result = await response.json();

            if (result.errno)
                throw result.errmsg;

            return result;
        } catch (e) {
            this.error(e);
        }
    }
    async getPublicKey() {
        try {
            const response = await fetch(this.pluginUrl + 'actions.php?action=get_vapid_public_key');
            const result = await response.json();

            if (result.errno)
                throw result.errmsg;

            return result.data.publicKey || false;
        } catch (e) {
            this.error(e);
        }
    }
    async saveConfig() {
        try {
            const data = {
                'wpn-enable': document.querySelector('#wpn-enable-select').value == '1' ? 'enable' : 'disable',
                'wpn-silent': [],
            };
            document.querySelectorAll('input[name="wpn-silent[]"]:checked').forEach((elem) => data['wpn-silent'].push(elem.value));

            return await this.postRequest(this.pluginUrl + 'actions.php?action=config', data);
        } catch (e) {
            this.error(e);
        }
    }
    async saveSubscription(subscription) {
        try {
            const data = {
                'subscription': JSON.stringify(subscription)
            };

            return await this.postRequest(this.pluginUrl + 'actions.php?action=save_device', data);
        } catch (e) {
            this.error(e);
        }
    }
    async removeSubscription(endpoint, remoteDelete = false) {
        try {
            const data = {
                'endpoint': endpoint,
                'remote_delete': remoteDelete
            };

            return await this.postRequest(this.pluginUrl + 'actions.php?action=remove_device', data);
        } catch (e) {
            this.error(e);
        }
    }
    async postRequest(url = '', params = null) {
        try {
            if (!url || !params)
                return false;

            const csrfToken = await this.getCSRFToken();
            const data = new URLSearchParams();
            data.append('csrf_token', csrfToken.data.csrf_token);
            for (let p in params)
                data.append(p, params[p]);

            const response = await fetch(url, {
                method: 'post',
                body: data,
            });
            const result = await response.json();

            if (result.errno)
                throw result.errmsg;

            return result;
        } catch (e) {
            this.error(e);
        }
    }
    requestNotificationPermission() {
        Notification.requestPermission()
            .then((permission) => {
                if (permission == 'granted')
                    this.registerServiceWorker();
                else
                    this.displayWebPushNotificationStatus();
            }).catch((e) => {
                this.error(e);
            });
    }
    registerServiceWorker() {
        // Register service worker
        navigator.serviceWorker.register(this.pluginUrl + 'serviceworker.php')
            .then((registration) => {
                let serviceWorker = null;
                if (registration.installing)
                    serviceWorker = registration.installing;
                else if (registration.waiting)
                    serviceWorker = registration.waiting;
                else if (registration.active)
                    serviceWorker = registration.active;

                if (serviceWorker.state == 'activated') {
                    this.displayWebPushNotificationStatus();
                    return;
                }

                serviceWorker.onstatechange = async (event) => {
                    if (event.target.state == 'activated')
                        this.subscribeToPushService(registration);
                };
            }).catch((e) => {
                this.error('', e);
            });
    }
    subscribeToPushService(registration) {
        Promise.resolve(this.getPublicKey())
            .then((publicKey) => {
                if (!publicKey)
                    return;

                const options = {
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(publicKey),
                };

                // Subscribe to push service
                registration?.pushManager.subscribe(options)
                    .then(async (subscription) => {
                        if (!subscription?.endpoint)
                            throw this.__('error_subscribing');

                        await this.saveSubscription(subscription);

                        document.querySelector('#wpn-list-btn')?.classList.remove('active');

                        this.displayWebPushNotificationStatus();
                    }).catch((e) => {
                        this.error('', e);
                    });
            }).catch((e) => {
                this.error('', e);
            });
    }
    getCurrentSubscription(callback) {
        // Get service worker registrations
        navigator.serviceWorker.getRegistrations()
            .then((registrations) => {
                if (!registrations.length)
                    callback(false, false);

                registrations?.forEach((registration) => {
                    // Get subscription
                    registration?.pushManager.getSubscription()
                        .then((subscription) => {
                            callback(subscription, registration);
                        }).catch((e) => {
                            this.error(this.__('error_retrieving_subscription'), e);
                        });
                });
            }).catch((e) => {
                this.error(this.__('error_retrieving_registrations'), e);
            });
    }
    unregisterServiceWorker() {
        this.getCurrentSubscription((subscription, registration) => {
            if (!subscription?.endpoint)
                return;

            // Remove subscription
            subscription.unsubscribe()
                .then(() => {
                    // Remove subscription on server
                    this.removeSubscription(subscription.endpoint)
                        .then(() => {
                            this.displayDevicesList();
                        });

                    // Remove service worker
                    registration.unregister()
                        .then(() => {
                            this.displayWebPushNotificationStatus();
                        }).catch((e) => {
                            this.error(this.__('error_unregistering'), e);
                        });
                }).catch((e) => {
                    this.error(this.__('error_unsubscribing'), e);
                });
        });
    }
    displayDevicesList() {
        const html = document.querySelector('#wpn-device-list');

        const tableHTML = `<table class="tablesorter">
                    <thead><tr><th>${this.__('action')}</th><th>${this.__('device_info')}</th><th>${this.__('notification_settings')}</th></tr></thead>
                    <tbody></tbody>
                </table>
                <p class="loading">${this.__('loading')}</p>`;
        html.innerHTML = tableHTML;

        Promise.resolve(this.getDevicesList())
            .then((devices) => {

                const tbody = html.querySelector('tbody');
                const pLoading = html.querySelector('p.loading');
                const availableLevels = [];
                const unraidLevels = {
                    'normal3': '0',
                    'warning3': '1',
                    'alert3': '2',
                };

                document.querySelectorAll('input[name="normal3"]:checked, input[name="warning3"]:checked, input[name="alert3"]:checked').forEach((elem) => {
                    if (elem.name in unraidLevels) {
                        availableLevels.push(unraidLevels[elem.name]);
                    }
                });

                this.getCurrentSubscription((subscription) => {
                    if (!devices?.data?.length) {
                        pLoading.innerText = this.__('no_devices');
                        return;
                    } else {
                        pLoading.remove();
                    }

                    devices?.data?.forEach((device, index) => {
                        const row = document.createElement('tr');
                        tbody.append(row);

                        const deviceUserAgent = this.parseUserAgent(device.user_agent);
                        let currentDevice = '';
                        if (subscription?.endpoint && device?.subscription?.endpoint && device.subscription.endpoint == subscription.endpoint) {
                            currentDevice = ` <strong style="color: green;">(${this.__('current_device')})</strong>`;
                        }

                        let deviceName = `<input type="text" value="${device.name ?? (deviceUserAgent.length ? deviceUserAgent.join(' / ') : '')}" class="wpn-device-name">`;
                        deviceName += `<button class="btn-rename" style="margin: 0; padding: 6px;" disabled>${this.__('rename')}</button>`;

                        const rowHTML = `<tr>
                            <td>
                                <button class="btn-test">${this.__('test')}</button><br>
                                <button class="btn-remove">${this.__('remove')}</button>
                            </td>
                            <td>
                                <p><strong>${this.__('device_name')}</strong>${currentDevice}<br> ${deviceName}</p>
                                <p><strong>${this.__('user_agent')}</strong><br> ${device.user_agent}</p>
                                <p><strong>${this.__('date')}</strong><br> ${new Date(device.datetime).toLocaleString()}</p>
                                <p><strong>${this.__('ip_address')}</strong><br> ${device.ip_address}</p>
                            </td>
                            <td>
                                <p>
                                    <label for="wpn-silent-notifications-${index}"><strong>${this.__('silent_notifications')}</strong></label> <input id="wpn-silent-notifications-${index}" class="wpn-silent-notifications" type="checkbox">
                                </p>
                                <p>
                                    <label for="wpn-notification-level-${index}"><strong>${this.__('notification_level_lowest')}</strong></label><br>
                                    <select id="wpn-notification-level-${index}" class="wpn-notification-level">
                                        <option value="0">${this.__('notification_level_notices')}</option>
                                        <option value="1">${this.__('notification_level_warnings')}</option>
                                        <option value="2">${this.__('notification_level_alerts')}</option>
                                    </select>
                                </p>
                                <p><button class="btn-save" disabled>${this.__('save')}</button></p>
                            </td>
                        </tr>`;
                        row.innerHTML = rowHTML;

                        const silentNotifications = row.querySelector('.wpn-silent-notifications');
                        if (deviceUserAgent.indexOf('Firefox') != -1) {
                            const span = document.createElement('span');
                            span.innerText = this.__('unsupported_firefox');
                            silentNotifications.parentNode.append(span);
                            silentNotifications.remove();
                        } else {
                            silentNotifications.checked = device.silentNotifications;
                        }
                        row.querySelector('.wpn-notification-level').value = device.notificationLevel;

                        row.querySelectorAll('.wpn-notification-level option').forEach((elem) => {
                            if (availableLevels.indexOf(elem.value) == -1) {
                                elem.disabled = true;
                            }
                        });

                        row.querySelectorAll('.wpn-silent-notifications, .wpn-notification-level').forEach((elem) => {
                            elem.onchange = () => row.querySelector('.btn-save').disabled = false;
                        });
                        row.querySelector('.wpn-device-name').onkeydown = () => {
                            row.querySelector('.btn-rename').disabled = false
                            return true;
                        };
                        row.querySelector('.btn-test').onclick = async () => {
                            try {
                                const data = {
                                    'endpoint': device?.subscription?.endpoint
                                };

                                return await this.postRequest(this.pluginUrl + 'actions.php?action=test', data);
                            } catch (e) {
                                this.error(e);
                            }
                        };
                        row.querySelector('.btn-remove').onclick = async () => {
                            if (!confirm(this.__('remove_device')))
                                return;

                            if (subscription?.endpoint && device?.subscription?.endpoint && device.subscription.endpoint == subscription.endpoint) {
                                this.unregisterServiceWorker();
                            } else {
                                await this.removeSubscription(device.subscription.endpoint, true);
                                this.displayDevicesList();
                            }
                        };
                        row.querySelector('.btn-rename').onclick = async () => {
                            try {
                                const data = {
                                    'endpoint': device?.subscription?.endpoint,
                                    'name': row.querySelector('.wpn-device-name').value,
                                };

                                await this.postRequest(this.pluginUrl + 'actions.php?action=set_device_name', data);

                                row.querySelector('.btn-rename').disabled = true;

                                return;
                            } catch (e) {
                                this.error(e);
                            }
                        };
                        row.querySelector('.btn-save').onclick = async () => {
                            try {
                                const data = {
                                    'endpoint': device?.subscription?.endpoint,
                                    'notification-level': row.querySelector('.wpn-notification-level').value,
                                };

                                if (row.querySelector('.wpn-silent-notifications')) {
                                    data['silent-notifications'] = row.querySelector('.wpn-silent-notifications').checked;
                                }

                                await this.postRequest(this.pluginUrl + 'actions.php?action=set_device_notifications', data);

                                row.querySelector('.btn-save').disabled = true;

                                return;
                            } catch (e) {
                                this.error(e);
                            }
                        };
                    });
                });
            });
    }
    parseUserAgent(userAgent = '') {
        if (!userAgent)
            userAgent = navigator.userAgent;

        const out = [];
        const isIpadOS = /CPU\s+OS\s+([\d]+)_([\d]+)/i.exec(userAgent) ?? [];

        if (/Android/i.test(userAgent))
            out.push('Android');
        else if (/Edg/i.test(userAgent))
            out.push('Edge');
        else if (/Windows/i.test(userAgent))
            out.push('Windows');
        else if (/iPad/i.test(userAgent) && (isIpadOS.length && isIpadOS[1] > 13 || (isIpadOS.length > 1 && isIpadOS[1] == 13 && isIpadOS[2] >= 1)))
            out.push('iPadOS');
        else if (/iPad|iPhone/i.test(userAgent))
            out.push('iOS');
        else if (/Macintosh/i.test(userAgent))
            out.push('Macintosh');
        else if (/Ubuntu/i.test(userAgent))
            out.push('Ubuntu');
        else if (/Linux/i.test(userAgent))
            out.push('Linux');

        if (/Firefox/i.test(userAgent))
            out.push('Firefox');
        else if (/Trident/i.test(userAgent))
            out.push('Internet Explorer');
        else if (/OPR|Presto/i.test(userAgent))
            out.push('Opera');
        else if (/Edg/i.test(userAgent))
            out.push('Edge');
        else if (/SamsungBrowser/i.test(userAgent))
            out.push('Samsung Internet');
        else if (/Chromium/i.test(userAgent))
            out.push('Chromium');
        else if (/Chrome/i.test(userAgent))
            out.push('Chrome');
        else if (/Safari/i.test(userAgent))
            out.push('Safari');

        return out;
    }
    bindEvents() {
        const csrfToken = new NchanSubscriber('/sub/session,var');
        csrfToken.on('message', function (data) {
            this.csrfToken = data;
        });
        csrfToken.start();

        document.querySelectorAll('#wpn-enable-select, input[name="wpn-silent[]"]').forEach((elem) => {
            elem.onchange = (event) => {
                document.querySelector('#wpn-apply-btn').removeAttribute('disabled');
            }
        });
        document.querySelector('#wpn-apply-btn').onclick = async (event) => {
            const result = await this.saveConfig();

            if (!result.errno)
                location.reload();
        }
        document.querySelector('#wpn-list-btn').onclick = (event) => {
            const html = document.querySelector('#wpn-device-list');
            html.hidden = !html.hidden;

            if (event.target.classList.contains('active')) {
                event.target.classList.remove('active');
            } else {
                this.displayDevicesList();
                event.target.classList.add('active');
            }
        }
        document.querySelector('#wpn-advanced-btn').onclick = (event) => {
            const div = document.querySelector('.wpn-advanced-settings');
            div.hidden = !div.hidden;

            if (event.target.classList.contains('active'))
                event.target.classList.remove('active');
            else
                event.target.classList.add('active');
        }
        document.querySelector('#wpn-permission-btn').onclick = (event) => {
            document.querySelector('#wpn-error').style.display = '';
            document.querySelector('#wpn-error-text').innerHTML = '';
            if (this.checkIOSHomeScreen() && this.checkAPI()) {
                document.querySelector('#wpn-permission-status').innerText = this.__('registration_progress');
                document.querySelector('#wpn-permission-status').setAttribute('data-status', '');
                this.requestNotificationPermission();
            }
        };
        document.querySelector('#wpn-generate-vapid-btn').onclick = (event) => {
            if (!confirm(this.__('generate_vapid_keys')))
                return;

            Promise.resolve(this.generateVapid())
                .then((publicKey) => {
                    const data = publicKey.data || { 'publicKey': this.__('error_while_generating'), 'privateKey': this.__('error_while_generating') };
                    document.querySelector('#wpn-publicKey').value = data.publicKey;
                    document.querySelector('#wpn-privateKey').value = data.privateKey;
                    document.querySelectorAll('#wpn-test-bt').forEach(x => x.removeAttribute('disabled'));

                    document.querySelector('#wpn-list-btn')?.classList.remove('active');
                    const html = document.querySelector('#wpn-device-list');
                    html.hidden = true;
                });
        }
    }
    addPwaManifest() {
        const linkManifestTag = document.createElement('link');
        linkManifestTag.href = '/plugins/web-push-notification/assets/site.webmanifest';
        linkManifestTag.rel = 'manifest';
        linkManifestTag.crossOrigin = 'use-credentials';
        document.querySelector('head').append(linkManifestTag);
    }
    error(msg, e = null) {
        const error = document.querySelector('#wpn-error');
        error.style.display = 'flex';

        const text = document.querySelector('#wpn-error-text');
        text.innerText = msg;

        if (e) {
            if (text.innerHTML)
                text.innerHTML += '<br>';

            text.innerText += e;
            console.error(e);
        }
    }
}
new WebPushNotification();

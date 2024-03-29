
class WebPushNotification {
    pluginUrl = '/plugins/web-push-notification/';
    constructor() {
        this.addPwaManifest();
        this.bindEvents();
    }
    __(text = '') {
        return wpm_lng[text] || text;
    }
    checkAPI() {
        if (!('serviceWorker' in navigator))
            this.error(this.__('no_service_worker_support'));

        if (!('PushManager' in window))
            this.error(this.__('no_push_api_support'));
    }
    checkResgistration() {
        // Get service worker registrations
        navigator.serviceWorker.getRegistrations()
            .then((registrations) => {
                if (!registrations || !registrations.length) {
                    document.querySelector('#wpn-permission-status').innerText = this.__('permissions_granted_not_registered');
                    document.querySelector('#wpn-permission-status').setAttribute('data-status', 'orange');
                    document.querySelectorAll('#wpn-permission-btn').forEach(x => x.removeAttribute('disabled'));
                    return;
                }

                registrations.forEach((registration) => {
                    registration?.pushManager.getSubscription()
                        .then((subscription) => {
                            document.querySelector('#wpn-permission-status').innerText = this.__('permissions_granted_registered');
                            document.querySelector('#wpn-permission-status').setAttribute('data-status', 'green');
                            document.querySelectorAll('#wpn-permission-btn').forEach(x => x.removeAttribute('disabled'));
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
                document.querySelector('#wpn-permission-btn').removeAttribute('disabled');
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
            const response = await fetch(this.pluginUrl + 'actions.php?action=get_csrf_token');
            const result = await response.json();

            if (result.errno)
                this.error(result.errmsg);

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
                throw new Error(result.errmsg);

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
                this.error(result.errmsg);

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
                this.error(result.errmsg);

            return result.data.publicKey || false;
        } catch (e) {
            this.error(e);
        }
    }
    async saveConfig() {
        try {
            const csrfToken = await this.getCSRFToken();
            const data = new URLSearchParams();
            data.append('csrf_token', csrfToken.data.csrf_token);
            data.append('wpn-enable', document.querySelector('#wpn-enable-select').value == '1' ? 'enable' : 'disable');
            document.querySelectorAll('input[name="wpn-silent[]"]:checked').forEach((elem) => data.append('wpn-silent[]', elem.value));

            const response = await fetch(this.pluginUrl + 'actions.php?action=config', {
                method: 'post',
                body: data,
            });
            const result = await response.json();

            if (result.errno)
                this.error(result.errmsg);

            return result;
        } catch (e) {
            this.error(e);
        }
    }
    async saveSubscription(subscription) {
        try {
            const csrfToken = await this.getCSRFToken();
            const data = new URLSearchParams();
            data.append('csrf_token', csrfToken.data.csrf_token);
            data.append('subscription', JSON.stringify(subscription));

            const response = await fetch(this.pluginUrl + 'actions.php?action=save_device', {
                method: 'post',
                body: data,
            });
            const result = await response.json();

            if (result.errno)
                this.error(result.errmsg);

            return result;
        } catch (e) {
            this.error(e);
        }
    }
    async removeSubscription(endpoint, remoteDelete = false) {
        try {
            const csrfToken = await this.getCSRFToken();
            const data = new URLSearchParams();
            data.append('csrf_token', csrfToken.data.csrf_token);
            data.append('subscription', JSON.stringify(endpoint));
            data.append('remote_delete', remoteDelete);

            const response = await fetch(this.pluginUrl + 'actions.php?action=remove_device', {
                method: 'post',
                body: data,
            });
            const result = await response.json();

            if (result.errno)
                this.error(result.errmsg);

            return result;
        } catch (e) {
            this.error(e);
        }
    }
    requestNotificationPermission() {
        Notification.requestPermission()
            .then((permission) => {
                if (permission == 'granted') {
                    this.registerServiceWorker();
                } else {
                    this.displayWebPushNotificationStatus();
                }
            }).catch((e) => {
                this.error(e);
            });
    }
    registerServiceWorker() {
        // Register service worker
        navigator.serviceWorker.register(this.pluginUrl + 'assets/js/serviceworker.php')
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
                    if (event.target.state == 'activated') {
                        Promise.resolve(this.getPublicKey())
                            .then((publicKey) => {
                                const options = {
                                    userVisibleOnly: true,
                                    applicationServerKey: this.urlBase64ToUint8Array(publicKey),
                                };

                                // Subscribe to push service
                                registration?.pushManager.subscribe(options)
                                    .then(async (subscription) => {
                                        await this.saveSubscription(subscription);

                                        document.querySelector('#wpn-list-btn')?.classList.remove('active');
                                        const html = document.querySelector('#wpn-device-list');
                                        html.innerHTML = '';
                                        html.style.display = 'none';

                                        this.displayWebPushNotificationStatus();
                                    }).catch((e) => {
                                        const permissionStatus = document.querySelector('#wpn-permission-status');

                                        permissionStatus.innerText = this.__('error_subscribing');
                                        permissionStatus.setAttribute('data-status', 'red');

                                        this.error('', e);
                                    });
                            });
                    }
                };
            }).catch((e) => {
                const permissionStatus = document.querySelector('#wpn-permission-status');

                permissionStatus.innerText = this.__('error_registering');
                permissionStatus.setAttribute('data-status', 'red');

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
                            if (!subscription) {
                                this.error(this.__('service_worker_not_registered'));
                                return;
                            }

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
            // Remove subscription
            subscription.unsubscribe()
                .then(() => {
                    // Remove subscription on server
                    this.removeSubscription(subscription.endpoint);

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
        Promise.resolve(this.getDevicesList())
            .then((devices) => {
                const tableCols = [
                    this.__('action'),
                    this.__('date'),
                    this.__('user_agent'),
                    this.__('ip_address'),
                ];
                const html = document.querySelector('#wpn-device-list');

                const table = document.createElement('table');
                html.append(table);
                table.classList.add('tablesorter');

                const thead = document.createElement('thead');
                table.append(thead);

                const tr = document.createElement('tr');
                thead.append(tr);

                tableCols.forEach((col) => {
                    const th = document.createElement('th');
                    tr.append(th);
                    th.innerText = col;
                });

                const p = document.createElement('p');
                html.append(p);
                p.innerText = this.__('loading');

                this.getCurrentSubscription((subscription) => {
                    if (!devices?.data?.length) {
                        p.innerText = this.__('no_devices');
                        return;
                    } else {
                        p.remove();
                    }

                    const tbody = document.createElement('tbody');
                    table.append(tbody);

                    devices?.data?.forEach((device) => {
                        const tr = document.createElement('tr');
                        tbody.append(tr);

                        const tdAction = document.createElement('td');
                        tr.append(tdAction);

                        const tdActionRemove = document.createElement('button');
                        tdAction.append(tdActionRemove);
                        tdActionRemove.innerText = this.__('remove');
                        tdActionRemove.onclick = async () => {
                            if (!confirm(this.__('remove_device')))
                                return;

                            let remoteDelete = true;
                            if (device.subscription.endpoint == subscription.endpoint) {
                                this.unregisterServiceWorker();
                                remoteDelete = false;
                            }

                            await this.removeSubscription(device.subscription.endpoint, remoteDelete);

                            const html = document.querySelector('#wpn-device-list');
                            html.innerHTML = '';
                            this.displayDevicesList();
                        };

                        const tdDate = document.createElement('td');
                        tr.append(tdDate);
                        tdDate.innerText = new Date(device.datetime).toLocaleString();

                        const tdUserAgent = document.createElement('td');
                        tr.append(tdUserAgent);
                        const d = this.parseUserAgent(device.user_agent);
                        tdUserAgent.innerHTML = d ? `<strong>${d}</strong>` : '';
                        if (device.subscription.endpoint == subscription.endpoint) {
                            const currentDevice = document.createElement('strong');
                            tdUserAgent.append(currentDevice);
                            currentDevice.innerText = ` (${this.__('current_device')})`;
                            currentDevice.style.color = 'green';
                        }
                        tdUserAgent.innerHTML += '<br>';
                        tdUserAgent.innerHTML += device.user_agent;

                        const tdIpAddress = document.createElement('td');
                        tr.append(tdIpAddress);
                        tdIpAddress.innerText = device.ip_address;
                    });
                });
            });
    }
    parseUserAgent(userAgent = '') {
        const out = [];

        if (/Android/i.test(userAgent))
            out.push('Android');
        else if (/Edg/i.test(userAgent))
            out.push('Edge');
        else if (/Windows/i.test(userAgent))
            out.push('Windows');
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
        else if (/Chromium/i.test(userAgent))
            out.push('Chromium');
        else if (/Chrome/i.test(userAgent))
            out.push('Chrome');
        else if (/Safari/i.test(userAgent))
            out.push('Safari');

        return out.join(' / ');
    }
    addPwaManifest() {
        const linkManifestTag = document.createElement('link');
        linkManifestTag.href = '/plugins/web-push-notification/assets/site.webmanifest';
        linkManifestTag.rel = 'manifest';
        linkManifestTag.crossOrigin = 'use-credentials';
        document.querySelector('head').append(linkManifestTag);

        const linkIconTag = document.createElement('link');
        linkIconTag.href = '/plugins/web-push-notification/assets/images/icon512_maskable.png';
        linkIconTag.rel = 'icon';
        linkIconTag.type = 'image/png';
        linkIconTag.sizes = '512x512';
        linkIconTag.crossOrigin = 'use-credentials';
        document.querySelector('head').append(linkIconTag);
    }
    bindEvents() {
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
            html.innerHTML = '';

            if (event.target.classList.contains('active')) {
                event.target.classList.remove('active');
                html.style.display = 'none';
            } else {
                this.displayDevicesList();
                event.target.classList.add('active');
                html.style.display = 'block';
            }
        }
        document.querySelector('#wpn-permission-btn').onclick = (event) => {
            this.checkAPI();
            document.querySelector('#wpn-permission-status').innerText = this.__('registration_progress');
            document.querySelector('#wpn-permission-status').setAttribute('data-status', '');
            this.requestNotificationPermission();
        };
        document.querySelector('#wpn-generate-vapid-btn').onclick = (event) => {
            if (!confirm(this.__('generate_vapid_keys')))
                return;
            Promise.resolve(this.generateVapid())
                .then((publicKey) => {
                    const data = publicKey.data || { 'publicKey': this.__('error_while_generating'), 'privateKey': this.__('error_while_generating') };
                    document.querySelector('#wpn-publicKey').value = data.publicKey;
                    document.querySelector('#wpn-privateKey').value = data.privateKey;
                    document.querySelectorAll('#wpn-test-bt, #wpn-permission-btn').forEach(x => x.removeAttribute('disabled'));

                    document.querySelector('#wpn-list-btn')?.classList.remove('active');
                    const html = document.querySelector('#wpn-device-list');
                    html.innerHTML = '';
                    html.style.display = 'none';
                });
        }
        document.querySelector('#wpn-test-btn').onclick = async (event) => {
            try {
                fetch(this.pluginUrl + 'actions.php?action=test');
            } catch (e) {
                this.error(e);
            }
        }
    }
    error(msg, e = null) {
        const error = document.querySelector('#wpn-error');
        error.style.display = 'flex';

        const text = document.querySelector('#wpn-error-text');
        text.innerText = msg;
        if (e) {
            text.innerHTML += '<br>';
            text.innerText += e;
            console.error(e);
        }
        text.style.display = 'block';
        throw new Error(msg);
    }
}
new WebPushNotification();

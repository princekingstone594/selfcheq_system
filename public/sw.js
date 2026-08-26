const CACHE_NAME = 'selfcheq-v2';

const APP_SHELL = [
    '/',
    '/dashboard',
    '/tasks',
    '/routines',
    '/journal',
    '/focus',
    '/progress',
    '/calendar',
    '/coach',
    '/settings',
    '/notification-worker.js',
];

const ICON_192 = '/icon-192.png';
const ICON_512 = '/icon-512.png';

// Install — cache the app shell
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(APP_SHELL))
    );
    self.skipWaiting();
});

// Activate — clean up old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(name => name !== CACHE_NAME)
                    .map(name => caches.delete(name))
            );
        })
    );
    self.clients.claim();
});

// Fetch — network-first for page navigations (always fresh HTML),
// cache-first for static assets.
self.addEventListener('fetch', event => {
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    // Refresh the cached copy in the background
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, copy));
                    return response;
                })
                .catch(() => caches.match(event.request))
        );
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then(cachedResponse => {
                return cachedResponse || fetch(event.request);
            })
    );
});

// Listen for messages from the app (push notifications)
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

// Handle push notifications
self.addEventListener('push', event => {
    if (!event.data) {
        return;
    }

    const data = event.data.json();

    const options = {
        body: data.body || 'You have a new notification from SelfCheq!',
        icon: ICON_192,
        badge: ICON_192,
        image: data.image ? data.image : undefined,
        tag: data.tag || 'selfcheq-notification',
        data: {
            url: data.url || '/dashboard',
            ...data.data,
        },
        actions: data.actions || [
            {
                action: 'open',
                title: 'Open App',
                icon: ICON_192,
            },
            {
                action: 'dismiss',
                title: 'Dismiss',
                icon: ICON_192,
            },
        ],
        requireInteraction: data.requireInteraction || false,
        silent: false,
        vibrate: [200, 100, 200],
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'SelfCheq', options)
    );
});

// Handle notification clicks
self.addEventListener('notificationclick', event => {
    event.notification.close();

    const urlToOpen = event.notification.data?.url || '/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(clientList => {
                // If a tab is already open, focus it and navigate
                for (let i = 0; i < clientList.length; i++) {
                    const client = clientList[i];
                    if (client.url === urlToOpen && 'focus' in client) {
                        return client.focus();
                    }
                }

                // Otherwise open a new tab
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Handle notification close
self.addEventListener('notificationclose', event => {
    // Clean up or log when a notification is dismissed
    console.log('Notification closed:', event.notification.tag);
});
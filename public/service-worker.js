const CACHE_NAME = 'zintoop-cache-v3';
const STATIC_ASSETS = [
  '/manifest.webmanifest',
  '/icons/zintoop-192.png',
  '/icons/zintoop-512.png',
  '/images/zintoop-logo.png',
  '/images/oliveoiltandefault.jpg'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.map((key) => (key === CACHE_NAME ? null : caches.delete(key))))
    ).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);
  
  // Bypass service worker for external/cross-origin requests
  if (url.origin !== self.location.origin) return;
  
  // For HTML pages (no extension or .php), use network-first to preserve auth state
  const isHtmlRequest = event.request.headers.get('accept')?.includes('text/html') ||
                        (!url.pathname.includes('.') || url.pathname.endsWith('.php'));
  
  if (isHtmlRequest) {
    // Network-first for HTML - always get fresh content for auth state
    event.respondWith(
      fetch(event.request)
        .catch(() => caches.match('/offline.html') || new Response('Offline', { status: 503 }))
    );
    return;
  }
  
  // Cache-first for static assets only
  event.respondWith(
    caches.match(event.request).then((cached) => {
      if (cached) return cached;
      return fetch(event.request).then((response) => {
        // Only cache successful responses for static assets
        if (response.ok && (url.pathname.match(/\.(js|css|png|jpg|jpeg|webp|svg|woff2?)$/))) {
          const clone = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
        }
        return response;
      });
    })
  );
});

self.addEventListener('push', (event) => {
  if (!event.data) return;
  const payload = event.data.json();
  const title = payload.title || 'ZinToop';
  const options = {
    body: payload.body || '',
    icon: payload.icon || '/icons/zintoop-192.png',
    badge: payload.badge || '/icons/zintoop-192.png',
    data: {
      url: payload.url || '/',
    },
  };

  event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const targetUrl = event.notification?.data?.url || '/';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      for (const client of clientList) {
        if (client.url.includes(targetUrl) && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
      return null;
    })
  );
});

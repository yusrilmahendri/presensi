const CACHE_NAME = 'presensi-v1.0.0';
const urlsToCache = [
  '/',
  '/dashboard',
  '/css/app.css',
  '/js/app.js',
  '/offline.html',
  '/manifest.json'
];

// Install event - cache resources
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Cache hit - return response
        if (response) {
          return response;
        }

        return fetch(event.request).then(
          response => {
            // Check if valid response
            if (!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }

            // Clone the response
            const responseToCache = response.clone();

            caches.open(CACHE_NAME)
              .then(cache => {
                cache.put(event.request, responseToCache);
              });

            return response;
          }
        );
      })
      .catch(() => {
        // If both cache and network fail, show offline page
        return caches.match('/offline.html');
      })
  );
});

// Background sync for attendance
self.addEventListener('sync', event => {
  if (event.tag === 'sync-attendance') {
    event.waitUntil(syncAttendance());
  }
});

// Push notification
self.addEventListener('push', event => {
  const options = {
    body: event.data ? event.data.text() : 'Notifikasi baru',
    icon: '/images/icon-192x192.png',
    badge: '/images/badge-72x72.png',
    vibrate: [200, 100, 200],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    }
  };

  event.waitUntil(
    self.registration.showNotification('Sistem Presensi', options)
  );
});

// Notification click
self.addEventListener('notificationclick', event => {
  event.notification.close();
  event.waitUntil(
    clients.openWindow('/')
  );
});

async function syncAttendance() {
  try {
    const cache = await caches.open(CACHE_NAME);
    const requests = await cache.keys();
    const attendanceRequests = requests.filter(req => req.url.includes('/attendance'));
    
    for (const request of attendanceRequests) {
      try {
        await fetch(request);
        await cache.delete(request);
      } catch (error) {
        console.error('Sync failed:', error);
      }
    }
  } catch (error) {
    console.error('Sync attendance failed:', error);
  }
}

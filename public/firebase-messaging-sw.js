importScripts("https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging-compat.js");

const firebaseConfig = {
  apiKey: "AIzaSyDsvRDC2OKIUhyQhE0eEYmCddF92_WNud8",
  authDomain: "siakad-6d044.firebaseapp.com",
  projectId: "siakad-6d044",
  storageBucket: "siakad-6d044.firebasestorage.app",
  messagingSenderId: "65444849835",
  appId: "1:65444849835:web:8f7f0f2d190572de2e2fee",
  measurementId: "G-2X7HDW3CF8"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

const CACHE_NAME = 'siakad-cache-v4';
const urlsToCache = [
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
  'https://code.jquery.com/jquery-3.7.0.min.js'
];

// ─── Install ───────────────────────────────────────────────────────────────
self.addEventListener('install', event => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
  );
});

// ─── Activate ──────────────────────────────────────────────────────────────
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
    ).then(() => self.clients.claim())
  );
});

// ─── Fetch (Network-first with graceful fallback) ─────────
self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;
  if (!event.request.url.startsWith('http')) return;

  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request).then(response => {
        if (response) {
          return response;
        }
        return new Response('Network error or offline', { 
          status: 503, 
          statusText: 'Service Unavailable',
          headers: new Headers({ 'Content-Type': 'text/plain' })
        });
      });
    })
  );
});

// ─── Firebase Push Notification Received (Background) ──────────────────────
messaging.onBackgroundMessage((payload) => {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  
  // Extract data from FCM payload
  const notificationTitle = payload.notification?.title || payload.data?.title || '🔔 SIAKAD SMK Sunan Giri';
  const notificationOptions = {
    body: payload.notification?.body || payload.data?.body || 'Anda memiliki pemberitahuan baru.',
    icon: payload.notification?.icon || payload.data?.icon || '/images/logo-sekolah.png',
    badge: '/images/logo-sekolah.png',
    tag: 'siakad-notification',
    data: {
      url: payload.data?.url || '/orangtua/notifications'
    }
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});

// ─── Notification Click ────────────────────────────────────────────────────
self.addEventListener('notificationclick', event => {
  event.notification.close();

  if (event.action === 'dismiss') return;

  const targetUrl = (event.notification.data && event.notification.data.url)
    ? event.notification.data.url
    : '/orangtua/notifications';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientList => {
      for (const client of clientList) {
        if (client.url.includes(self.location.origin) && 'focus' in client) {
          client.navigate(targetUrl);
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});

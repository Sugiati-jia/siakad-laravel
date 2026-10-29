// Service Worker for SIAKAD SMK Sunan Giri PWA
const CACHE_NAME = 'siakad-v1';

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

self.addEventListener('fetch', (event) => {
    // Passthrough — tidak cache apapun, hanya agar PWA bisa install
    event.respondWith(fetch(event.request));
});

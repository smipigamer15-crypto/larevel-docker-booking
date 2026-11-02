const OFFLINE_CACHE = "booking-offline-v1";
self.addEventListener("install", (event) => {
    event.waitUntil(caches.open(OFFLINE_CACHE).then((cache) => cache.add("/offline.html")));
    self.skipWaiting();
});
self.addEventListener("activate", (event) => {
    event.waitUntil(Promise.all([
        caches.keys().then((keys) => Promise.all(keys.filter((key) => key.startsWith("booking-offline-") && key !== OFFLINE_CACHE).map((key) => caches.delete(key)))),
        self.clients.claim()
    ]));
});
self.addEventListener("fetch", (event) => {
    if (event.request.method === "GET" && event.request.mode === "navigate") {
        event.respondWith(fetch(event.request).catch(() => caches.match("/offline.html")));
    }
});

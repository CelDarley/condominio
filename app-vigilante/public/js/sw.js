self.addEventListener('install', event => {
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    console.log('Service Worker ativado');
    clients.claim();
});

self.addEventListener('message', event => {
    if (event.data.action === 'pingLocation') {
        navigator.serviceWorker.controller.postMessage({action: 'requestLocation'});
    }
});

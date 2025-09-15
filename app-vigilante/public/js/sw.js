self.addEventListener('install', event => {
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    console.log('Service Worker ativado');
    clients.claim();
});

function solicitarLocalizacao() {
    self.clients.matchAll().then(allClients => {
        allClients.forEach(client => {
            client.postMessage({ action: 'requestLocation' });
        });
    });
}

// Envia solicitação de localização a cada 1 minuto
setInterval(solicitarLocalizacao, 60 * 1000);

const CACHE_NAME = 'segcond-v1';
const urlsToCache = [
    '/',
    '/static/css/style.css',
    '/static/js/app.js',
    '/static/icons/icon-192x192.png',
    '/static/icons/icon-512x512.png',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'
];

// Instalação do Service Worker
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('Cache aberto');
                return cache.addAll(urlsToCache);
            })
            .catch((error) => {
                console.error('Erro ao fazer cache:', error);
            })
    );
});

// Ativação do Service Worker
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Removendo cache antigo:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Interceptação de requisições
self.addEventListener('fetch', (event) => {
    // Estratégia: Network First para API, Cache First para recursos estáticos
    if (event.request.url.includes('/api/') || event.request.url.includes('/registrar_presenca') || 
        event.request.url.includes('/enviar_aviso') || event.request.url.includes('/botao_panico')) {
        // Para APIs, usar Network First
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    // Clonar resposta para cache
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });
                    return response;
                })
                .catch(() => {
                    // Se falhar, tentar do cache
                    return caches.match(event.request);
                })
        );
    } else {
        // Para recursos estáticos, usar Cache First
        event.respondWith(
            caches.match(event.request)
                .then((response) => {
                    if (response) {
                        return response;
                    }
                    return fetch(event.request);
                })
        );
    }
});

// Sincronização em background
self.addEventListener('sync', (event) => {
    if (event.tag === 'background-sync') {
        event.waitUntil(
            // Implementar sincronização de dados offline
            console.log('Sincronização em background iniciada')
        );
    }
});

// Notificações push
self.addEventListener('push', (event) => {
    const options = {
        body: event.data ? event.data.text() : 'Nova notificação do SegCond',
        icon: '/static/icons/icon-192x192.png',
        badge: '/static/icons/icon-192x192.png',
        vibrate: [200, 100, 200],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'Ver Detalhes',
                icon: '/static/icons/icon-192x192.png'
            },
            {
                action: 'close',
                title: 'Fechar',
                icon: '/static/icons/icon-192x192.png'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification('SegCond', options)
    );
});

// Clique em notificação
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/')
        );
    } else if (event.action === 'close') {
        // Apenas fechar a notificação
        return;
    } else {
        // Clique na notificação principal
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// Mensagens do cliente
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: CACHE_NAME });
    }
});

// Gerenciamento de cache inteligente
const cleanOldCaches = async () => {
    const cacheNames = await caches.keys();
    const oldCaches = cacheNames.filter(name => name !== CACHE_NAME);
    
    await Promise.all(
        oldCaches.map(name => caches.delete(name))
    );
};

// Limpeza automática de cache antigo
self.addEventListener('activate', (event) => {
    event.waitUntil(cleanOldCaches());
});

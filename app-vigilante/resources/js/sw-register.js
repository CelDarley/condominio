export function registrarServiceWorker() {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => console.log('SW registered: ', registration))
                .catch(err => console.error('SW registration failed: ', err));
        });
    } else {
        console.warn('Service Worker não suportado neste navegador.');
    }
}

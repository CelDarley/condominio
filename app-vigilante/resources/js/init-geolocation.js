export function initGeolocation() {
    if (navigator.serviceWorker) {
        navigator.serviceWorker.addEventListener('message', async (event) => {
            if (event.data.action === 'requestLocation') {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        async (position) => {
                            try {
                                await fetch('/atualizar-localizacao', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({
                                        latitude: position.coords.latitude,
                                        longitude: position.coords.longitude
                                    })
                                });
                                console.log('Localização enviada com sucesso');
                            } catch (err) {
                                console.error('Erro ao enviar localização:', err);
                            }
                        },
                        (err) => console.error('Erro ao pegar localização:', err)
                    );
                } else {
                    console.error('Geolocalização não suportada');
                }
            }
        });

        async function sendPing() {
            const reg = await navigator.serviceWorker.ready;
            const sw = navigator.serviceWorker.controller || reg.active;
            if (sw) {
                sw.postMessage({ action: 'pingLocation' });
                console.log('[client] ping enviado');
            }
        }

        sendPing();

        setInterval(sendPing, 5 * 1000);
    }
}

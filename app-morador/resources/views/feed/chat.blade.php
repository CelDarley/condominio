@extends('layouts.pwa')

@section('title', 'Feed da Comunidade')

@section('styles')
<style>
    /* Layout estilo Chat */
    .chat-container {
        height: 100%;
        display: flex;
        flex-direction: column;
        background: #f8f9fa;
        position: relative;
        overflow: hidden;
        flex: 1;
    }

    .chat-header {
        background: linear-gradient(135deg, #364659 0%, #566273 100%);
        color: white;
        padding: 15px 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        z-index: 100;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: #f8f9fa;
        min-height: 0; /* Permite que o flex funcione corretamente */
    }

    .message-bubble {
        max-width: 70%;
        margin-bottom: 15px;
        position: relative;
        animation: messageSlideIn 0.3s ease;
    }

    .message-bubble.own {
        margin-left: auto;
        margin-right: 0;
    }

    .message-bubble.other {
        margin-left: 0;
        margin-right: auto;
    }

    .message-content {
        background: white;
        padding: 12px 16px;
        border-radius: 18px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        position: relative;
    }

    .message-bubble.own .message-content {
        background: linear-gradient(135deg, #364659 0%, #566273 100%);
        color: white;
        border-bottom-right-radius: 5px;
    }

    .message-bubble.own .message-text {
        color: white; /* Texto branco para mensagens próprias */
    }

    .message-bubble.own .message-time {
        color: rgba(255, 255, 255, 0.8); /* Tempo em branco com transparência */
    }

    .message-bubble.own .delete-btn {
        color: white; /* Lixeirinha branca para mensagens próprias */
        opacity: 0.8; /* Sempre um pouco visível */
    }

    .message-bubble.own:hover .delete-btn {
        opacity: 1; /* Totalmente visível no hover */
    }

    .message-bubble.other .message-content {
        background: white;
        border-bottom-left-radius: 5px;
    }

    .message-author {
        font-weight: 600;
        color: #364659;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .message-text {
        color: #333;
        font-size: 15px;
        line-height: 1.4;
        word-wrap: break-word;
    }

    .message-time {
        font-size: 11px;
        color: #999;
        margin-top: 6px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .message-actions {
        display: flex;
        gap: 10px;
        margin-top: 8px;
    }

    .message-action-btn {
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 10px;
        transition: background-color 0.2s;
    }

    .message-action-btn:hover {
        background: rgba(0,0,0,0.05);
    }

    .delete-btn {
        color: #e74c3c;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .message-bubble.own:hover .delete-btn {
        opacity: 1;
    }

    .message-media {
        margin-top: 8px;
        border-radius: 10px;
        overflow: hidden;
    }

    .message-media img {
        width: 100%;
        max-width: 300px;
        height: auto;
        display: block;
        object-fit: contain;
    }

    .message-media video {
        max-width: 100%;      /* limita largura da bolha */
        max-height: 40vh;     /* limita altura em relação à tela */
        border-radius: 10px;
        object-fit: contain;  /* mostra o vídeo inteiro sem cortar */
        background: #000;     /* se sobrar espaço, fica fundo preto */
    }

    /* Área de digitação fixa */
    .chat-input-area {
        background: white;
        padding: 15px 20px;
        border-top: 1px solid #ddd;
        position: sticky;
        bottom: 0;
        z-index: 100;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        min-height: 80px;
        flex-shrink: 0;
    }

    .input-container {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        background: #f8f9fa;
        border-radius: 25px;
        padding: 8px 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        border: 1px solid #e9ecef;
    }

    .message-input {
        flex: 1;
        border: none;
        outline: none;
        resize: none;
        font-size: 15px;
        line-height: 1.4;
        padding: 8px 0;
        max-height: 120px;
        min-height: 20px;
    }

    .message-input::placeholder {
        color: #999;
    }

    .media-buttons {
        position: relative;
        margin-right: 8px;
    }

    .media-btn-toggle {
        width: 32px;
        height: 32px;
        border: none;
        background: var(--primary-color);
        color: white;
        cursor: pointer;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        transform: rotate(0deg);
    }

    .media-btn-toggle.active {
        transform: rotate(45deg);
        background: #e74c3c;
    }

    .media-menu {
        position: absolute;
        bottom: 45px;
        left: 0;
        background: white;
        border-radius: 25px;
        padding: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border: 1px solid #e9ecef;
        display: none;
        flex-direction: column;
        gap: 8px;
        z-index: 1000;
    }

    .media-menu.show {
        display: flex;
        animation: menuSlideUp 0.3s ease;
    }

    .media-btn {
        width: 36px;
        height: 36px;
        border: none;
        background: #f8f9fa;
        color: #666;
        cursor: pointer;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .media-btn:hover {
        background: var(--primary-color);
        color: white;
        transform: scale(1.1);
    }

    @keyframes menuSlideUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .send-btn {
        width: 40px;
        height: 40px;
        border: none;
        background: linear-gradient(135deg, #364659 0%, #566273 100%);
        color: white;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .send-btn:hover:not(:disabled) {
        background: linear-gradient(135deg, #2a3a4a 0%, #4a5a6a 100%);
        transform: scale(1.05);
    }

    .send-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    .media-preview {
        margin-bottom: 10px;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .media-preview-item {
        position: relative;
        width: 60px;
        height: 60px;
        border-radius: 8px;
        overflow: hidden;
        background: #f0f0f0;
    }

    .media-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .media-remove-btn {
        position: absolute;
        top: 2px;
        right: 2px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: rgba(0,0,0,0.7);
        color: white;
        border: none;
        cursor: pointer;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #mediaInput {
        display: none;
    }

    /* Animações */
    @keyframes messageSlideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Scrollbar personalizada */
    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: rgba(0,0,0,0.2);
        border-radius: 3px;
    }

    /* Responsivo */
    @media (max-width: 768px) {
        .message-bubble {
            max-width: 85%;
        }

        .chat-input-area {
            padding: 10px 15px;
        }
    }
</style>
@endsection

@section('content')
<div class="chat-container">
    <!-- Header do Chat -->
    <div class="chat-header">
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="btn btn-link text-white p-0 me-3">
                <i class="fas fa-arrow-left fa-lg"></i>
            </a>
            <div>
                <h5 class="mb-0 text-white">Feed da Comunidade</h5>
                <small class="text-white-50">{{ $posts->total() }} mensagens</small>
            </div>
        </div>
    </div>

    <!-- Área de Mensagens -->
    <div class="chat-messages" id="messagesContainer">
        @forelse($posts as $post)
            <div class="message-bubble {{ $post->usuario_id === Auth::guard('morador')->id() ? 'own' : 'other' }}" data-post-id="{{ $post->id }}">
                <div class="message-content">
                    @if($post->usuario_id !== Auth::guard('morador')->id())
                        <div class="message-author">{{ $post->usuario->nome }}</div>
                    @endif

                    @if($post->conteudo)
                        <div class="message-text">{{ $post->conteudo }}</div>
                    @endif

                    @if($post->medias->count() > 0)
                        <div class="message-media">
                            @foreach($post->medias as $media)
                                @if($media->tipo === 'imagem')
                                    <img src="{{ $media->url }}" alt="{{ $media->arquivo_nome }}">
                                @elseif($media->tipo === 'video')
                                    <video controls>
                                        <source src="{{ $media->url }}" type="{{ $media->mime_type }}">
                                        Seu navegador não suporta vídeo.
                                    </video>
                                @elseif($media->tipo === 'audio')
                                    <audio controls>
                                        <source src="{{ $media->url }}" type="{{ $media->mime_type }}">
                                        Seu navegador não suporta áudio.
                                    </audio>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <div class="message-time">
                        <span>{{ $post->tempo_decorrido }}</span>
                        @if($post->usuario_id === Auth::guard('morador')->id())
                            <button class="delete-btn message-action-btn delete-post-btn" data-post-id="{{ $post->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma mensagem ainda</h5>
                <p class="text-muted">Seja o primeiro a compartilhar algo com a comunidade!</p>
            </div>
        @endforelse
    </div>

    <!-- Área de Digitação Fixa -->
    <div class="chat-input-area">
        <form id="messageForm" action="{{ route('feed.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Preview de mídias -->
            <div class="media-preview" id="mediaPreview"></div>
            <div id="mediaError" style="color: #e74c3c; font-size: 14px; margin-top: 5px;"></div>

            <div class="input-container">
                <div class="media-buttons">
                    <button type="button" class="media-btn-toggle" id="mediaToggle" title="Anexar mídia">
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="media-menu" id="mediaMenu">
                        <button type="button" class="media-btn" onclick="selectMedia('image/*')" title="Imagem">
                            <i class="fas fa-image"></i>
                        </button>
                        <button type="button" class="media-btn" onclick="selectMedia('video/*')" title="Vídeo">
                            <i class="fas fa-video"></i>
                        </button>
                        <button type="button" class="media-btn" onclick="selectMedia('audio/*')" title="Áudio">
                            <i class="fas fa-microphone"></i>
                        </button>
                    </div>
                </div>

                <textarea
                    name="conteudo"
                    class="message-input"
                    placeholder="Digite uma mensagem..."
                    rows="1"
                    id="messageInput"
                ></textarea>

                <button type="submit" class="send-btn" id="sendBtn" disabled>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            <input type="file" id="mediaInput" name="medias[]" multiple style="display: none;">
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const mediaInput = document.getElementById('mediaInput');
    const mediaPreview = document.getElementById('mediaPreview');
    const messagesContainer = document.getElementById('messagesContainer');
    const mediaToggle = document.getElementById('mediaToggle');
    const mediaMenu = document.getElementById('mediaMenu');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                     document.querySelector('input[name="_token"]')?.value;

    // Obter ID do usuário atual (escopo global)
    const currentUserId = {{ Auth::guard('morador')->id() ?? 'null' }};

    // Controle do menu de mídia
    mediaToggle.addEventListener('click', function() {
        const isActive = mediaMenu.classList.contains('show');

        if (isActive) {
            mediaMenu.classList.remove('show');
            mediaToggle.classList.remove('active');
        } else {
            mediaMenu.classList.add('show');
            mediaToggle.classList.add('active');
        }
    });

    // Fechar menu quando clicar fora
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.media-buttons')) {
            mediaMenu.classList.remove('show');
            mediaToggle.classList.remove('active');
        }
    });

    // Fechar menu após selecionar mídia
    const originalSelectMedia = window.selectMedia;
    window.selectMedia = function(accept) {
        originalSelectMedia(accept);
        mediaMenu.classList.remove('show');
        mediaToggle.classList.remove('active');
    };

    // Auto-resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        validateForm();
    });

    // Validar formulário
    function validateForm() {
        const hasText = messageInput.value.trim().length > 0;
        const hasFiles = mediaInput.files.length > 0;

        let hasError = false;
        const mediaError = document.getElementById('mediaError');
        mediaError.innerText = '';

        Array.from(mediaInput.files).forEach(file => {
            if (file.size > 10 * 1024 * 1024) { // 10 MB
                hasError = true;
                mediaError.innerText = `O arquivo "${file.name}" excede o limite de 10MB.`;
            }
        });

        sendBtn.disabled = hasError || !(hasText || hasFiles);
    }

    // Selecionar mídia
    function selectMedia(accept) {
        mediaInput.setAttribute('accept', accept);
        mediaInput.click();
    }
    window.selectMedia = selectMedia;

    // Preview de mídias
    mediaInput.addEventListener('change', function() {
        previewMedia();
        validateForm();
    });

    function previewMedia() {
        mediaPreview.innerHTML = '';

        Array.from(mediaInput.files).forEach((file, index) => {
            const previewItem = document.createElement('div');
            previewItem.className = 'media-preview-item';

            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                previewItem.appendChild(img);
            } else {
                const icon = document.createElement('div');
                icon.className = 'd-flex align-items-center justify-content-center h-100';
                icon.innerHTML = file.type.startsWith('video/') ?
                    '<i class="fas fa-video fa-2x"></i>' :
                    '<i class="fas fa-music fa-2x"></i>';
                previewItem.appendChild(icon);
            }

            const removeBtn = document.createElement('button');
            removeBtn.className = 'media-remove-btn';
            removeBtn.innerHTML = '×';
            removeBtn.onclick = () => removeMedia(index);

            previewItem.appendChild(removeBtn);
            mediaPreview.appendChild(previewItem);
        });
    }

    function removeMedia(index) {
        const dt = new DataTransfer();
        Array.from(mediaInput.files).forEach((file, i) => {
            if (i !== index) dt.items.add(file);
        });
        mediaInput.files = dt.files;
        previewMedia();
        validateForm();

        mediaInput.dispatchEvent(new Event('change'));
    }
    window.removeMedia = removeMedia;

    // Envio do formulário via AJAX
    document.getElementById('messageForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const originalText = sendBtn.innerHTML;

        // Desabilitar botão e mostrar loading
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            if (!response.ok) {

                if (response.status === 422) {
                    const data = await response.json();
                    alert(data.message || "Erro de validação nos arquivos!");
                }
            }else {
                return response.json();
            }
        })
        .then(data => {
            if (data.success) {
                // Limpar formulário
                this.reset();
                mediaPreview.innerHTML = '';
                messageInput.style.height = 'auto';
                validateForm();

                // Adicionar mensagem na tela imediatamente
                if (data.post) {
                    addNewMessage(data.post);
                }
            } else {
                alert('Erro ao enviar mensagem: ' + (data.error || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('❌ Erro ao enviar:', error);
            alert('Erro ao enviar mensagem: ' + error.message);
        })
        .finally(() => {
            // Reabilitar botão
            sendBtn.disabled = false;
            sendBtn.innerHTML = originalText;
            validateForm();
        });
    });

    // Função para adicionar nova mensagem
    function addNewMessage(postData) {
        const messageHtml = createMessageHTML(postData, true);
        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
        scrollToBottom();

        // Adicionar event listener para botão de deletar
        const newMessage = messagesContainer.querySelector(`[data-post-id="${postData.id}"]`);
        if (newMessage) {
            const deleteBtn = newMessage.querySelector('.delete-post-btn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', handleDeletePost);
            }
        }
    }

    // Função para criar HTML da mensagem
    function createMessageHTML(post, isOwn = false) {
        console.log(post);
        const mediasHtml = post.medias && post.medias.length > 0 ?
            `<div class="message-media">
                ${post.medias.map(media => {
                    if (media.tipo === 'imagem') {
                        return `<img src="${media.url}" alt="${media.arquivo_nome}">`;
                    } else if (media.tipo === 'video') {
                        return `
                            <video controls>
                                <source src="${media.url }" type="${ media.mime_type }">
                                Seu navegador não suporta vídeo.
                            </video>
                        `;
                    } else if (media.tipo === 'audio') {
                        return `
                            <audio controls>
                                <source src="${ media.url }" type="${ media.mime_type }">
                                Seu navegador não suporta áudio.
                            </audio>
                        `;
                    }
                    return '';
                }).join('')}
            </div>` : '';

        const authorHtml = !isOwn ? `<div class="message-author">${post.usuario.nome}</div>` : '';
        const deleteBtn = isOwn ?
            `<button class="delete-btn message-action-btn delete-post-btn" data-post-id="${post.id}">
                <i class="fas fa-trash"></i>
            </button>` : '';

        return `
            <div class="message-bubble ${isOwn ? 'own' : 'other'}" data-post-id="${post.id}">
                <div class="message-content">
                    ${authorHtml}
                    ${post.conteudo ? `<div class="message-text">${post.conteudo}</div>` : ''}
                    ${mediasHtml}
                    <div class="message-time">
                        <span>${post.tempo_decorrido}</span>
                        ${deleteBtn}
                    </div>
                </div>
            </div>
        `;
    }

    // Função para deletar mensagem
    function handleDeletePost() {
        if (confirm('Tem certeza que deseja deletar esta mensagem?')) {
            const postId = this.dataset.postId;

            fetch(`/feed/${postId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.closest('.message-bubble').remove();
                } else {
                    alert('Erro ao excluir mensagem: ' + (data.error || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('❌ Erro ao deletar:', error);
                alert('Erro ao excluir mensagem: ' + error.message);
            });
        }
    }

    // Aplicar event listeners nos posts existentes
    document.querySelectorAll('.delete-post-btn').forEach(btn => {
        btn.addEventListener('click', handleDeletePost);
    });

    // Scroll para o final
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Scroll inicial para o final
    scrollToBottom();

    // ===== WEBSOCKET - TEMPO REAL =====
    if (window.Echo) {
        console.log('🔌 Conectando ao WebSocket...');

        window.Echo.channel('feed-updates')
            .listen('.post.updated', (e) => {
                console.log('📡 Evento recebido:', e);

                if (e.action === 'created') {
                    // Só adicionar se for de outro usuário (para evitar duplicação)
                    if (e.post.usuario.id !== currentUserId) {
                        const messageHtml = createMessageHTML(e.post, false);
                        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                        scrollToBottom();
                    }
                } else if (e.action === 'deleted') {
                    // Remover mensagem da lista (qualquer usuário)
                    const messageElement = document.querySelector(`[data-post-id="${e.post.id}"]`);
                    if (messageElement) {
                        messageElement.style.transition = 'opacity 0.3s ease';
                        messageElement.style.opacity = '0';
                        setTimeout(() => messageElement.remove(), 300);
                    }
                }
            });
    } else {
        console.log('❌ Echo não está disponível');
    }
});
</script>
@endsection

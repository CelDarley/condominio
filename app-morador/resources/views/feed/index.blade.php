@extends('layouts.pwa')

@section('title', 'Feed da Comunidade')

@section('container-class', 'feed-page')

@section('styles')
<style>
    .feed-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .chat-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px 15px 150px 15px; /* Padding inferior aumentado para evitar sobreposição com barra de navegação */
        min-height: calc(100vh - 150px);
    }
    
    .post-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        overflow: hidden;
        border: 2px solid var(--primary-color); /* Borda azul suave */
    }
    
    .post-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
    }
    
    .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #364659 0%, #566273 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }
    
    .user-name {
        font-weight: 600;
        color: #333; /* Texto escuro no fundo branco */
    }
    
    .post-time {
        color: #666; /* Cinza para o tempo */
        font-size: 0.9em;
    }
    
    .post-content {
        padding: 20px;
        line-height: 1.6;
        color: #333; /* Texto escuro para as postagens */
    }
    
    .post-media {
        position: relative;
    }
    
    .post-media img {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .post-media video {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .post-actions {
        padding: 15px 20px;
        border-top: 1px solid #eee;
        display: flex;
        gap: 20px;
    }
    
    .action-btn {
        display: flex;
        align-items: center;
        gap: 5px;
        background: none;
        border: none;
        color: #666; /* Botões em cinza */
        cursor: pointer;
        transition: color 0.3s;
    }
    
    .action-btn:hover {
        color: var(--primary-color); /* Hover em azul */
    }
    
    .action-btn.liked {
        color: #e74c3c;
    }
    
    .comments-section {
        padding: 0 20px 20px;
        border-top: 1px solid #eee;
    }
    
    .comment {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .comment-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: linear-gradient(135deg, #364659 0%, #566273 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.8em;
        font-weight: bold;
    }
    
    .comment-content {
        background: #f8f9fa; /* Fundo cinza claro */
        padding: 10px 15px;
        border-radius: 20px;
        flex: 1;
        border: 1px solid #e9ecef;
    }
    
    .comment-author {
        font-weight: 600;
        font-size: 0.9em;
        margin-bottom: 2px;
        color: #333; /* Nome do autor em escuro */
    }
    
    .comment-text {
        font-size: 0.9em;
        line-height: 1.4;
        color: #555; /* Texto do comentário em cinza escuro */
    }
    
    .comment-time {
        font-size: 0.8em;
        color: #666; /* Tempo do comentário em cinza */
        margin-top: 5px;
    }
    
    .comment-form {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }
    
    .comment-input {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 25px;
        padding: 10px 15px;
        outline: none;
    }
    
    .comment-input:focus {
        border-color: #364659;
    }
    
    .comment-submit {
        background: #364659;
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .new-post-form {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 20px;
        margin-bottom: 20px;
        position: sticky;
        top: 20px;
        z-index: 100;
        border: 2px solid var(--primary-color); /* Borda azul suave igual aos posts */
    }
    
    .post-textarea {
        width: 100%;
        border: 1px solid #ddd;
        resize: none;
        outline: none;
        font-size: 16px;
        line-height: 1.5;
        min-height: 80px;
        background: white;
        color: #333;
        border-radius: 10px;
        padding: 10px;
    }
    
    .post-form-actions {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-top: 15px;
        gap: 15px;
    }
    
    .media-upload-btn {
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .post-submit-btn {
        background: #364659;
        color: white;
        border: none;
        border-radius: 25px;
        padding: 10px 20px;
        cursor: pointer;
        margin-left: auto;
    }
    
    .post-submit-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
    
    .media-preview {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        flex-wrap: wrap;
    }
    
    .media-preview-item {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        width: 100px;
        height: 100px;
    }
    
    .media-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .media-remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(0,0,0,0.7);
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        cursor: pointer;
        font-size: 12px;
    }
    
    /* Garantir que os inputs não sejam cobertos pela barra de navegação */
    .comment-form {
        margin-bottom: 20px;
        padding-bottom: 10px;
    }
    
    /* Espaçamento extra para o último elemento */
    .post-card:last-child {
        margin-bottom: 50px;
    }
    
    /* Ajuste para dispositivos móveis */
    @media (max-width: 768px) {
        .chat-container {
            padding-bottom: 180px !important; /* Muito mais espaço em dispositivos móveis */
        }
        
        .new-post-form {
            position: relative; /* Remover sticky em mobile para melhor UX */
            top: auto;
            margin-bottom: 30px;
        }
        
        .comment-form {
            margin-bottom: 30px;
            padding-bottom: 20px;
        }
        
        .post-card:last-child {
            margin-bottom: 80px;
        }
    }
    
    /* Espaçamento otimizado sem barra de navegação */
    .chat-container {
        padding-bottom: 80px !important;
        margin-bottom: 20px !important;
    }
    
    /* Botão de navegação flutuante */
    .feed-nav-button {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
    }
    
    .feed-nav-button .btn {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    
    .comment-form {
        margin-bottom: 20px !important;
        padding-bottom: 10px !important;
    }
    
    /* Garantir que o último elemento tenha espaço extra */
    .post-card:last-child .comment-form {
        margin-bottom: 40px !important;
        padding-bottom: 20px !important;
    }
    
    /* Espaçamento específico para mobile */
    @media screen and (max-width: 768px) {
        .chat-container {
            padding-bottom: 120px !important;
            margin-bottom: 30px !important;
        }
        
        .comment-form {
            margin-bottom: 30px !important;
            padding-bottom: 15px !important;
        }
        
        .post-card:last-child .comment-form {
            margin-bottom: 60px !important;
            padding-bottom: 30px !important;
        }
    }
</style>
@endsection

@section('content')
<!-- Botão de navegação flutuante para o feed -->
<div class="feed-nav-button">
    <button class="btn btn-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#feedNavModal">
        <i class="fas fa-bars"></i>
    </button>
</div>

<!-- Modal de navegação -->
<div class="modal fade" id="feedNavModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Navegação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i>Início
                    </a>
                    <a href="{{ route('alertas.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-bell me-2"></i>Alertas
                    </a>
                    <button class="btn btn-outline-danger" onclick="ativarPanico()">
                        <i class="fas fa-shield-alt me-2"></i>Pânico
                    </button>
                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#profileModal" data-bs-dismiss="modal">
                        <i class="fas fa-user me-2"></i>Perfil
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="chat-container">
    <!-- Formulário para criar novo post -->
    <div class="new-post-form">
        <form action="{{ route('feed.store') }}" method="POST" enctype="multipart/form-data" id="postForm">
            @csrf
            <div class="user-info mb-3">
                <div class="user-avatar">
                    {{ substr(Auth::guard('morador')->user()->nome, 0, 1) }}
                </div>
                <div>
                    <div class="user-name">{{ Auth::guard('morador')->user()->nome }}</div>
                    <small class="text-muted">Compartilhe algo com a comunidade...</small>
                </div>
            </div>
            
            <textarea 
                name="conteudo" 
                class="post-textarea" 
                placeholder="O que você gostaria de compartilhar?"
                rows="3"
            ></textarea>
            
            <div class="media-preview" id="mediaPreview"></div>
            
            <div class="post-form-actions">
                <input type="file" id="mediaInput" name="medias[]" multiple accept="image/*,video/*,audio/*" style="display: none;">
                
                <button type="button" class="media-upload-btn" onclick="document.getElementById('mediaInput').click()">
                    <i class="fas fa-camera"></i>
                    Foto/Vídeo
                </button>
                
                <button type="button" class="media-upload-btn" onclick="document.getElementById('mediaInput').setAttribute('accept', 'audio/*'); document.getElementById('mediaInput').click()">
                    <i class="fas fa-microphone"></i>
                    Áudio
                </button>
                
                <button type="submit" class="post-submit-btn" id="submitBtn" disabled>
                    Publicar
                </button>
            </div>
            
            @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            
            @if (session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
            @endif
        </form>
    </div>

    <!-- Feed de posts -->
    @forelse($posts as $post)
        <div class="post-card" data-post-id="{{ $post->id }}">
            <!-- Cabeçalho do post -->
            <div class="post-header">
                <div class="user-info">
                    <div class="user-avatar">
                        {{ substr($post->usuario->nome, 0, 1) }}
                    </div>
                    <div>
                        <div class="user-name">{{ $post->usuario->nome }}</div>
                        <div class="post-time">{{ $post->tempo_decorrido }}</div>
                    </div>
                    
                    @if($post->usuario_id === Auth::guard('morador')->id())
                        <div class="ms-auto">
                            <button class="btn btn-sm btn-outline-danger delete-post-btn" data-post-id="{{ $post->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Conteúdo do post -->
            @if($post->conteudo)
                <div class="post-content">
                    {{ $post->conteudo }}
                </div>
            @endif

            <!-- Mídia do post -->
            @if($post->tem_midia)
                <div class="post-media">
                    @foreach($post->medias as $media)
                        @if($media->isImagem())
                            <img src="{{ $media->url }}" alt="Imagem do post" loading="lazy">
                        @elseif($media->isVideo())
                            <video controls>
                                <source src="{{ $media->url }}" type="{{ $media->mime_type }}">
                                Seu navegador não suporta vídeo.
                            </video>
                        @elseif($media->isAudio())
                            <div class="p-3">
                                <audio controls class="w-100">
                                    <source src="{{ $media->url }}" type="{{ $media->mime_type }}">
                                    Seu navegador não suporta áudio.
                                </audio>
                                <small class="text-muted">{{ $media->arquivo_nome }} ({{ $media->tamanho_humano }})</small>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            <!-- Ações do post -->
            <div class="post-actions">
                <button class="action-btn like-btn" data-post-id="{{ $post->id }}">
                    <i class="fas fa-heart"></i>
                    <span class="likes-count">{{ $post->likes }}</span>
                </button>
                
                <button class="action-btn comment-toggle-btn">
                    <i class="fas fa-comment"></i>
                    <span>{{ $post->comentarios_count }}</span>
                </button>
            </div>

            <!-- Seção de comentários -->
            <div class="comments-section" style="display: none;">
                <div class="comments-list">
                    @foreach($post->comentarios as $comentario)
                        <div class="comment" data-comment-id="{{ $comentario->id }}">
                            <div class="comment-avatar">
                                {{ substr($comentario->usuario->nome, 0, 1) }}
                            </div>
                            <div class="comment-content">
                                <div class="comment-author">{{ $comentario->usuario->nome }}</div>
                                <div class="comment-text">{{ $comentario->comentario }}</div>
                                <div class="comment-time">{{ $comentario->tempo_decorrido }}</div>
                            </div>
                            
                            @if($comentario->usuario_id === Auth::guard('morador')->id())
                                <button class="btn btn-sm btn-outline-danger delete-comment-btn" data-comment-id="{{ $comentario->id }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <!-- Formulário para novo comentário -->
                <form class="comment-form" data-post-id="{{ $post->id }}">
                    @csrf
                    <input type="text" class="comment-input" placeholder="Escreva um comentário..." required>
                    <button type="submit" class="comment-submit">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Nenhum post ainda</h5>
            <p class="text-muted">Seja o primeiro a compartilhar algo com a comunidade!</p>
        </div>
    @endforelse

    <!-- Paginação -->
    @if($posts->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuração CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Elementos do formulário
    const postForm = document.getElementById('postForm');
    const mediaInput = document.getElementById('mediaInput');
    const mediaPreview = document.getElementById('mediaPreview');
    const submitBtn = document.getElementById('submitBtn');
    const textArea = document.querySelector('.post-textarea');
    
    // Validar se pode submeter o formulário
    function validateForm() {
        const hasText = textArea.value.trim().length > 0;
        const hasFiles = mediaInput.files.length > 0;
        submitBtn.disabled = !(hasText || hasFiles);
    }
    
    // Event listeners para validação
    textArea.addEventListener('input', validateForm);
    mediaInput.addEventListener('change', function() {
        previewMedia();
        validateForm();
    });
    
    // Interceptar envio do formulário para fazer via AJAX
    document.getElementById('postForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const originalText = submitBtn.textContent;
        
        // Desabilitar botão e mostrar loading
        submitBtn.disabled = true;
        submitBtn.textContent = 'Publicando...';
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
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
                // Limpar formulário
                this.reset();
                mediaPreview.innerHTML = '';
                validateForm();
                
                // Adicionar post na tela imediatamente
                if (data.post) {
                    addNewPostToFeed(data.post);
                }
                
                showNotification('📝 Post publicado com sucesso!', 'success');
            } else {
                showNotification('❌ Erro ao publicar: ' + (data.error || 'Erro desconhecido'), 'danger');
            }
        })
        .catch(error => {
            console.error('❌ Erro ao publicar:', error);
            showNotification('❌ Erro ao publicar: ' + error.message, 'danger');
        })
        .finally(() => {
            // Reabilitar botão
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            validateForm();
        });
    });
    
    // Preview de mídias selecionadas
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
    
    // Remover mídia do preview
    function removeMedia(index) {
        const dt = new DataTransfer();
        Array.from(mediaInput.files).forEach((file, i) => {
            if (i !== index) dt.items.add(file);
        });
        mediaInput.files = dt.files;
        previewMedia();
        validateForm();
    }
    
    // Toggle comentários
    document.querySelectorAll('.comment-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentsSection = this.closest('.post-card').querySelector('.comments-section');
            commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';
        });
    });
    
    // Curtir post
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const postId = this.dataset.postId;
            
            fetch(`/feed/${postId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.classList.toggle('liked');
                    this.querySelector('.likes-count').textContent = data.likes;
                }
            });
        });
    });
    
    // Comentar post
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const postId = this.dataset.postId;
            const input = this.querySelector('.comment-input');
            const comentario = input.value.trim();
            
            if (!comentario) return;
            
            fetch(`/feed/${postId}/comment`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ comentario })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Adicionar comentário à lista
                    const commentsList = this.previousElementSibling;
                    const newComment = document.createElement('div');
                    newComment.className = 'comment';
                    newComment.innerHTML = `
                        <div class="comment-avatar">${data.comentario.usuario_nome.charAt(0)}</div>
                        <div class="comment-content">
                            <div class="comment-author">${data.comentario.usuario_nome}</div>
                            <div class="comment-text">${data.comentario.comentario}</div>
                            <div class="comment-time">${data.comentario.tempo_decorrido}</div>
                        </div>
                    `;
                    commentsList.appendChild(newComment);
                    
                    // Limpar input
                    input.value = '';
                    
                    // Atualizar contador
                    const countSpan = this.closest('.post-card').querySelector('.comment-toggle-btn span');
                    countSpan.textContent = parseInt(countSpan.textContent) + 1;
                }
            });
        });
    });
    
    // Função para deletar post
    function handleDeletePost() {
        if (confirm('Tem certeza que deseja deletar este post?')) {
            const postId = this.dataset.postId;
            console.log('🗑️ Tentando deletar post ID:', postId);
            
            fetch(`/feed/${postId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('📡 Status da resposta:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('📦 Dados recebidos:', data);
                if (data.success) {
                    this.closest('.post-card').remove();
                    showNotification('🗑️ Post excluído com sucesso!', 'success');
                } else {
                    showNotification('❌ Erro ao excluir post: ' + (data.error || 'Erro desconhecido'), 'danger');
                }
            })
            .catch(error => {
                console.error('❌ Erro ao deletar:', error);
                showNotification('❌ Erro ao excluir: ' + error.message, 'danger');
            });
        }
    }

    // Função para like em post
    function handleLikePost() {
        // Implementar lógica de like aqui se necessário
        console.log('❤️ Like no post:', this.dataset.postId);
    }

    // Aplicar event listeners nos posts existentes
    document.querySelectorAll('.delete-post-btn').forEach(btn => {
        btn.addEventListener('click', handleDeletePost);
    });

    // Obter ID do usuário atual (escopo global)
    const currentUserId = {{ Auth::guard('morador')->id() ?? 'null' }};
    
    // ===== WEBSOCKET - TEMPO REAL =====
    if (window.Echo) {
        console.log('🔌 Conectando ao WebSocket...');
        
        window.Echo.channel('feed-updates')
            .listen('.post.updated', (e) => {
                console.log('📡 Evento recebido:', e);
                
                if (e.action === 'created') {
                    // Só adicionar se for de outro usuário (para evitar duplicação)
                    if (e.post.usuario.id !== currentUserId) {
                        addNewPostToFeed(e.post);
                        showNotification(`📝 ${e.post.usuario.nome} fez uma nova publicação!`, 'info');
                    }
                } else if (e.action === 'deleted') {
                    // Remover post da lista (qualquer usuário)
                    removePostFromFeed(e.post.id);
                }
            });
    } else {
        console.log('❌ Echo não está disponível');
    }

    // Função para adicionar novo post
    function addNewPostToFeed(postData) {
        const feedContainer = document.querySelector('.post-card')?.parentElement;
        if (!feedContainer) return;

        const postHtml = createPostHTML(postData);
        feedContainer.insertAdjacentHTML('afterbegin', postHtml);
        
        // Adicionar event listeners para o novo post
        const newPost = feedContainer.querySelector(`[data-post-id="${postData.id}"]`);
        if (newPost) {
            // Event listener para botão de deletar
            const deleteBtn = newPost.querySelector('.delete-post-btn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', handleDeletePost);
            }
            
            // Event listener para botão de like
            const likeBtn = newPost.querySelector('.like-btn');
            if (likeBtn) {
                likeBtn.addEventListener('click', handleLikePost);
            }
        }
        
        // Mostrar notificação
        showNotification('📝 Novo post adicionado!', 'success');
    }

    // Função para remover post
    function removePostFromFeed(postId) {
        const postElement = document.querySelector(`[data-post-id="${postId}"]`);
        if (postElement) {
            postElement.style.transition = 'opacity 0.3s ease';
            postElement.style.opacity = '0';
            setTimeout(() => {
                postElement.remove();
                showNotification('🗑️ Post removido', 'info');
            }, 300);
        }
    }

    // Função para criar HTML do post
    function createPostHTML(post) {
        const userInitial = post.usuario.nome.charAt(0).toUpperCase();
        const mediasHtml = post.medias.map(media => {
            if (media.tipo === 'imagem') {
                return `<img src="${media.url}" alt="${media.arquivo_nome}" class="post-image">`;
            }
            return '';
        }).join('');

        // Botão de deletar só aparece se for do usuário atual
        const deleteButton = post.usuario.id === currentUserId ? 
            `<div class="ms-auto">
                <button class="btn btn-sm btn-outline-danger delete-post-btn" data-post-id="${post.id}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>` : '';

        return `
            <div class="post-card" data-post-id="${post.id}" style="animation: slideInDown 0.5s ease;">
                <div class="post-header">
                    <div class="user-info">
                        <div class="user-avatar">${userInitial}</div>
                        <div>
                            <div class="user-name">${post.usuario.nome}</div>
                            <div class="post-time">${post.tempo_decorrido}</div>
                        </div>
                    </div>
                    ${deleteButton}
                </div>
                
                ${post.conteudo ? `<div class="post-content">${post.conteudo}</div>` : ''}
                
                ${mediasHtml ? `<div class="post-media">${mediasHtml}</div>` : ''}
                
                <div class="post-actions">
                    <button class="action-btn like-btn" data-post-id="${post.id}">
                        <i class="fas fa-heart"></i>
                        <span>${post.likes}</span>
                    </button>
                    <button class="action-btn comment-toggle-btn">
                        <i class="fas fa-comment"></i>
                        <span>${post.comentarios_count}</span>
                    </button>
                </div>
            </div>
        `;
    }

    // Função para mostrar notificações
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; animation: slideInRight 0.3s ease;';
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    // Ajuste para teclado virtual em dispositivos móveis
    function handleVirtualKeyboard() {
        const initialViewportHeight = window.innerHeight;
        
        window.addEventListener('resize', function() {
            const currentViewportHeight = window.innerHeight;
            const heightDifference = initialViewportHeight - currentViewportHeight;
            
            // Se a diferença de altura for significativa, provavelmente o teclado virtual está ativo
            if (heightDifference > 150) {
                document.body.style.paddingBottom = '20px';
                // Scroll para o elemento ativo se necessário
                const activeElement = document.activeElement;
                if (activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA')) {
                    setTimeout(() => {
                        activeElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 300);
                }
            } else {
                document.body.style.paddingBottom = '';
            }
        });
    }
    
    // Inicializar ajuste do teclado virtual
    if (/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        handleVirtualKeyboard();
    }
    
    // Scroll suave para campos focados (a barra de navegação já se esconde automaticamente)
    document.addEventListener('focusin', function(e) {
        if (e.target.matches('input, textarea')) {
            setTimeout(() => {
                e.target.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center',
                    inline: 'nearest'
                });
            }, 400); // Delay para aguardar a barra se esconder
        }
    });
});
</script>

<style>
@keyframes slideInDown {
    from { transform: translateY(-100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideOutRight {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}
</style>
@endsection 
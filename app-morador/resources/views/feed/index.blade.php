@extends('layouts.pwa')

@section('title', 'Feed da Comunidade')

@section('styles')
<style>
    .feed-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .post-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        overflow: hidden;
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
        color: #333;
    }
    
    .post-time {
        color: #666;
        font-size: 0.9em;
    }
    
    .post-content {
        padding: 20px;
        line-height: 1.6;
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
        color: #666;
        cursor: pointer;
        transition: color 0.3s;
    }
    
    .action-btn:hover {
        color: #364659;
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
        background: #f8f9fa;
        padding: 10px 15px;
        border-radius: 20px;
        flex: 1;
    }
    
    .comment-author {
        font-weight: 600;
        font-size: 0.9em;
        margin-bottom: 2px;
    }
    
    .comment-text {
        font-size: 0.9em;
        line-height: 1.4;
    }
    
    .comment-time {
        font-size: 0.8em;
        color: #666;
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
    }
    
    .post-textarea {
        width: 100%;
        border: none;
        resize: none;
        outline: none;
        font-size: 16px;
        line-height: 1.5;
        min-height: 80px;
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
</style>
@endsection

@section('content')
<div class="feed-container">
    <!-- Formulário para criar novo post -->
    <div class="new-post-form">
        <form action="{{ route('feed.store') }}" method="POST" enctype="multipart/form-data" id="postForm">
            @csrf
            <div class="user-info mb-3">
                <div class="user-avatar">
                    {{ substr(Auth::user()->nome, 0, 1) }}
                </div>
                <div>
                    <div class="user-name">{{ Auth::user()->nome }}</div>
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
                    
                    @if($post->usuario_id === Auth::id())
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
                            
                            @if($comentario->usuario_id === Auth::id())
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
    
    // Deletar post
    document.querySelectorAll('.delete-post-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Tem certeza que deseja deletar este post?')) {
                const postId = this.dataset.postId;
                
                fetch(`/feed/${postId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('.post-card').remove();
                    }
                });
            }
        });
    });
});
</script>
@endsection 
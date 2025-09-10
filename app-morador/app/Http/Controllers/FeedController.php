<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostMedia;
use App\Events\PostUpdated;

class FeedController extends Controller
{
    /**
     * Exibir o feed principal dos moradores
     */
    public function index()
    {
        $posts = Post::with(['usuario', 'comentarios.usuario', 'medias'])
                    ->ativos()
                    ->recentes()
                    ->paginate(10);

        return view('feed.index', compact('posts'));
    }

    public function chat()
    {
        $posts = Post::with(['usuario', 'medias'])
                    ->ativos()
                    ->orderBy('created_at', 'asc') // Ordem cronológica como WhatsApp
                    ->paginate(50); // Mais mensagens por página

        return view('feed.chat', compact('posts'));
    }

    /**
     * Criar um novo post
     */
    public function store(Request $request)
    {
        $request->validate([
            'conteudo' => 'nullable|string|max:1000',
            'medias.*' => 'file|max:10240', // 10MB máximo por arquivo
        ],
        [
            'medias.*.max' => 'O tamanho máximo de mídia não pode ultrapassar 10MB.'
        ]);

        // Validar que há conteúdo ou mídia
        if (!$request->conteudo && !$request->hasFile('medias')) {
            return back()->withErrors(['conteudo' => 'É necessário adicionar texto ou mídia ao post.']);
        }

        $usuario = Auth::guard('morador')->user();

        // Criar o post
        $post = Post::create([
            'usuario_id' => $usuario->id,
            'conteudo' => $request->conteudo,
            'tipo' => $request->hasFile('medias') ? 'imagem' : 'texto', // Será ajustado depois
            'ativo' => true,
        ]);

        // Processar arquivos de mídia se houver
        if ($request->hasFile('medias')) {
            $this->processarMedias($request->file('medias'), $post);
        }

        // Disparar evento para atualização em tempo real
        broadcast(new PostUpdated($post->load(['usuario', 'medias']), 'created'));

        // Se for requisição AJAX, retornar JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Post criado com sucesso!',
                'post' => [
                    'id' => $post->id,
                    'conteudo' => $post->conteudo,
                    'tipo' => $post->tipo,
                    'usuario' => [
                        'id' => $post->usuario->id,
                        'nome' => $post->usuario->nome,
                    ],
                    'likes' => $post->likes,
                    'comentarios_count' => $post->comentarios_count,
                    'tempo_decorrido' => $post->tempo_decorrido,
                    'medias' => $post->medias->map(function ($media) {
                        return [
                            'id' => $media->id,
                            'tipo' => $media->tipo,
                            'arquivo_nome' => $media->arquivo_nome,
                            'url' => route('media.show', $media->id),
                        ];
                    }),
                ]
            ]);
        }

        return redirect()->route('feed.index')->with('success', 'Post criado com sucesso!');
    }

    /**
     * Curtir/descurtir um post
     */
    public function like(Post $post)
    {
        // Aqui você pode implementar um sistema mais sofisticado de likes
        // Por agora, apenas incrementa o contador
        $post->incrementarLikes();

        return response()->json([
            'success' => true,
            'likes' => $post->likes
        ]);
    }

    /**
     * Adicionar comentário a um post
     */
    public function comment(Request $request, Post $post)
    {
        $request->validate([
            'comentario' => 'required|string|max:500'
        ]);

        $comentario = PostComment::create([
            'post_id' => $post->id,
            'usuario_id' => Auth::guard('morador')->id(),
            'comentario' => $request->comentario,
            'ativo' => true,
        ]);

        // Carregar o usuário para retornar na resposta
        $comentario->load('usuario');

        return response()->json([
            'success' => true,
            'comentario' => [
                'id' => $comentario->id,
                'usuario_nome' => $comentario->usuario->nome,
                'comentario' => $comentario->comentario,
                'tempo_decorrido' => $comentario->tempo_decorrido,
            ]
        ]);
    }

    /**
     * Deletar um post (apenas do próprio usuário)
     */
    public function destroy(Post $post)
    {
        \Log::info('🗑️ Tentativa de exclusão - Post ID: ' . $post->id);

        $usuario = Auth::guard('morador')->user();
        \Log::info('👤 Usuário logado: ' . ($usuario ? $usuario->id . ' - ' . $usuario->nome : 'NENHUM'));
        \Log::info('📝 Post pertence ao usuário: ' . $post->usuario_id);

        // Verificar se o usuário pode deletar o post
        if ($post->usuario_id !== $usuario->id) {
            \Log::warning('❌ Usuário não autorizado a deletar post');
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $post->update(['ativo' => false]);
        \Log::info('✅ Post marcado como inativo');

        // Disparar evento para atualização em tempo real
        broadcast(new PostUpdated($post, 'deleted'));
        \Log::info('📡 Evento WebSocket disparado');

        return response()->json(['success' => true]);
    }

    /**
     * Deletar um comentário (apenas do próprio usuário)
     */
    public function destroyComment(PostComment $comment)
    {
        $usuario = Auth::guard('morador')->user();

        // Verificar se o usuário pode deletar o comentário
        if ($comment->usuario_id !== $usuario->id) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $comment->update(['ativo' => false]);

        return response()->json(['success' => true]);
    }

    /**
     * Processar upload de mídias
     */
    private function processarMedias($arquivos, Post $post)
    {
        $ordem = 0;

        foreach ($arquivos as $arquivo) {
            $mimeType = $arquivo->getMimeType();
            $tipo = $this->determinarTipoMidia($mimeType);

            if (!$tipo) {
                continue; // Pular arquivos não suportados
            }

            // Gerar nome único para o arquivo
            $nomeArquivo = time() . '_' . uniqid() . '.' . $arquivo->getClientOriginalExtension();
            $pasta = 'posts/' . $post->id . '/' . $tipo . 's';

            // Fazer upload do arquivo
            $caminhoArquivo = $arquivo->storeAs($pasta, $nomeArquivo, 'sftp_server');

            // Criar registro na tabela post_medias
            PostMedia::create([
                'post_id' => $post->id,
                'tipo' => $tipo,
                'arquivo_path' => $caminhoArquivo,
                'arquivo_nome' => $arquivo->getClientOriginalName(),
                'mime_type' => $mimeType,
                'tamanho' => $arquivo->getSize(),
                'ordem' => $ordem++,
                'metadata' => $this->extrairMetadados($arquivo, $tipo),
            ]);

            // Atualizar tipo do post baseado na primeira mídia
            if ($ordem === 1) {
                $post->update(['tipo' => $tipo]);
            }
        }
    }

    /**
     * Determinar tipo de mídia baseado no MIME type
     */
    private function determinarTipoMidia($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'imagem';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        return null;
    }

    /**
     * Extrair metadados do arquivo
     */
    private function extrairMetadados($arquivo, $tipo)
    {
        $metadata = [];

        if ($tipo === 'imagem') {
            $imageInfo = getimagesize($arquivo->getPathname());
            if ($imageInfo) {
                $metadata['largura'] = $imageInfo[0];
                $metadata['altura'] = $imageInfo[1];
            }
        }

        return $metadata;
    }

    public function getMedia($id)
    {
        $media = PostMedia::findOrFail($id);

        try {
            // Buscar o conteúdo no servidor remoto via SFTP
            $conteudo = Storage::disk('sftp_server')->get($media->arquivo_path);

            return response($conteudo, 200)
                ->header('Content-Type', $media->mime_type);
        } catch (\Exception $e) {
            \Log::error("Erro ao buscar mídia {$id}: " . $e->getMessage());
            abort(404, 'Mídia não encontrada');
        }
    }
}

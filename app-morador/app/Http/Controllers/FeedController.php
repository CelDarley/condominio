<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostMedia;

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

    /**
     * Criar um novo post
     */
    public function store(Request $request)
    {
        $request->validate([
            'conteudo' => 'nullable|string|max:1000',
            'medias.*' => 'file|max:10240', // 10MB máximo por arquivo
        ]);

        // Validar que há conteúdo ou mídia
        if (!$request->conteudo && !$request->hasFile('medias')) {
            return back()->withErrors(['conteudo' => 'É necessário adicionar texto ou mídia ao post.']);
        }

        $usuario = Auth::user();
        
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
            'usuario_id' => Auth::id(),
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
        $usuario = Auth::user();
        
        // Verificar se o usuário pode deletar o post
        if ($post->usuario_id !== $usuario->id) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $post->update(['ativo' => false]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Deletar um comentário (apenas do próprio usuário)
     */
    public function destroyComment(PostComment $comment)
    {
        $usuario = Auth::user();
        
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
            $caminhoArquivo = $arquivo->storeAs($pasta, $nomeArquivo, 'public');
            
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
}

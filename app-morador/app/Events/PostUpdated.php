<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post;
    public $action; // 'created', 'deleted', 'updated'

    /**
     * Create a new event instance.
     */
    public function __construct(Post $post = null, string $action = 'updated')
    {
        $this->post = $post;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('feed-updates'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $data = [
            'action' => $this->action,
            'timestamp' => now()->toISOString(),
        ];

        if ($this->post) {
            $data['post'] = [
                'id' => $this->post->id,
                'conteudo' => $this->post->conteudo,
                'tipo' => $this->post->tipo,
                'usuario' => [
                    'id' => $this->post->usuario->id,
                    'nome' => $this->post->usuario->nome,
                ],
                'likes' => $this->post->likes,
                'comentarios_count' => $this->post->comentarios_count,
                'tempo_decorrido' => $this->post->tempo_decorrido,
                'medias' => $this->post->medias->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'tipo' => $media->tipo,
                        'arquivo_nome' => $media->arquivo_nome,
                        'url' => $media->url,
                    ];
                }),
            ];
        }

        return $data;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'post.updated';
    }
}

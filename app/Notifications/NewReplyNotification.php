<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reply;
    protected $comic;
    protected $responder;

    public function __construct($reply, $comic, $responder)
    {
        $this->reply = $reply;
        $this->comic = $comic;
        $this->responder = $responder;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'new_reply',
            'title' => 'Phản hồi mới',
            'message' => "**{$this->responder->name}** đã trả lời bình luận của bạn tại truyện **{$this->comic->title}**.",
            'image' => $this->responder->avatar_url ?? asset('images/default-avatar.png'),
            // Thêm hash #comment-id để cuộn tới bình luận
            'url' => route('user.comics.show', $this->comic->slug) . '#comment-' . $this->reply->id,
        ];
    }
}

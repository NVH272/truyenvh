<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewChapterNotification extends Notification
{
    use Queueable;

    protected $chapter;
    protected $comic;

    public function __construct($chapter, $comic)
    {
        $this->chapter = $chapter;
        $this->comic = $comic;
    }

    // 1. Đổi driver sang 'database'
    public function via($notifiable)
    {
        return ['database'];
    }

    // 2. Định nghĩa dữ liệu sẽ lưu vào DB
    public function toArray($notifiable)
    {
        return [
            'type' => 'new_chapter',
            'title' => 'Truyện mới cập nhật!',
            'message' => "Truyện **{$this->comic->title}** vừa ra chap **{$this->chapter->chapter_number}**.",
            'image' => $this->comic->cover_url, // Sử dụng accessor cover_url
            'url' => route('user.comics.show', $this->comic->slug),
        ];
    }
}

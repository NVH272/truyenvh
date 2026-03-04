<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Comic;
use App\Models\Chapter;

class ChapterErrorNotification extends Notification
{
    use Queueable;

    public $comic;
    public $chapter;
    public $description;
    public $reporterName;

    public function __construct(Comic $comic, Chapter $chapter, $description, $reporterName)
    {
        $this->comic = $comic;
        $this->chapter = $chapter;
        $this->description = $description;
        $this->reporterName = $reporterName;
    }

    public function via($notifiable)
    {
        return ['database']; // Chỉ lưu vào database
    }

    public function toDatabase($notifiable)
    {
        return [
            'type'       => 'chapter_error',
            'comic_id'   => $this->comic->id,
            'chapter_id' => $this->chapter->id,
            'title'      => 'Báo lỗi truyện: ' . $this->comic->title,
            'message'    => "{$this->reporterName} đã báo lỗi ở Chapter {$this->chapter->chapter_number}: {$this->description}",
            'url'        => route('poster.errors.index', $this->comic->id),
        ];
    }
}

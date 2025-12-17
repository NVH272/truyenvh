<?php

namespace App\Mail;

use App\Models\Comic;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComicReviewResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public Comic $comic;
    public string $result; // approved | rejected

    public function __construct(Comic $comic, string $result)
    {
        $this->comic  = $comic;
        $this->result = $result;
    }

    public function build()
    {
        return $this
            ->subject(
                $this->result === 'approved'
                    ? 'Truyện của bạn đã được phê duyệt'
                    : 'Truyện của bạn đã bị từ chối'
            )
            ->view('emails.comics.review_result');
    }
}

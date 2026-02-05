<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuestionReviewNotification extends Notification
{
    use Queueable;

    public function __construct(
        public $question,
        public string $action, // approved | rejected
        public ?string $reason = null
    ) {}

    public function via($notifiable)
    {
        return ['database']; // later: mail, fcm, slack
    }

    public function toArray($notifiable)
    {
        return [
            'question_id' => $this->question->id,
            'title'       => $this->question->title,
            'action'      => $this->action,
            'reason'      => $this->reason,
            'url'         => route('admin.questions.edit', $this->question->id),
        ];
    }
}

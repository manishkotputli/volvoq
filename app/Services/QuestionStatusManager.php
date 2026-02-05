<?php

namespace App\Services;

use App\Models\Question;
use App\Enums\QuestionStatus;
use Exception;

class QuestionStatusManager
{
    public static function transition(
        Question $question,
        string $to,
        ?int $byUser = null,
        ?string $reason = null
    ) {
        $from = $question->status;

        $allowed = [
            QuestionStatus::DRAFT => [
                QuestionStatus::REVIEW,
                QuestionStatus::FAILED,
            ],
            QuestionStatus::PENDING => [
                QuestionStatus::REVIEW,
                QuestionStatus::FAILED,
            ],
            QuestionStatus::REVIEW => [
                QuestionStatus::APPROVED,
                QuestionStatus::REJECTED,
                QuestionStatus::FAILED,
            ],
            QuestionStatus::APPROVED => [
                QuestionStatus::PUBLISHED,
            ],
        ];

        if (!isset($allowed[$from]) || !in_array($to, $allowed[$from])) {
            throw new Exception("Invalid transition: $from → $to");
        }

        $question->update([
            'status'        => $to,
            'reviewed_by'   => $byUser,
            'reviewed_at'   => in_array($to, [
                QuestionStatus::APPROVED,
                QuestionStatus::REJECTED
            ]) ? now() : null,
            'reject_reason' => $to === QuestionStatus::REJECTED ? $reason : null,
            'is_indexable'  => $to === QuestionStatus::PUBLISHED,
            'published_at'  => $to === QuestionStatus::PUBLISHED ? now() : null,
        ]);
    }
}

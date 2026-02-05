<?php

namespace App\Services;

use App\Models\Answer;
use Illuminate\Support\Facades\DB;
use Exception;

class AnswerStatusManager
{
    /**
     * Allowed lifecycle transitions
     */
    protected static array $allowed = [
        'draft' => ['review','failed'],
        'review' => ['published','rejected','failed'],
        'published' => [],
        'rejected' => [],
        'failed' => [],
    ];

    /**
     * Transition answer to next state
     */
    public static function transition(
        Answer $answer,
        string $to,
        ?int $actorId = null,
        ?string $reason = null
    ): void
    {
        $from = $answer->status;

        if (!in_array($to, self::$allowed[$from] ?? [])) {
            throw new Exception("Invalid Answer Transition: {$from} → {$to}");
        }

        DB::transaction(function () use ($answer, $to, $actorId, $reason) {

            // If accepting → unaccept previous
            if ($to === 'published' && $answer->is_accepted) {
                Answer::where('question_id',$answer->question_id)
                    ->where('id','!=',$answer->id)
                    ->update(['is_accepted'=>false]);
            }

            $answer->update([
                'status'        => $to,
                'reviewed_by'   => $actorId,
                'reviewed_at'   => now(),
                'reject_reason' => $reason,
            ]);
        });
    }

    /**
     * Accept this answer (SEO + UX)
     */
    public static function accept(Answer $answer, int $actorId): void
    {
        DB::transaction(function () use ($answer, $actorId) {

            Answer::where('question_id',$answer->question_id)
                ->update(['is_accepted'=>false]);

            $answer->update([
                'is_accepted' => true,
                'status'      => 'published',
                'reviewed_by' => $actorId,
                'reviewed_at' => now(),
            ]);
        });
    }
}

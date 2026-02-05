<?php

namespace App\Enums;

class QuestionStatus
{
    const DRAFT     = 'draft';
    const REVIEW    = 'review';
    const APPROVED  = 'approved';
    const REJECTED  = 'rejected';
    const PUBLISHED = 'published';
    const PENDING   = 'pending';
    const FAILED    = 'failed';

    public static function all(): array
    {
        return [
            self::DRAFT,
            self::REVIEW,
            self::APPROVED,
            self::REJECTED,
            self::PUBLISHED,
            self::PENDING,
            self::FAILED,
        ];
    }
}

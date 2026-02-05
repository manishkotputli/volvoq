<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'content',
        'answer_type',
        'is_primary',
        'sort_order',
        'status',
        'is_ai_generated',
        'ai_model',
        'ai_meta',
        'created_by',
        'upvotes',
        'downvotes',
        'extra'
    ];

    protected $casts = [
        'is_primary'      => 'boolean',
        'is_ai_generated' => 'boolean',
        'ai_meta'         => 'array',
        'extra'           => 'array',
    ];

    /* =========================
       RELATIONS
    ========================== */

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* =========================
       SCOPES (POWERFUL)
    ========================== */

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeAi($query)
    {
        return $query->where('is_ai_generated', true);
    }

    /* =========================
       HELPERS
    ========================== */

    public function markPrimary()
    {
        self::where('question_id', $this->question_id)
            ->update(['is_primary' => false]);

        $this->update(['is_primary' => true]);
    }

}

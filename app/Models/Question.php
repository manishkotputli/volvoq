<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use SoftDeletes;

   protected $fillable = [
    'title',
    'slug',
    'body',
    'category_id',
    'status',
    'created_by',
    'reviewed_by',
    'reviewed_at',
    'reject_reason',
    'is_indexable',
    'published_at',
];


    // Relations
    

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    // State guards
    public function canPublish(): bool
    {
        return $this->status === 'review'
            && $this->answers()->where('status', 'published')->count() > 0;
    }

    public function answers()
{
    return $this->hasMany(Answer::class)
        ->orderByDesc('is_primary')
        ->orderBy('sort_order');
}

public function creator()
{
    return $this->belongsTo(User::class,'created_by');
}

public function reviewer()
{
    return $this->belongsTo(User::class,'reviewed_by');
}

public function scopePublic($q)
{
    return $q->where('status', QuestionStatus::PUBLISHED);
}
public function isSlaBreached(): bool
{
    return $this->status === 'review'
        && $this->created_at->diffInHours(now()) > 24;
}


public function acceptedAnswer()
{
    return $this->hasOne(Answer::class)
        ->where('is_accepted', true);
}


}


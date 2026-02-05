<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'level',
        'name',
        'slug',
        'seo_title',
        'seo_description',
        'is_active',
        'sort_order',
        'extra',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'extra'     => 'array',
    ];

    /* ============================
        RELATIONSHIPS
    ============================ */

    // Parent category
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Direct children
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')
                    ->orderBy('sort_order');
    }

    // Recursive children (unlimited depth)
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    // Questions under this category
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /* ============================
        SCOPES (VERY IMPORTANT)
    ============================ */

    // Only active categories
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Root (super) categories
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    // By level
    public function scopeLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    /* ============================
        HELPERS
    ============================ */

    // Full breadcrumb path (SEO + UI)
    public function getBreadcrumbAttribute(): array
    {
        $trail = [];
        $category = $this;

        while ($category) {
            array_unshift($trail, $category);
            $category = $category->parent;
        }

        return $trail;
    }

    // Display name with indentation (admin dropdown)
    public function getIndentedNameAttribute(): string
    {
        return str_repeat('— ', $this->level) . $this->name;
    }
}

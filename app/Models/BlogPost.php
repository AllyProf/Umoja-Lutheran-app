<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'category',
        'tags',
        'author_name',
        'author_id',
        'is_published',
        'published_at',
        'views',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title') && empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    /**
     * Get the author user
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope for published posts
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(function($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->published_at 
            ? $this->published_at->format('d M, Y')
            : $this->created_at->format('d M, Y');
    }
}

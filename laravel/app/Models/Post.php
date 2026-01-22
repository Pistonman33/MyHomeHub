<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'status',
        'published_at',
    ];

    protected static function booted()
    {
        static::saving(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            if ($post->status === 'published' && !$post->published_at) {
                $post->published_at = now();
            }
        });
    }

    public function terms()
    {
        return $this->belongsToMany(Term::class);
    }

    public function categories()
    {
        return $this->terms()->where('type', 'category');
    }

    public function tags()
    {
        return $this->terms()->where('type', 'tag');
    }

}

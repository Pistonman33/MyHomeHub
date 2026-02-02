<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory; 
    
    protected $fillable = [
        'title',
        'slug',
        'content',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
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
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Term extends Model
{
    protected $fillable = ['name', 'slug', 'type']; 

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
    
    protected static function booted()
    {
        // generate automatically slug on creating
        static::creating(function ($term) {
            if (empty($term->slug)) {
                $term->slug = Str::slug($term->name);
            }
        });

        // generate automatically slug on updating mode
        static::updating(function ($term) {
            if (empty($term->slug)) {
                $term->slug = Str::slug($term->name);
            }
        });
    }    
}

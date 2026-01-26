<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Term;

class BlogController extends Controller
{
    public function front()
    {
        return view('frontend.blog.index');
    }
    public function post($slug)
    {
        $article = Post::where('slug', $slug)->first();
        return view('frontend.blog.post', ['post' => $article]);
    }
}
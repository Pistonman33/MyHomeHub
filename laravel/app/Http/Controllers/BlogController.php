<?php
namespace App\Http\Controllers;

use App\Post;

class BlogController extends Controller
{
    public function index()
    {
        return view('blog.posts.index');
    }

    public function create()
    {
        return view('blog.posts.create');
    }

    public function edit(Post $post)
    {
        return view('blog.posts.edit', compact('post'));
    }
}

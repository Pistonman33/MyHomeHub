<?php
namespace App\Http\Controllers;

use App\Models\Post;

class BlogController extends Controller
{
    /*************************/
    /* BACKEND with livewire */
    /*************************/
    public function index()
    {
        return view('blog.posts.index');
    }

    public function create()
    {
        return view('blog.posts.create');
    }

    public function edit($postid)
    {
        return view('blog.posts.edit', ['postId' => $postid]);
    }

    /************/
    /* Frontend */
    /************/
    
    public function front()
    {
        return view('blog.posts.front');
    }
}

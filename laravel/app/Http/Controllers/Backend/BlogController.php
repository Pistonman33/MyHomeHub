<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;


class BlogController extends Controller
{
    /*************************/
    /* BACKEND with livewire */
    /*************************/
    public function index()
    {
        return view('backend.blog.posts.index');
    }

    public function create()
    {
        return view('backend.blog.posts.create');
    }

    public function edit($postid)
    {
        return view('backend.blog.posts.edit', ['postId' => $postid]);
    }

    
}

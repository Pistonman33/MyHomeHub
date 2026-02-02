<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FriendsController extends Controller
{
    public function index()
    {
        return view('frontend.friends.index');
    }

    public function tailwindcss()
    {
        return view('frontend.test.tailwindcss');
    
    }
}
<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class CttController extends Controller
{
    public function index()
    {
        return view('frontend.ctt.dashboard');
    }
}
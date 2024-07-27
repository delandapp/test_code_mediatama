<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequestVideoController extends Controller
{
    public function index()
    {
        return view('menu.request-video.index');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NeedWantGameController extends Controller
{
    public function index()
    {
        return view('tools.need-want-game');
    }
}

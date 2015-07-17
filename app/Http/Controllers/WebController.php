<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class WebController extends BaseController
{
    public function index()
    {
        return view('index');
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword', null);
        return view('search', ['keyword' => $keyword]);
    }
}

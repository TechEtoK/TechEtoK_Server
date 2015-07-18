<?php

namespace App\Http\Controllers;

use App\Models\Words;
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

        $words = Words::query()->get()->all();

        return view('search', ['keyword' => $keyword, 'words' => $words]);
    }
}

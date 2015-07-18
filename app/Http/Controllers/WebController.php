<?php

namespace App\Http\Controllers;

use App\Models\Words;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

        // TODO: 검색어로 검색 결과 찾도록 변경해야함.

        $words = Words::all();
        return view('search', ['keyword' => $keyword, 'words' => $words]);
    }

    public function detail($word)
    {
        $word = urldecode($word);

        try {
            $word = Words::query()->where('word', '=', $word)->firstOrFail();
            return view('detail', ['word' => $word]);
        } catch (ModelNotFoundException $e) {
            return view('not_found');
        }
    }
}

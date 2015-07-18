<?php

namespace App\Http\Controllers;

use App\Models\Words;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class WebController extends BaseController
{
    public function index(Request $request)
    {
        $query = $request->input('q', null);
        if (!empty($query)) {
            $words = Words::searchWords($query);
        } else {
            $words = [];
        }

        return view('index', ['query' => $query, 'words' => $words]);
    }

    public function detail($word)
    {
        $word = urldecode($word);

        try {
            $word = Words::query()->where('word', '=', $word)->firstOrFail();
            // TODO: .md 파일 가지고 와서 내용을 뿌려주어야 한다.
            return view('detail', ['word' => $word]);
        } catch (ModelNotFoundException $e) {
            return view('not_found');
        }
    }
}

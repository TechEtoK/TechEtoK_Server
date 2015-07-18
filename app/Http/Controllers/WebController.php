<?php

namespace App\Http\Controllers;

use App\Models\Words;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // TODO: Eloquent ORM으로 변경해야함.
        $words = DB::table('words')->whereIn('id', function($q) use ($keyword) {
            $q->select('word_id')
                ->from('words_tags')
                ->where('tag', 'LIKE', '%' . $keyword . '%')
                ->groupBy('word_id');
        })->orWhere('word', '=', $keyword)->get();

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

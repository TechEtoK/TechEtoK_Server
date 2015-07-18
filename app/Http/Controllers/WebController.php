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

        // 검색 키워드와의 유사성에 따른 정렬
        foreach ($words as &$word) {
            $percent = 0;
            similar_text(strtolower($keyword), strtolower($word->word), $percent);
            $word->similar_percent = $percent;
        }
        usort($words, function ($l_word, $r_word) {
            if ($l_word->similar_percent == $r_word->similar_percent) {
                return 0;
            }
            // 내림차순 정렬
            return ($l_word->similar_percent < $r_word->simliar_percent) ? 1 : -1;
        });

        return view('search', ['keyword' => $keyword, 'words' => $words]);
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

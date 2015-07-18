<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

final class Words extends Model
{
    public static function searchWords($query) {
        // TODO: Eloquent ORM으로 변경해야함.
        $words = DB::table('words')->whereIn('id', function($q) use ($query) {
            $q->select('word_id')
                ->from('words_tags')
                ->where('tag', 'LIKE', '%' . $query . '%')
                ->groupBy('word_id');
        })->orWhere('word', 'LIKE', '%' . $query . '%')->get();

        // 검색 키워드와의 유사성에 따른 정렬
        foreach ($words as &$word) {
            $percent = 0;
            similar_text(strtolower($query), strtolower($word->word), $percent);
            $word->similar_percent = $percent;
        }
        usort($words, function ($l_word, $r_word) {
            if ($l_word->similar_percent == $r_word->similar_percent) {
                return 0;
            }
            // 내림차순 정렬
            return ($l_word->similar_percent < $r_word->simliar_percent) ? 1 : -1;
        });
        return $words;
    }
}

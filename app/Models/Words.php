<?php

namespace App\Models;

use App\Util\Markdown\MarkdownWords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

final class Words extends Model
{
    /**
     * @param $query
     * @return Words
     */
    public static function getByWord($query)
    {
        return Words::query()->where('word', '=', $query)->first();
    }

    /**
     * @param $query
     * @return Words[]
     */
    public static function searchWords($query)
    {
        // TODO: Cache 적용 (CacheKey = query);
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

    private function getMarkdownContent() {
        return file_get_contents('http://words.techetok.kr/' . $this->file_name);
    }

    /**
     * @return MarkdownWords[]
     */
    public function getMarkdownObjects() {
        $markdown = self::getMarkdownContent();
        return new MarkdownWords($markdown);
    }

    public function getPublishedHTMLs($separate_by_usage = false, &$usages = null) {
        $markdown = self::getMarkdownContent();

        // Github markdown API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,            'https://api.github.com/markdown/raw');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST,           true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $markdown);
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
                'Content-Type: text/plain',
                'Content-Length: ' . strlen($markdown),
                'User-Agent: TechEtoK'
            )
        );
        $html = curl_exec($ch);
        curl_close($ch);
        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');

        if ($separate_by_usage === false) {
            return $html;
        }

        // 여기부터는 separate_by_usage가 true이므로, 사용처가 한 개일지라도 array로 반환한다.

        $usage_count = preg_match_all('/' . MarkdownWords::HEAD_USAGE . '<\/h3>\s\s<p>(.*)<\/p>\s\s<h3>/', $html, $usages);
        if ($usage_count <= 1) {
            return array($html);
        }
        $usages = $usages[1];

        preg_match_all('/<h1>(.*)<\/a>(.*)<\/h1>/s', $html, $title);
        $title = $title[0][0];

        $html = substr($html, strlen($title), strlen($html) - strlen($title));
        $sub_htmls = explode('<hr>', $html);

        foreach ($sub_htmls as &$sub_html) {
            // 사용처에 따라 나눈 HTML 소스 앞에다가 title을 붙여준다. (Nav 형태로 사용하기 위함.)
            $sub_html = $title . $sub_html;
        }
        return $sub_htmls;
    }
}

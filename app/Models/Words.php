<?php

namespace App\Models;

use App\Util\Markdown\MarkdownUtil;
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
        return static::query()->where('word', '=', $query)->first();
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
            return ($l_word->similar_percent < $r_word->similar_percent) ? 1 : -1;
        });
        return $words;
    }

    private function getMarkdownContent() {
        return file_get_contents(MarkdownWords::MARKDOWN_DIR . $this->file_name);
    }

    /**
     * @return MarkdownWords[]
     */
    public function getMarkdownObjects() {
        $markdown = self::getMarkdownContent();
        return MarkdownWords::importMarkdown($markdown);
    }

    public function getPublishedHTMLs($markdown_by, $separate_by_usage = false, &$usages = null) {
        $markdown = self::getMarkdownContent();

        $html = MarkdownUtil::getHTML($markdown, $markdown_by);

        if ($separate_by_usage === false) {
            return $html;
        }

        // 여기부터는 separate_by_usage가 true이므로, 사용처가 한 개일지라도 array로 반환한다.

        $usage_count = preg_match_all(MarkdownUtil::getUsagesRegEx($markdown_by), $html, $usages);
        if ($usage_count <= 1) {
            return array($html);
        }
        $usages = $usages[1];

        preg_match_all(MarkdownUtil::getTitleRegEx($markdown_by), $html, $title);
        $title = $title[0][0];

        $html = substr($html, strlen($title), strlen($html) - strlen($title));
        $sub_htmls = explode(MarkdownUtil::getSubHTMLDelimiter($markdown_by), $html);
        foreach ($sub_htmls as &$sub_html) {
            // 사용처에 따라 나눈 HTML 소스 앞에다가 title을 붙여준다. (Nav 형태로 사용하기 위함.)
            $sub_html = $title . $sub_html;
        }
        return $sub_htmls;
    }
}

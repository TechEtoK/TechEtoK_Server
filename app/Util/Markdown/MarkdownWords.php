<?php

namespace App\Util\Markdown;

class MarkdownWords
{
    const MARKDOWN_DIR = '/var/www/techetok_words/';

    // 타이틀
    const HEAD_USAGE            = '사용처';
    const HEAD_KOR_EXPRESSION   = '한글표현';
    const HEAD_EXAMPLE          = '사용 예';
    const HEAD_RELATED_WORD     = '관련 단어';
    const HEAD_SUMMARY          = '간략 설명';
    const HEAD_RELATED_LINK     = '관련 링크';

    // 내용을 뽑기 위한 정규식
    const REGEX_TITLE           = '/^\#(.*)\s/';
    const REGEX_USAGE           = '/\#{3} ' . self::HEAD_USAGE  . '(.*)\#{3} '. self::HEAD_KOR_EXPRESSION . '/s';
    const REGEX_KOR_EXPRESSION  = '/\#{3} ' . self::HEAD_KOR_EXPRESSION  . '(.*)\#{3} '. self::HEAD_EXAMPLE . '/s';
    const REGEX_EXAMPLE         = '/\#{3} ' . self::HEAD_EXAMPLE  . '(.*)\#{3} '. self::HEAD_RELATED_WORD . '/s';
    const REGEX_RELATED_WORD    = '/\#{3} ' . self::HEAD_RELATED_WORD  . '(.*)\#{3} '. self::HEAD_SUMMARY . '/s';
    const REGEX_SUMMARY         = '/\#{3} ' . self::HEAD_SUMMARY  . '(.*)\#{3} '. self::HEAD_RELATED_LINK . '/s';
    const REGEX_RELATED_LINK    = '/\#{3} ' . self::HEAD_RELATED_LINK  . '(.*)/s';

    // Github Markdown
    const MARK_LIST = '*';
    const MARK_LINK_LIST_START = '* [';
    const MARK_LINK_LIST_END = ']';
    const MARK_LINK_START = '[';
    const MARK_LINK_END = ']:';
    const MARK_BLOCKQUOTE = '>';
    const MARK_SEPARATOR = '---';

    // Github Markdown 정규식
    const MARK_REGEX_LINKED_LIST_TEXT = '/\* \[(.*)\]/';
    const MARK_REGEX_LINKED_LIST_LINK = '/\[(.*)]:(.*)/';

    public $title;
    public $usages = array();
    public $kor_expressions = array();
    public $examples = array();
    public $related_words = array();
    public $summaries = array();
    public $related_links = array();

    /**
     * @param $values
     * @return MarkdownWords
     */
    public static function importValues($values)
    {
        if (empty($values)) {
            return false;
        }

        $word = new MarkdownWords();

        // 타이틀
        $title = trim($values['title']);
        if (empty($title)) {
            return false;
        }
        $word->title = $title;

        for ($i = 0; $i < count($values['usages']); $i++) {
            // 사용처
            $usage = trim($values['usages'][$i]);
            if (empty($usage)) {
                return false;
            }
            $word->usages[] = $usage;

            // 한글표현
            $kor_expression = trim($values['kor_expressions'][$i]);
            if (empty($kor_expression)) {
                return false;
            }
            $word->kor_expressions[] = $kor_expression;

            // 사용 예
            foreach ($values['examples'][$i] as $example) {
                $word->examples[$i][] = trim($example);
            }

            // 관련 단어
            for ($j = 0; $j < count($values['related_words_words'][$i]); $j++) {
                $related_words_word = trim($values['related_words_words'][$i][$j]);
                $related_words_link = trim($values['related_words_links'][$i][$j]);
                if (empty($related_words_link)) {
                    $related_words_link = null;
                }
                $word->related_words[$i][] = new RelatedWords($related_words_word, $related_words_link);
            }

            // 간략 설명
            $word->summaries[] =trim($values['summaries'][$i]);

            // 관련 링크
            foreach ($values['related_links'][$i] as $related_link) {
                $word->related_links[$i][] = trim($related_link);
            }
        }

        return $word;
    }

    /**
     * Markdown 텍스트를 받아서 MarkdownWords 객체에 넣어준다.
     * @param $markdown
     * @return MarkdownWords
     */
    public static function importMarkdown($markdown)
    {
        $word = new MarkdownWords();
        // 타이틀
        preg_match_all(self::REGEX_TITLE, $markdown, $title);
        $word->title = trim($title[1][0]);
        $markdown = substr($markdown, strlen($title[0][0]), strlen($markdown) - strlen($title[0][0]));

        $sub_markdowns = explode(self::MARK_SEPARATOR, $markdown);
        for ($i = 0; $i < count($sub_markdowns); $i++) {
            $sub_markdown = $sub_markdowns[$i];

            // 사용처
            preg_match_all(self::REGEX_USAGE, $sub_markdown, $usage);
            $word->usages[$i] = trim($usage[1][0]);

            // 한글표현
            preg_match_all(self::REGEX_KOR_EXPRESSION, $sub_markdown, $kor_expression);
            $word->kor_expressions[$i] = trim($kor_expression[1][0]);

            // 사용 예
            preg_match_all(self::REGEX_EXAMPLE, $sub_markdown, $examples);
            $examples = $examples[1][0];
            $examples = explode(self::MARK_BLOCKQUOTE, $examples);
            foreach ($examples as $example) {
                $example = trim($example);
                if (!empty($example)) {
                    $word->examples[$i][] = $example;
                }
            }

            // 관련 단어
            preg_match_all(self::REGEX_RELATED_WORD, $sub_markdown, $related_words);
            $related_words = $related_words[1][0];
            preg_match_all(self::MARK_REGEX_LINKED_LIST_TEXT, $related_words, $related_words_words);
            $related_words_words = $related_words_words[1];
            preg_match_all(self::MARK_REGEX_LINKED_LIST_LINK, $related_words, $related_words_links);
            $related_words_links['text'] = $related_words_links[1];
            $related_words_links['link'] = $related_words_links[2];
            foreach ($related_words_words as $related_words_word) {
                $related_words_word = trim($related_words_word);
                $related_words_link = null;

                $index = array_search($related_words_word, $related_words_links['text']);
                if ($index !== false) {
                    $related_words_link = trim($related_words_links['link'][$index]);
                }
                $word->related_words[$i][] = new RelatedWords($related_words_word, $related_words_link);
            }

            // 간략 설명
            preg_match_all(self::REGEX_SUMMARY, $sub_markdown, $summary);
            $word->summaries[$i] = trim($summary[1][0]);

            // 관련 링크
            preg_match_all(self::REGEX_RELATED_LINK, $sub_markdown, $related_links);
            $related_links = $related_links[1][0];
            $related_links = explode(self::MARK_LIST, $related_links);
            foreach ($related_links as $related_link) {
                $related_link = trim($related_link);
                if (!empty($related_link)) {
                    $word->related_links[$i][] = $related_link;
                }
            }
        }

        return $word;
    }

    public function exportMarkdown()
    {
        // 타이틀
        $markdown = '#' . $this->title . "\n";

        for ($i = 0; $i < count($this->usages); $i++) {
            // 사용처
            $markdown .= "\n" . '### ' . self::HEAD_USAGE . "\n";
            $markdown .= $this->usages[$i] . "\n";

            // 한글표현
            $markdown .= "\n" . '### ' . self::HEAD_KOR_EXPRESSION . "\n";
            $markdown .= $this->kor_expressions[$i] . "\n";

            // 사용 예
            $markdown .= "\n" . '### ' . self::HEAD_EXAMPLE . "\n";
            foreach ($this->examples[$i] as $example) {
                $markdown .= self::MARK_BLOCKQUOTE . ' ' . $example . "\n\n";
            }

            // 관련 단어
            $markdown .= "\n" . '### ' . self::HEAD_RELATED_WORD . "\n";
            foreach ($this->related_words[$i] as $related_word) {
                $markdown .= self::MARK_LINK_LIST_START . $related_word->word . self::MARK_LINK_LIST_END . "\n";
                if ($related_word->link !== null) {
                    $markdown .= self::MARK_LINK_START . $related_word->word . self::MARK_LINK_END .  $related_word->link . "\n";
                }
            }

            // 간략 설명
            $markdown .= "\n" . '### ' . self::HEAD_SUMMARY . "\n";
            $markdown .= $this->summaries[$i] . "\n";

            // 관련 링크
            $markdown .= "\n" . '### ' . self::HEAD_RELATED_LINK . "\n";
            foreach ($this->related_links[$i] as $related_link) {
                $markdown .= self::MARK_LIST . ' ' . $related_link . "\n";
            }

            if ($i != count($this->usages) - 1) {
                $markdown .= "\n" . self::MARK_SEPARATOR . "\n";
            }
        }

        return $markdown;
    }
}

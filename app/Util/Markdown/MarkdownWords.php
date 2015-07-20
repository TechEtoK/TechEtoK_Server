<?php

namespace App\Util\Markdown;

class MarkdownWords
{
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

    // Markdown 텍스트를 받아서 MarkdownWords 객체로 만들어준다.
    public function __construct($markdown)
    {
        // 타이틀
        preg_match_all(self::REGEX_TITLE, $markdown, $title);
        $this->title = trim($title[1][0]);
        $markdown = substr($markdown, strlen($title[0][0]), strlen($markdown) - strlen($title[0][0]));

        $sub_markdowns = explode(self::MARK_SEPARATOR, $markdown);
        for ($i = 0; $i < count($sub_markdowns); $i++) {
            $sub_markdown = $sub_markdowns[$i];

            // 사용처
            preg_match_all(self::REGEX_USAGE, $sub_markdown, $usage);
            $this->usages[$i] = trim($usage[1][0]);

            // 한글표현
            preg_match_all(self::REGEX_KOR_EXPRESSION, $sub_markdown, $kor_expression);
            $this->kor_expressions[$i] = trim($kor_expression[1][0]);

            // 사용 예
            preg_match_all(self::REGEX_EXAMPLE, $sub_markdown, $examples);
            $examples = $examples[1][0];
            $examples = explode(self::MARK_BLOCKQUOTE, $examples);
            foreach ($examples as $example) {
                $example = trim($example);
                if (!empty($example)) {
                    $this->examples[$i][] = $example;
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
                $word = trim($related_words_word);
                $link = null;

                $index = array_search($word, $related_words_links['text']);
                if ($index !== false) {
                    $link = trim($related_words_links['link'][$index]);
                }
                $this->related_words[$i][] = new RelatedWords($word, $link);
            }

            // 간략 설명
            preg_match_all(self::REGEX_SUMMARY, $sub_markdown, $summary);
            $this->summaries[$i] = trim($summary[1][0]);

            // 관련 링크
            preg_match_all(self::REGEX_RELATED_LINK, $sub_markdown, $related_links);
            $related_links = $related_links[1][0];
            $related_links = explode(self::MARK_LIST, $related_links);
            foreach ($related_links as $related_link) {
                $related_link = trim($related_link);
                if (!empty($related_link)) {
                    $this->related_links[$i][] = $related_link;
                }
            }
        }
    }
}

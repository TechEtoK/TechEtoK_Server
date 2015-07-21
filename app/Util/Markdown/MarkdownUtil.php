<?php

namespace App\Util\Markdown;

use Parsedown;

class MarkdownUtil
{
    const MARKDOWN_BY_GITHUB = 0;
    const MARKDOWN_BY_PARSE_DOWN = 1;

    public static function getHTML($markdown, $markdown_by = self::MARKDOWN_BY_GITHUB) {
        if ($markdown_by == self::MARKDOWN_BY_GITHUB) {
            return static::getHTMLFromGithub($markdown);
        } else if ($markdown_by == self::MARKDOWN_BY_PARSE_DOWN) {
            return static::getHTMLFromParseDown($markdown);
        } else {
            throw new \Exception('알 수 없는 markdown_by 값입니다. (' . $markdown_by . ')');
        }
    }

    private static function getHTMLFromGithub($markdown) {
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
        return html_entity_decode($html, ENT_QUOTES, 'UTF-8');
    }

    private static function getHTMLFromParseDown($markdown) {
        return (new Parsedown())->text($markdown);
    }

    public static function getUsagesRegEx($markdown_by) {
        if ($markdown_by == self::MARKDOWN_BY_GITHUB) {
            return '/' . MarkdownWords::HEAD_USAGE . '<\/h3>\s\s<p>(.*)<\/p>\s\s<h3>/';
        } else if ($markdown_by == self::MARKDOWN_BY_PARSE_DOWN) {
            return '/<h3>' . MarkdownWords::HEAD_USAGE . '<\/h3>\s<p>(.*)<\/p>\s<h3>/';
        } else {
            throw new \Exception('알 수 없는 markdown_by 값입니다. (' . $markdown_by . ')');
        }
    }

    public static function getTitleRegEx($markdown_by) {
        if ($markdown_by == self::MARKDOWN_BY_GITHUB) {
            return '/<h1>(.*)<\/a>(.*)<\/h1>/s';
        } else if ($markdown_by == self::MARKDOWN_BY_PARSE_DOWN) {
            return '/<h1>(.*)<\/h1>/';
        } else {
            throw new \Exception('알 수 없는 markdown_by 값입니다. (' . $markdown_by . ')');
        }
    }

    public static function getSubHTMLDelimiter($markdown_by) {
        if ($markdown_by == self::MARKDOWN_BY_GITHUB) {
            return '<hr>';
        } else if ($markdown_by == self::MARKDOWN_BY_PARSE_DOWN) {
            return '<hr />';
        } else {
            throw new \Exception('알 수 없는 markdown_by 값입니다. (' . $markdown_by . ')');
        }
    }
}

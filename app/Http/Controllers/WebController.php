<?php

namespace App\Http\Controllers;

use App\Models\Words;
use App\Models\WordsTags;
use App\Util\Markdown\MarkdownUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function detail($query)
    {
        $query = urldecode($query);
        $word = Words::getByWord($query);
        if ($word === null) {
            return view('not_found', ['query' => $query]);
        }

        $published_htmls = $word->getPublishedHTMLs(MarkdownUtil::MARKDOWN_BY_PARSE_DOWN, true, $usages);
        return view('detail', ['word' => $word->word, 'usages' => $usages, 'published_htmls' => $published_htmls]);
    }

    public function update(Request $request)
    {
        $query = $request->input('word', null);
        if (!empty($query)) {
            $query = urldecode($query);
            $word = Words::getByWord($query);
            if ($word === null) {
                return response('Wrong parameters', Response::HTTP_BAD_REQUEST);
            }
            $markdown_word = $word->getMarkdownObjects();
            $word_tags = WordsTags::getByWord($word->id);
        } else {
            $markdown_word = null;
            $word_tags = null;
        }

        return view('update', ['word' => $markdown_word, 'tags' => $word_tags]);
    }
}

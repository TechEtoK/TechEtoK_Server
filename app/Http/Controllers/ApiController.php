<?php

namespace App\Http\Controllers;

use App\Models\Words;
use App\Util\Markdown\MarkdownWords;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;

class ApiController extends BaseController
{
    public function search(Request $request)
    {
        $query = $request->input('q', null);
        if ($query === null) {
            return response('Wrong parameters', Response::HTTP_BAD_REQUEST);
        }

        $words = Words::searchWords($query);
        return response()->json(['words' => $words]);
    }

    public function addWord(Request $request)
    {
        $values = $request->all();

        $word = MarkdownWords::importValues($values);
        if ($word === false) {
            return response('Wrong parameters', Response::HTTP_BAD_REQUEST);
        }
        $markdown = $word->exportMarkdown();

        // TODO: Github API
        /*
         * 1. feature-branch
         * 2. change-branch
         * 3. 단어 파일 생성
         * 4. commit (단어 추가)
         * 5. pull-request
         *
         * 6. 작성자 정보 추가 (name, email, commit id)
         */

        return response('', Response::HTTP_OK);
    }

    public function editWord(Request $request)
    {
        $values = $request->all();

        $word = MarkdownWords::importValues($values);
        if ($word === false) {
            return response('Wrong parameters', Response::HTTP_BAD_REQUEST);
        }
        $markdown = $word->exportMarkdown();

        // TODO: Github API
        /*
         * 1. feature-branch
         * 2. change-branch
         * 3. 단어 파일 백업 (.bak postfix)
         * 4. 단어 파일 생성
         * 5. commit (단어 수정)
         * 6. pull-request
         *
         * 7. 작성자 정보 추가 (name, email, commit id)
         */

        return response('', Response::HTTP_OK);
    }
}

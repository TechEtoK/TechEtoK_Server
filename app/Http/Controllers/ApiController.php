<?php

namespace App\Http\Controllers;

use App\Models\Words;
use App\Models\WordsUpdateLocks;
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
        if (WordsUpdateLocks::isLocked()) {
            return response('이미 작업중입니다.',  Response::HTTP_SERVICE_UNAVAILABLE);
        } else {
            WordsUpdateLocks::setLock(true);
        }

        $values = $request->all();

        $word = MarkdownWords::importValues($values);
        if ($word === false) {
            WordsUpdateLocks::setLock(false);
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
         * 7. 키워드 추가
         */

        $file_name = strtolower($word->title);
        $file_name = preg_replace('/\s+/', '_', $file_name);
        $file_name .= '.md';

        try {
            $md_file = fopen(MarkdownWords::MARKDOWN_DIR . $file_name, 'w');
            if ($md_file === false) {
                fclose($md_file);
                throw new \Exception('Markdown 파일 생성 실패');
            }

            if (fwrite($md_file, $markdown) === false) {
                fclose($md_file);
                throw new \Exception('Markdown 파일 쓰기 실패');
            }
            fclose($md_file);
        } catch (\Exception $e) {
            WordsUpdateLocks::setLock(false);
            return response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        WordsUpdateLocks::setLock(false);
        return response('', Response::HTTP_OK);
    }

    public function editWord(Request $request)
    {
        if (WordsUpdateLocks::isLocked()) {
            return response('이미 작업중입니다.',  Response::HTTP_SERVICE_UNAVAILABLE);
        } else {
            WordsUpdateLocks::setLock(true);
        }

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
         * 8. 키워드 추가
         */

        WordsUpdateLocks::setLock(false);

        return response('', Response::HTTP_OK);
    }
}

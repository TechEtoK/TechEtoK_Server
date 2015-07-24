<?php

namespace App\Http\Controllers;

use App\Models\Words;
use App\Models\WordsTags;
use App\Models\WordsUpdateLocks;
use App\Util\Markdown\MarkdownWords;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller as BaseController;

class ApiController extends BaseController
{
    public function search(Request $request)
    {
        $query = $request->input('q', null);
        if ($query === null) {
            return response('잘못된 파라미터입니다.', Response::HTTP_BAD_REQUEST);
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

        $markdown_word = MarkdownWords::importValues($values);
        if ($markdown_word === false) {
            WordsUpdateLocks::setLock(false);
            return response('잘못된 파라미터입니다.', Response::HTTP_BAD_REQUEST);
        }
        $markdown = $markdown_word->exportMarkdown();

        // 마크다운 파일 경로
        $md_file_name = MarkdownWords::makeMarkdownFileName($markdown_word->title);
        $md_file_path = MarkdownWords::MARKDOWN_DIR . $md_file_name;

        $db_transaction_began = false;

        try {
            // TODO: 1. Git 브랜치 생성 + 체크아웃

            // 2. 단어 파일 생성
            if (file_exists($md_file_path)) {
                throw new Exception('이미 존재하는 파일입니다.');
            }

            $md_file = fopen($md_file_path, 'w');
            if ($md_file === false) {
                fclose($md_file);
                throw new Exception('Markdown 파일 생성 실패');
            }
            if (fwrite($md_file, $markdown) === false) {
                fclose($md_file);
                throw new Exception('Markdown 파일 쓰기 실패');
            }
            fclose($md_file);

            // TODO: 3. Git 커밋

            // 4. 단어 DB 관련 작업
            DB::beginTransaction();
            $db_transaction_began = true;

            // 단어 DB 추가
            $word = Words::create(['word' => $markdown_word->title, 'file_name' => $md_file_name]);
            if ($word === null) {
                throw new Exception('단어를 생성하는 도중 오류가 발생하였습니다.');
            }

            // 단어 태그 추가
            foreach ($values['tags'] as $tag) {
                $tag = WordsTags::create(['word_id' => $word->id, 'tag' => $tag]);
                if ($tag === null) {
                    throw new Exception('단어 태그를 생성하는 도중 오류가 발생하였습니다.');
                }
            }

            // TODO: 작성자 추가

            // TODO: 5. Git Push & Pull-Request

            DB::commit();
        } catch (Exception $e) {
            if ($db_transaction_began) {
                DB::rollback();
            }

            // TODO: Git strip(Reset)

            WordsUpdateLocks::setLock(false);
            return response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        WordsUpdateLocks::setLock(false);
        return response('단어를 추가했습니다.', Response::HTTP_OK);
    }

    public function editWord(Request $request)
    {
        if (WordsUpdateLocks::isLocked()) {
            return response('이미 작업중입니다.',  Response::HTTP_SERVICE_UNAVAILABLE);
        } else {
            WordsUpdateLocks::setLock(true);
        }

        $values = $request->all();

        $markdown_word = MarkdownWords::importValues($values);
        if ($markdown_word === false) {
            WordsUpdateLocks::setLock(false);
            return response('잘못된 파라미터입니다.', Response::HTTP_BAD_REQUEST);
        }
        $markdown = $markdown_word->exportMarkdown();

        $word = Words::getByWord($markdown_word->title);
        if ($word === null) {
            return response('단어를 찾을 수 없습니다.', Response::HTTP_NOT_FOUND);
        }

        // 마크다운 파일 경로
        $md_file_name = MarkdownWords::makeMarkdownFileName($markdown_word->title);
        $md_file_path = MarkdownWords::MARKDOWN_DIR . $md_file_name;

        $db_transaction_began = false;

        try {
            // TODO: 1. Git 브랜치 생성 + 체크아웃

            // 2. 원본 파일 삭제
            if (!unlink($md_file_path)) {
                throw new Exception('원본 파일을 삭제하지 못했습니다.');
            }

            // 3. 단어 파일 생성
            $md_file = fopen($md_file_path, 'w');
            if ($md_file === false) {
                fclose($md_file);
                throw new Exception('Markdown 파일 생성 실패');
            }
            if (fwrite($md_file, $markdown) === false) {
                fclose($md_file);
                throw new Exception('Markdown 파일 쓰기 실패');
            }
            fclose($md_file);

            // TODO: 4. Git 커밋

            // 5. 단어 DB 관련 작업
            DB::beginTransaction();
            $db_transaction_began = true;

            // 태그 삭제 후 다시 추가
            if (!WordsTags::deleteByWord($word->id)) {
                throw new Exception('기존 태그를 삭제하는 도중 오류가 발생하였습니다.');
            }
            foreach ($values['tags'] as $tag) {
                $tag = WordsTags::create(['word_id' => $word->id, 'tag' => $tag]);
                if ($tag === null) {
                    throw new Exception('단어 태그를 생성하는 도중 오류가 발생하였습니다.');
                }
            }

            // TODO: 작성자 추가

            // TODO: 6. Git Push & Pull-Request

            DB::commit();
        } catch (Exception $e) {
            if ($db_transaction_began) {
                DB::rollback();
            }

            // TODO: Git strip(Reset)

            WordsUpdateLocks::setLock(false);
            return response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        WordsUpdateLocks::setLock(false);
        return response('단어를 수정했습니다.', Response::HTTP_OK);
    }
}

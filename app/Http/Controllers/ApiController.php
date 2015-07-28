<?php

namespace App\Http\Controllers;

use App\Models\Words;
use App\Models\WordsAuthors;
use App\Models\WordsTags;
use App\Models\WordsUpdateLocks;
use App\Util\Github\GithubUtil;
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

        // Git 경로
        $git_path = MarkdownWords::MARKDOWN_DIR;
        $git_branch_name = GithubUtil::makeBranchName($markdown_word->title, GithubUtil::BRANCH_TYPE_ADDED);

        $git_checkoutted = $db_transaction_began = $git_pushed = false;
        try {
            // 1. 단어 파일 생성
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

            // 2. Git Checkout
            if (!GithubUtil::checkout($git_path, $git_branch_name)) {
                throw new Exception('Git Checkout 실패');
            }
            $git_checkoutted = true;

            // 3. Git Commit
            if (!GithubUtil::addCommit($git_path, GithubUtil::BRANCH_TYPE_ADDED, $markdown_word->title)) {
                throw new Exception('Git Add & Commit 실패');
            }
            $commit_id = GithubUtil::getLastShortCommitID($git_path);

            // 4. 단어 DB 관련 작업
            DB::beginTransaction();
            $db_transaction_began = true;

            // 단어 DB 추가
            $word = Words::create([
                'word' => $markdown_word->title,
                'file_name' => $md_file_name
            ]);

            // 단어 태그 추가
            foreach ($values['tags'] as $tag) {
                if (empty($tag)) {
                    continue;
                }

                WordsTags::create([
                    'word_id' => $word->id,
                    'tag' => $tag
                ]);
            }

            // 작성자 추가
            $author_name = empty($values['author_name']) ? '' : trim($values['author_name']);
            $author_email = empty($values['author_email']) ? '' : trim($values['author_email']);
            WordsAuthors::create([
                'word_id' => $word->id,
                'name' => $author_name,
                'email' => $author_email,
                'commit' => $commit_id
            ]);

            // 5. Git Push
            if (!GithubUtil::push($git_path, $git_branch_name)) {
                throw new Exception('Git Push 실패');
            }
            $git_pushed = true;

            // 6. Git Pull-Request
            if (!GithubUtil::pullRequest($git_path, GithubUtil::BRANCH_TYPE_ADDED, $word->word)) {
                throw new Exception('Git Pull-request 실패');
            }

            // 7. Git Checkout to master
            if (!GithubUtil::checkoutToMaster($git_path)) {
                throw new Exception('Git Checkout master 실패');
            }

            DB::commit();
        } catch (Exception $e) {
            if ($db_transaction_began) {
                DB::rollback();
            }

            if ($git_pushed) {
                static::rollbackGithubWhileSuccess($git_path, $git_branch_name, true);
            } else if ($git_checkoutted) {
                static::rollbackGithubWhileSuccess($git_path, $git_branch_name, false);
            } else {
                GithubUtil::undoChanges($git_path);
            }

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

        // Git 경로
        $git_path = MarkdownWords::MARKDOWN_DIR;
        $git_branch_name = GithubUtil::makeBranchName($markdown_word->title, GithubUtil::BRANCH_TYPE_MODIFIED);

        $git_checkoutted = $db_transaction_began = $git_pushed = false;
        try {
            // 1. 원본 파일 삭제
            if (!unlink($md_file_path)) {
                throw new Exception('원본 파일을 삭제하지 못했습니다.');
            }

            // 2. 단어 파일 생성
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

            // 3. Git Checkout
            if (!GithubUtil::checkout($git_path, $git_branch_name)) {
                throw new Exception('Git Checkout 실패');
            }
            $git_checkoutted = true;

            // 4. Git Commit
            if (!GithubUtil::addCommit($git_path, GithubUtil::BRANCH_TYPE_MODIFIED, $markdown_word->title)) {
                throw new Exception('Git Add & Commit 실패');
            }
            $commit_id = GithubUtil::getLastShortCommitID($git_path);

            // 5. 단어 DB 관련 작업
            DB::beginTransaction();
            $db_transaction_began = true;

            // 태그 삭제 후 다시 추가
            WordsTags::deleteByWord($word->id);
            // 단어 태그 추가
            foreach ($values['tags'] as $tag) {
                if (empty($tag)) {
                    continue;
                }

                WordsTags::create([
                    'word_id' => $word->id,
                    'tag' => $tag
                ]);
            }

            // 작성자 추가
            $author_name = empty($values['author_name']) ? '' : trim($values['author_name']);
            $author_email = empty($values['author_email']) ? '' : trim($values['author_email']);
            WordsAuthors::create([
                'word_id' => $word->id,
                'name' => $author_name,
                'email' => $author_email,
                'commit' => $commit_id
            ]);

            // 6. Git Push
            if (!GithubUtil::push($git_path, $git_branch_name)) {
                throw new Exception('Git Push 실패');
            }
            $git_pushed = true;

            // 7. Git Pull-Request
            if (!GithubUtil::pullRequest($git_path, GithubUtil::BRANCH_TYPE_MODIFIED, $word->word)) {
                throw new Exception('Git Pull-request 실패');
            }

            // 8. Git Checkout to master
            if (!GithubUtil::checkoutToMaster($git_path)) {
                throw new Exception('Git Checkout master 실패');
            }

            DB::commit();
        } catch (Exception $e) {
            if ($db_transaction_began) {
                DB::rollback();
            }

            if ($git_pushed) {
                static::rollbackGithubWhileSuccess($git_path, $git_branch_name, true);
            } else if ($git_checkoutted) {
                static::rollbackGithubWhileSuccess($git_path, $git_branch_name, false);
            } else {
                GithubUtil::undoChanges($git_path);
            }

            WordsUpdateLocks::setLock(false);
            return response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        WordsUpdateLocks::setLock(false);
        return response('단어를 수정했습니다.', Response::HTTP_OK);
    }

    // Github 관련 롤백이 성공할 때까지 롤백을 시도한다.
    private static function rollbackGithubWhileSuccess($git_path, $git_branch_name, $delete_remote_branch = false)
    {
        static $max_try_to_rollback = 10;   // 최대 롤백 시도 횟수

        $checkout_master_result = $delete_remote_result = $delete_local_result = false;
        for ($i = 0; $i < $max_try_to_rollback; $i++) {
            // master 브랜치로 체크아웃
            if ($checkout_master_result === false) {
                $checkout_master_result = GithubUtil::checkoutToMaster($git_path);
                if ($checkout_master_result === false) {
                    continue;
                }
            }

            // 원격 branch 삭제
            if ($delete_remote_branch && $delete_remote_result === false) {
                $delete_remote_result = GithubUtil::deleteRemoteBranch($git_path, $git_branch_name);
                if ($delete_remote_result === false) {
                    continue;
                }
            }

            // 로컬 branch 삭제
            if ($delete_local_result === false) {
                $delete_local_result = GithubUtil::deleteLocalBranch($git_path, $git_branch_name);
                if ($delete_local_result === false) {
                    continue;
                }
            }

            // 모두 성공하면 return
            if ($checkout_master_result
                && (!$delete_remote_branch || $delete_remote_result)
                && $delete_local_result) {
                return;
            }
        }

        // 실패시, Sentry에 에러를 보낸다.
        // TODO: Sentry
    }
}

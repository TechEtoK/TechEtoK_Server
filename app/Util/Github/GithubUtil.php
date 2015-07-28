<?php

namespace App\Util\Github;

class GithubUtil
{
    const BRANCH_TYPE_ADDED = 'add';
    const BRANCH_TYPE_MODIFIED = 'modify';

    public static function makeBranchName($word, $branch_type)
    {
        $now_date = date('YmdHis');
        return $branch_type . '_' . $word . '_' . $now_date;
    }

    public static function checkout($git_path, $branch_name)
    {
        $last_line = exec('git -C ' . $git_path . ' checkout -b ' . $branch_name . ' 2>&1');
        return trim($last_line) =='Switched to a new branch \'' . $branch_name . '\'';
    }

    public static function addCommit($git_path, $branch_type, $word)
    {
        exec('git -C ' . $git_path . ' add .');
        exec('git -C ' . $git_path . ' commit -m "' . $branch_type . ' ' . $word . '"', $output);
        return static::startsWith(trim($output[1]), "1 file changed");
    }

    public static function getLastShortCommitID($git_path)
    {
        return exec('git -C ' . $git_path . ' rev-parse --short HEAD');
    }

    public static function push($git_path, $branch_name)
    {
        $last_line = exec('git -C ' . $git_path . ' push origin ' . $branch_name . ' 2>&1');
        return trim($last_line) == '* [new branch]      ' . $branch_name . ' -> ' . $branch_name;
    }

    public static function pullRequest($git_path, $branch_type, $word)
    {
        $last_line = exec('sudo ' . __DIR__ . '/pull-request.sh ' . $git_path . ' "' . $branch_type . ' ' . $word . '"');
        return starts_with(trim($last_line), 'https://github.com/TechEtoK/TechEtoK_Words/pull/');
    }

    public static function checkoutToMaster($git_path)
    {
        exec('git -C ' . $git_path . ' checkout -f master 2>&1', $output);
        return trim($output[0]) == 'Switched to branch \'master\'';
    }

    public static function deleteLocalBranch($git_path, $branch_name)
    {
        $last_line = exec('git -C ' . $git_path . ' branch -D ' . $branch_name);
        return static::startsWith(trim($last_line), 'Deleted branch ' . $branch_name);
    }

    public static function deleteRemoteBranch($git_path, $branch_name)
    {
        $last_line = exec('git -C ' . $git_path . ' push --delete origin ' . $branch_name . ' 2>&1');
        return trim($last_line) == '- [deleted]         ' . $branch_name;
    }

    private static function startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === '' || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
}

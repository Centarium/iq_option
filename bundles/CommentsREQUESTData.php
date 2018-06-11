<?php
namespace Bundles;

use Interfaces\CommentsData;

/**
 * Получение данных для сообщений из http REQUEST. Необходима защита от Xss, SQL - injection
 * Class CommentsPOSTData
 * @package Bundles
 */
class CommentsREQUESTData implements CommentsData
{
    public function getLevel()
    {
        $level = intval($_POST['level']);

        return $level;
    }

    public function getRightKey(): int
    {
        $right_key = intval($_POST['right_key']);

        return $right_key;
    }

    public function getLeftKey(): int
    {
        $left_key = intval($_POST['left_key']);

        return $left_key;
    }

    public function getCommentID():int
    {
        $commentID = intval($_POST['comment_id']);

        return $commentID;
    }

    public function getComment():string
    {
        $newComment = $this->filterXss($_POST['comment']);

        return $newComment;
    }

    private function filterXss(string $value):string
    {
        $value = htmlspecialchars($value);
        return strip_tags($value);
    }
}
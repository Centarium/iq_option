<?php
namespace Bundles;

use Interfaces\CommentsData;

class CommentsFixturesData implements CommentsData
{
    protected $left_key;
    protected $right_key;
    protected $comment_id;
    protected $level;
    protected $comment;

    public function getLevel():int
    {
        return $this->level;
    }

    public function getRightKey(): int
    {
        return $this->right_key;
    }

    public function getLeftKey(): int
    {
        return $this->left_key;
    }

    public function getCommentID():int
    {
        return $this->comment_id;
    }

    public function getComment():string
    {
        return $this->comment;
    }

    /**
     * @param mixed $left_key
     */
    public function setLeftKey($left_key): void
    {
        $this->left_key = $left_key;
    }

    /**
     * @param mixed $right_key
     */
    public function setRightKey($right_key): void
    {
        $this->right_key = $right_key;
    }

    /**
     * @param mixed $comment_id
     */
    public function setCommentId($comment_id): void
    {
        $this->comment_id = $comment_id;
    }

    /**
     * @param mixed $level
     */
    public function setLevel($level): void
    {
        $this->level = $level;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment): void
    {
        $this->comment = $comment;
    }
}
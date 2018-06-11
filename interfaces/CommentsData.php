<?php
namespace Interfaces;


interface CommentsData
{
    public function getCommentID():int;
    public function getLeftKey():int;
    public function getRightKey():int;
    public function getLevel();
    public function getComment():string;
}
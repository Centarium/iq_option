<?php
namespace Interfaces;


interface CommentsData
{
    public function getCommentID():int;
    public function getLeftKey():int;
    public function getRightKey():int;
    public function getLevel():int;
    public function getComment():string;
}
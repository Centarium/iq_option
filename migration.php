<?php
include_once __DIR__.'/models/Comments.php';

use Models\Comments;

$type = $argv[1];
$model = new Comments();

if( $type == '--migrateUp' )
{
    $model->migrateUp();
}
elseif($type == '--migrateDown')
{
    $model->migrateDown();
}



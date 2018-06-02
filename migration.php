<?php
include_once __DIR__.'/vendor/autoload.php';

use Models\Comments;

$type = $argv[1];
$model = new Comments();

if( $type == '--migrateUp' )
{
    $model->migrateUp();
}
if($type == '--migrateDown')
{
    $model->migrateDown();
}
if($type == '--createData')
{
    $model->createData();
}



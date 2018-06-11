<?php
include_once __DIR__.'/vendor/autoload.php';

use Fixtures\FixtureComments;

$type = $argv[1];
$model = new FixtureComments();

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



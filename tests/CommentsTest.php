<?php
namespace Tests;

include_once __DIR__.'/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Fixtures\FixtureComments;
use Bundles\CommentsFixturesData;

/**
 * ./vendor/bin/phpunit  tests/CommentsTest
 * Class CommentsTest
 */
class CommentsTest extends TestCase
{
    /**
     * @var FixtureComments $comments
     */
    private $comments;

    private $testAddNodeLevel = 1;

    const TEST_LEVELS = 3;

    public function setUp():void/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUp();

        $this->comments = new FixtureComments();

        $this->comments->migrateDown();
        $this->comments->migrateUp();
        $this->comments->createData();
    }


    /**
     * В произвольном порядке пишем сообщения в ноды и делаем проверку после каждой вставленной ноды
     * @throws \Exception
     */
    public function testAddNode()
    {
        $commentsID = $this->comments->getCommentsID();

        $start = $commentsID[0];
        $end = $commentsID[count($commentsID)-1];

        foreach ($commentsID as $item)
        {
            $commentID = random_int($start,$end);

            $Fixture = new CommentsFixturesData();

            $data = $this->comments->getCommentByID( $commentID );

            $Fixture->setComment("New Test Comment!");
            $Fixture->setCommentId($commentID);
            $Fixture->setLeftKey($data['left_key']);
            $Fixture->setLevel($data['level']);
            $Fixture->setRightKey($data['right_key']);

            $this->comments->addNode($Fixture);

            $assertRightKeyDoubleNodes = $this->comments->checkRightKeyDubleNodes();

            $this->assertEquals(  $this->comments->checkTreeLeftLessThenRight(), false );
            $this->assertEquals(  $this->comments->checkMinLeftKey(), 1 );
            $this->assertEquals(  $this->comments->checkReminderDivisionTwo(), false );
            $this->assertEquals(  $this->comments->checkAddOdd(), false );
            $this->assertEquals(  $this->comments->chekKeysUnique(), false );
            $this->assertEquals(  $assertRightKeyDoubleNodes['max'], $assertRightKeyDoubleNodes['double_nodes'] );
        }

        if($this->testAddNodeLevel < self::TEST_LEVELS )
        {
            $this->testAddNodeLevel++;
            $this->testAddNode();
        }
    }
}
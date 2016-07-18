<?php

namespace yii2mod\comments\tests\models;

use Yii;
use yii2mod\comments\models\CommentModel;
use yii2mod\comments\models\enums\CommentStatus;
use yii2mod\comments\Module;
use yii2mod\comments\tests\TestCase;

/**
 * Class CommentModelTest
 * @package yii2mod\comments\models\tests
 */
class CommentModelTest extends TestCase
{
    public function testGetModule()
    {
        $this->assertInstanceOf(Module::className(), Yii::$app->getModule('comment'));
    }

    public function testDeleteComment()
    {
        $comment = CommentModel::findOne(1);

        $this->assertTrue($comment->deleteComment());
        $this->assertEquals(CommentStatus::DELETED, $comment->status);
        $this->assertTrue($comment->getIsDeleted());
        $this->assertFalse($comment->getIsActive());
    }

    public function testGetActiveCommentsCount()
    {
        $comment = CommentModel::findOne(1);
        $this->assertEquals(1, $comment->getCommentsCount());
    }

    public function testGetCommentsCountWithInactive()
    {
        $comment = CommentModel::findOne(1);
        $this->assertEquals(2, $comment->getCommentsCount(false));
    }
}
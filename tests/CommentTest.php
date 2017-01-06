<?php

namespace yii2mod\comments\tests;

use Yii;
use yii\base\Event;
use yii\helpers\Json;
use yii2mod\comments\models\CommentModel;
use yii2mod\comments\Module;
use yii2mod\comments\tests\data\DefaultController;
use yii2mod\comments\tests\data\PostModel;
use yii2mod\comments\tests\data\User;
use yii2mod\moderation\enums\Status;

/**
 * Class CommentTest
 *
 * @package yii2mod\comments\tests
 */
class CommentTest extends TestCase
{
    public function testAddComment()
    {
        Yii::$app->user->login(User::find()->one());
        Yii::$app->request->bodyParams = [
            'CommentModel' => [
                'content' => 'test comment',
            ],
        ];
        $response = Yii::$app->runAction('comment/default/create', ['entity' => $this->generateEntity()]);

        $this->assertEquals('success', $response['status'], 'Unable to add a comment!');
    }

    public function testBeforeCreateEvent()
    {
        $commentContent = 'test comment';
        Yii::$app->user->login(User::find()->one());
        Yii::$app->request->bodyParams = [
            'CommentModel' => [
                'content' => $commentContent,
            ],
        ];
        Event::on(
            DefaultController::class,
            DefaultController::EVENT_BEFORE_CREATE,
            function ($event) use ($commentContent) {
                $this->assertEquals(1, $event->getCommentModel()->entityId);
                $this->assertInstanceOf(CommentModel::class, $event->getCommentModel());
            }
        );
        $response = Yii::$app->runAction('comment/default/create', ['entity' => $this->generateEntity()]);

        $this->assertEquals('success', $response['status'], 'Unable to add a comment!');
    }

    public function testAfterCreateEvent()
    {
        $commentContent = 'test comment';
        Yii::$app->user->login(User::find()->one());
        Yii::$app->request->bodyParams = [
            'CommentModel' => [
                'content' => $commentContent,
            ],
        ];
        Event::on(
            DefaultController::class,
            DefaultController::EVENT_AFTER_CREATE,
            function ($event) use ($commentContent) {
                $this->assertEquals($commentContent, $event->getCommentModel()->content);
                $this->assertInstanceOf(CommentModel::class, $event->getCommentModel());
            }
        );
        $response = Yii::$app->runAction('comment/default/create', ['entity' => $this->generateEntity()]);

        $this->assertEquals('success', $response['status'], 'Unable to add a comment!');
    }

    public function testReply()
    {
        $comment = CommentModel::find()->one();
        Yii::$app->user->login(User::find()->one());
        Yii::$app->request->bodyParams = [
            'CommentModel' => [
                'parentId' => $comment->id,
                'content' => 'test comment',
            ],
        ];
        $response = Yii::$app->runAction('comment/default/create', ['entity' => $this->generateEntity()]);
        $this->assertEquals('success', $response['status'], 'Unable to add a comment!');

        $addedComment = CommentModel::find()->orderBy(['id' => SORT_DESC])->one();
        $this->assertEquals($comment->id, $addedComment->parentId, 'Invalid value for parentId attribute!');
    }

    public function testDeleteComment()
    {
        Yii::$app->user->login(User::find()->one());
        $response = Yii::$app->runAction('comment/default/delete', ['id' => 1]);

        $this->assertEquals('Comment has been deleted.', $response, 'Unable to delete a comment!');
    }

    public function testBeforeDeleteEvent()
    {
        Event::on(
            DefaultController::class,
            DefaultController::EVENT_BEFORE_DELETE,
            function ($event) {
                $this->assertEquals(Status::APPROVED, $event->getCommentModel()->status);
                $this->assertInstanceOf(CommentModel::class, $event->getCommentModel());
            }
        );

        Yii::$app->user->login(User::find()->one());
        $response = Yii::$app->runAction('comment/default/delete', ['id' => 1]);

        $this->assertEquals('Comment has been deleted.', $response, 'Unable to delete a comment!');
    }

    public function testAfterDeleteEvent()
    {
        Event::on(
            DefaultController::class,
            DefaultController::EVENT_AFTER_DELETE,
            function ($event) {
                $this->assertEquals(Status::REJECTED, $event->getCommentModel()->status);
                $this->assertInstanceOf(CommentModel::class, $event->getCommentModel());
            }
        );

        Yii::$app->user->login(User::find()->one());
        $response = Yii::$app->runAction('comment/default/delete', ['id' => 1]);

        $this->assertEquals('Comment has been deleted.', $response, 'Unable to delete a comment!');
    }

    /**
     * @expectedException \yii\web\BadRequestHttpException
     */
    public function testTryAddCommentWithInvalidEntityParam()
    {
        Yii::$app->runAction('comment/default/create', ['entity' => 'invalid entity']);
    }

    /**
     * Generate entity string
     *
     * @return string
     */
    private function generateEntity()
    {
        $post = PostModel::find()->one();

        return utf8_encode(Yii::$app->getSecurity()->encryptByKey(Json::encode([
            'entity' => hash('crc32', get_class($post)),
            'entityId' => $post->id,
            'relatedTo' => get_class($post) . ':' . $post->id,
        ]), Module::$name));
    }
}

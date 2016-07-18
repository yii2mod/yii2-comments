<?php

namespace yii2mod\comments\tests;

use Yii;
use yii\helpers\Json;
use yii2mod\comments\Module;
use yii2mod\comments\tests\data\PostModel;
use yii2mod\comments\tests\data\User;

/**
 * Class CommentTest
 * @package yii2mod\comments\tests
 */
class CommentTest extends TestCase
{
    public function testTryAddComment()
    {
        Yii::$app->user->login(User::find()->one());
        Yii::$app->request->bodyParams = [
            'CommentModel' => [
                'content' => 'my comment',
            ]
        ];
        $response = Yii::$app->runAction('comment/default/create', ['entity' => $this->generateEntity()]);

        $this->assertEquals('success', $response['status']);
    }

    public function testTryAddCommentWithInvalidEntityParam()
    {
        $response = Yii::$app->runAction('comment/default/create', ['entity' => 'invalid entity']);
        $this->assertEquals('error', $response['status']);
    }

    /**
     * @return string
     */
    private function generateEntity()
    {
        $post = PostModel::find()->one();

        return Yii::$app->getSecurity()->encryptByKey(Json::encode([
            'entity' => hash('crc32', get_class($post)),
            'entityId' => $post->id,
            'relatedTo' => get_class($post) . ':' . $post->id
        ]), Module::$name);
    }
}
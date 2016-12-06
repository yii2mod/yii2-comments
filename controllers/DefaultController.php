<?php

namespace yii2mod\comments\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii2mod\comments\events\CommentEvent;
use yii2mod\comments\models\CommentModel;
use yii2mod\comments\Module;
use yii2mod\moderation\ModerationBehavior;

/**
 * Class DefaultController
 *
 * @package yii2mod\comments\controllers
 */
class DefaultController extends Controller
{
    /**
     * Event is triggered after creating a new comment.
     * Triggered with yii2mod\comments\events\CommentEvent
     */
    const EVENT_AFTER_CREATE = 'afterCreate';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'delete' => ['post', 'delete'],
                ],
            ],
            'contentNegotiator' => [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['create'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Create comment.
     *
     * @param $entity string encrypt entity
     *
     * @return array
     */
    public function actionCreate($entity)
    {
        /* @var $commentModel CommentModel */
        $commentModel = Yii::createObject(Yii::$app->getModule(Module::$name)->commentModelClass);
        $commentModel->setAttributes($this->getCommentAttributesFromEntity($entity));
        if ($commentModel->load(Yii::$app->request->post()) && $commentModel->saveComment()) {
            $event = Yii::createObject(['class' => CommentEvent::class, 'commentModel' => $commentModel]);
            $this->trigger(self::EVENT_AFTER_CREATE, $event);

            return ['status' => 'success'];
        }

        return [
            'status' => 'error',
            'errors' => ActiveForm::validate($commentModel),
        ];
    }

    /**
     * Delete comment.
     *
     * @param int $id Comment ID
     *
     * @return string Comment text
     */
    public function actionDelete($id)
    {
        if ($this->findModel($id)->markRejected()) {
            return Yii::t('yii2mod.comments', 'Comment has been deleted.');
        } else {
            Yii::$app->response->setStatusCode(500);

            return Yii::t('yii2mod.comments', 'Comment has not been deleted. Please try again!');
        }
    }

    /**
     * Find model by ID.
     *
     * @param int|array $id Comment ID
     *
     * @throws NotFoundHttpException
     *
     * @return null|CommentModel|ModerationBehavior
     */
    protected function findModel($id)
    {
        /** @var CommentModel $model */
        $commentModelClass = Yii::$app->getModule(Module::$name)->commentModelClass;
        if (($model = $commentModelClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('yii2mod.comments', 'The requested page does not exist.'));
        }
    }

    /**
     * Get list of attributes from encrypted entity
     *
     * @param $entity string encrypted entity
     *
     * @throws BadRequestHttpException
     *
     * @return array|mixed
     */
    protected function getCommentAttributesFromEntity($entity)
    {
        $decryptEntity = Yii::$app->getSecurity()->decryptByKey(utf8_decode($entity), Module::$name);
        if ($decryptEntity !== false) {
            return Json::decode($decryptEntity);
        }

        throw new BadRequestHttpException(Yii::t('yii2mod.comments', 'Oops, something went wrong. Please try again later.'));
    }
}

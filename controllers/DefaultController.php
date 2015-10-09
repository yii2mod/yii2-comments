<?php

namespace yii2mod\comments\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii2mod\comments\models\CommentModel;
use yii2mod\comments\Module;


/**
 * Class DefaultController
 * @package yii2mod\comments\controllers
 */
class DefaultController extends Controller
{
    /**
     * Behaviors
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['post'],
                    'delete' => ['post', 'delete']
                ],
            ],
        ];
    }

    /**
     * Create comment.
     * @param $entity string ecrypt entity
     * @return array|null|Response
     */
    public function actionCreate($entity)
    {
        /* @var $module Module */
        $module = Yii::$app->getModule(Module::$name);
        $commentModelClass = $module->commentModelClass;
        $decryptEntity = Yii::$app->getSecurity()->decryptByKey($entity, $module::$name);
        if ($decryptEntity !== false) {
            $entityData = Json::decode($decryptEntity);
            /* @var $model CommentModel */
            $model = new $commentModelClass([
                'entity' => $entityData['entity'],
                'entityId' => $entityData['entityId'],
            ]);
            $load = $model->load(Yii::$app->request->post());
            if (Yii::$app->request->isAjax && $load) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($load && $model->save()) {
                Yii::$app->session->setFlash('success', 'Comment has been added.');
                return $this->redirect(Yii::$app->request->referrer);
            }
        }
        Yii::$app->session->setFlash('error', 'Oops, something went wrong. Please try again later.');
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Delete comment page.
     *
     * @param integer $id Comment ID
     * @return string Comment text
     */
    public function actionDelete($id)
    {
        if ($this->findModel($id)->deleteComment()) {
            return 'Comment was deleted.';
        } else {
            Yii::$app->response->setStatusCode(500);
            return 'Comment has not been deleted. Please try again!';
        }
    }

    /**
     * Find model by ID.
     *
     * @param integer|array $id Comment ID
     * @return null|CommentModel
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        /** @var CommentModel $model */
        $commentModelClass = Yii::$app->getModule(Module::$name)->commentModelClass;
        if (($model = $commentModelClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

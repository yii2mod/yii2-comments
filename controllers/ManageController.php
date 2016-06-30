<?php

namespace yii2mod\comments\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii2mod\comments\models\CommentModel;
use yii2mod\comments\models\CommentSearchModel;
use yii2mod\comments\Module;
use yii2mod\editable\EditableAction;

/**
 * Manage comments in admin panel
 *
 * Class ManageController
 * @package yii2mod\comments\controllers
 */
class ManageController extends Controller
{
    /**
     * @var string path to index view file, which is used in admin panel
     */
    public $indexView = '@vendor/yii2mod/yii2-comments/views/manage/index';

    /**
     * @var string path to update view file, which is used in admin panel
     */
    public $updateView = '@vendor/yii2mod/yii2-comments/views/manage/update';

    /**
     * Returns a list of behaviors that this component should behave as.
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'update' => ['get', 'post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Declares external actions for the controller.
     *
     * @return array
     */
    public function actions()
    {
        return [
            'edit-comment' => [
                'class' => EditableAction::className(),
                'modelClass' => CommentModel::className(),
                'forceCreate' => false
            ]
        ];
    }

    /**
     * Lists all comments.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CommentSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $commentModel = Yii::$app->getModule(Module::$name)->commentModelClass;

        return $this->render($this->indexView, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'commentModel' => $commentModel
        ]);
    }

    /**
     * Updates an existing CommentModel model.
     *
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('yii2mod.comments', 'Comment has been saved.'));
            return $this->redirect(['index']);
        }

        return $this->render($this->updateView, [
            'model' => $model,
        ]);

    }

    /**
     * Deletes an existing CommentModel model.
     *
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', Yii::t('yii2mod.comments', 'Comment has been deleted.'));
        return $this->redirect(['index']);
    }

    /**
     * Finds the CommentModel model based on its primary key value.
     *
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return CommentModel the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CommentModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('yii2mod.comments', 'The requested page does not exist.'));
        }
    }
}
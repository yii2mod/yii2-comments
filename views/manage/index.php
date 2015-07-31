<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\Pjax;
use yii2mod\comments\models\enums\CommentStatus;
use yii2mod\editable\EditableColumn;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel \yii2mod\comments\models\CommentSearchModel */

$this->title = Yii::t('app', 'Comments Management');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comments-index">

    <h1><?php echo Html::encode($this->title) ?></h1>
    <?php Pjax::begin(['enablePushState' => false, 'timeout' => 5000]); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions' => ['style' => 'max-width: 50px;']
            ],
            [
                'attribute' => 'content',
                'contentOptions' => ['style' => 'max-width: 350px;'],
                'value' => function ($model) {
                    return \yii\helpers\StringHelper::truncate($model->content, 100);
                }
            ],
            [
                'attribute' => 'createdBy',
                'value' => function ($model) {
                    return $model->getAuthorName();
                },
                'filter' => $commentModel::getListAuthorsNames(),
                'filterInputOptions' => ['prompt' => 'Select Author', 'class' => 'form-control'],
            ],
            [
                'class' => EditableColumn::className(),
                'attribute' => 'status',
                'url' => ['edit-comment'],
                'value' => function ($model) {
                    return CommentStatus::getLabel($model->status);
                },
                'type' => 'select',
                'editableOptions' => function ($model) {
                    return [
                        'source' => Json::encode(CommentStatus::listData()),
                        'value' => $model->status,
                    ];
                },
                'filter' => CommentStatus::listData(),
                'filterInputOptions' => ['prompt' => 'Select Status', 'class' => 'form-control'],
            ],
            [
                'attribute' => 'createdAt',
                'value' => function ($model) {
                    return Yii::$app->formatter->asDatetime($model->createdAt);
                },
                'filter' => false,
            ],
            [
                'header' => 'Actions',
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}{delete}',
            ]
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>

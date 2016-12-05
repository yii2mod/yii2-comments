<?php

use yii\helpers\Html;
use yii\imperavi\Widget;
use yii\widgets\ActiveForm;
use yii2mod\moderation\enums\Status;

/* @var $this yii\web\View */
/* @var $model \yii2mod\comments\models\CommentModel */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('yii2mod.comments', 'Update Comment: {0}', $model->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('yii2mod.comments', 'Comments Management'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('yii2mod.comments', 'Update');
?>
<div class="comment-update">

    <h1><?php echo Html::encode($this->title) ?></h1>

    <div class="comment-form">
        <?php $form = ActiveForm::begin(); ?>
        <?php echo $form->field($model, 'content')->widget(Widget::class, [
            'options' => [
                'minHeight' => 300,
                'replaceDivs' => true,
                'paragraphize' => false,
            ],
            'id' => 'content',
        ]);
        ?>
        <?php echo $form->field($model, 'status')->dropDownList(Status::listData()); ?>
        <div class="form-group">
            <?php echo Html::submitButton(Yii::t('yii2mod.comments', 'Update'), ['class' => 'btn btn-primary']) ?>
            <?php echo Html::a(Yii::t('yii2mod.comments', 'Go Back'), ['index'], ['class' => 'btn btn-default']); ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

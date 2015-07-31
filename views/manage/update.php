<?php

use yii\helpers\Html;
use yii\imperavi\Widget;
use yii\widgets\ActiveForm;
use yii2mod\comments\models\enums\CommentStatus;

/* @var $this yii\web\View */
/* @var $model \yii2mod\comments\models\CommentModel */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Update Comment: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Comments Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="comment-update">

    <h1><?php echo Html::encode($this->title) ?></h1>

    <div class="comment-form">
        <?php $form = ActiveForm::begin(); ?>
        <?php echo $form->field($model, 'content')->widget(Widget::className(), [
            'options' => [
                'minHeight' => 300,
                'replaceDivs' => true,
                'paragraphize' => false,
            ],
            'id' => 'content',
        ]);
        ?>
        <?php echo $form->field($model, 'status')->dropDownList(CommentStatus::listData()); ?>
        <div class="form-group">
            <?php echo Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

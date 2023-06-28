<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this \yii\web\View */
/* @var $commentModel \yii2mod\comments\models\CommentModel */
/* @var $encryptedEntity string */
/* @var $formId string comment form id */
?>
<div class="nk-chat-editor">
    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => $formId,
            //'class' => '',
        ],
        'action' => Url::to(['/comment/default/create', 'entity' => $encryptedEntity]),
        'validateOnChange' => false,
        'validateOnBlur' => false,
    ]); ?>
    <div class="nk-chat-editor-form">

    <?php echo $form->field($commentModel, 'content', ['template' => '{input}{error}'])->textarea(['class' => 'form-control form-control-simple no-resize', 'placeholder' => Yii::t('yii2mod.comments', 'Add a comment...'), 'rows' => 4, 'data' => ['comment' => 'content']]); ?>
    <?php echo $form->field($commentModel, 'parentId', ['template' => '{input}'])->hiddenInput(['data' => ['comment' => 'parent-id']]); ?>
    </div>
    <ul class="nk-chat-editor-tools g-2">
        <li>
            <?php echo Html::a(Yii::t('yii2mod.comments', 'Click here to cancel reply.'), '#', ['id' => 'cancel-reply', 'class' => 'pull-right', 'data' => ['action' => 'cancel-reply']]); ?>
            <?php echo Html::submitButton('<em class="icon ni ni-send-alt"></em>', ['class' => 'btn btn-round btn-primary btn-icon comment-submit']); ?>
        </li>
    </ul>
    <?php $form->end(); ?>

</div>

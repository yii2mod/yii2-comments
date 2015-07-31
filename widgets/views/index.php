<?php
/* @var $this \yii\web\View */
/* @var $comments array */
/* @var $commentModel \yii2mod\comments\models\CommentModel */
/* @var $maxLevel null|integer coments max level */
/* @var $encryptedEntity string */
?>
<div class="comments row">
    <div class="col-md-11 col-sm-11">
        <div class="title-block clearfix">
            <h3 class="h3-body-title"><?php echo 'Comments'; ?></h3>

            <div class="title-seperator"></div>
        </div>
        <ol class="comments-list">
            <?php echo $this->render('_list', ['comments' => $comments, 'maxLevel' => $maxLevel]) ?>
        </ol>
        <?php if (!\Yii::$app->user->isGuest) : ?>
            <?php echo $this->render('_form', ['commentModel' => $commentModel, 'encryptedEntity' => $encryptedEntity]); ?>
        <?php endif; ?>
    </div>
</div>



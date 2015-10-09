<?php
/* @var $this \yii\web\View */
use yii\widgets\Pjax;

/* @var $comments array */
/* @var $commentModel \yii2mod\comments\models\CommentModel */
/* @var $maxLevel null|integer comments max level */
/* @var $encryptedEntity string */
/* @var $pjaxContainerId string */
?>
<?php Pjax::begin([
    'enablePushState' => false,
    'timeout' => 10000,
    'id' => $pjaxContainerId,
]); ?>
<div class="comments row">
    <div class="col-md-11 col-sm-11">
        <div class="title-block clearfix">
            <h3 class="h3-body-title"><?php echo Yii::t('app', 'Comments'); ?></h3>

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
<?php Pjax::end(); ?>


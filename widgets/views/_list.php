<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii2mod\editable\Editable;

/* @var $this \yii\web\View */
/* @var $model \yii2mod\comments\models\CommentModel */
/* @var $maxLevel null|integer comments max level */
$isMe = $model->createdBy === Yii::$app->user->id;
?>
<div class="chat is-<?= $isMe ? 'me' : 'you' ?>" id="comment-<?php echo $model->id; ?>">
    <?php if (!$isMe) { ?>
        <div class="chat-avatar">
            <?php echo Html::img($model->getAvatar(), ['alt' => $model->getAuthorName(), 'class' => 'user-avatar']); ?>
        </div>
    <?php } ?>
    <div class="chat-content" data-comment-content-id="<?php echo $model->id; ?>">
        <div class="chat-bubbles">
            <div class="chat-bubble">
                <div class="chat-msg">
                    <?php if (Yii::$app->getModule('comment')->enableInlineEdit && Yii::$app->getUser()->can('admin')): ?>
                        <?php echo Editable::widget([
                            'model' => $model,
                            'attribute' => 'content',
                            'url' => Url::to(['/comment/default/quick-edit']),
                            'options' => [
                                'id' => 'editable-comment-' . $model->id,
                            ],
                        ]); ?>
                    <?php else: ?>
                        <?php echo $model->getContent(); ?>
                    <?php endif; ?>
                </div>
                <ul class="chat-msg-more">
                    <?php if (Yii::$app->getUser()->can('admin')) : ?>
                        <li><?php echo Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Yii::t('yii2mod.comments', 'Delete'), '#', ['class' => 'delete-comment-btn', 'data' => ['action' => 'delete', 'url' => Url::to(['/comment/default/delete', 'id' => $model->id]), 'comment-id' => $model->id]]); ?></li>
                    <?php endif; ?>
                    <?php if (!Yii::$app->user->isGuest && ($model->level < $maxLevel || is_null($maxLevel))) : ?>
                        <li><?php echo Html::a("<span class='glyphicon glyphicon-share-alt'></span> " . Yii::t('yii2mod.comments', 'Reply'), '#', ['class' => 'reply-comment-btn', 'data' => ['action' => 'reply', 'comment-id' => $model->id]]); ?></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <ul class="chat-meta">
            <li><?php echo $model->getAuthorName(); ?></li>
            <li><?php echo Html::a($model->getPostedDate(), $model->getAnchorUrl(), ['class' => 'comment-date']); ?></li>
        </ul>
    </div>
</div>
<?php if ($model->hasChildren()) : ?>
    <ul class="children">
        <?php foreach ($model->getChildren() as $children) : ?>
            <?php echo $this->render('_list', ['model' => $children, 'maxLevel' => $maxLevel]); ?>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

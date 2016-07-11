<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $comment \yii2mod\comments\models\CommentModel */
/* @var $comments array */
/* @var $maxLevel null|integer comments max level */
?>
<?php if (!empty($comments)) : ?>
    <?php foreach ($comments as $comment) : ?>
        <li class="comment" id="comment-<?php echo $comment->id ?>" itemscope itemtype="http://schema.org/Comment">
            <div class="comment-content" data-comment-content-id="<?php echo $comment->id ?>">
                <div class="comment-author-avatar">
                    <?php echo Html::img($comment->getAvatar(), ['alt' => $comment->getAuthorName()]); ?>
                </div>
                <div class="comment-details">
                    <?php if ($comment->isActive): ?>
                        <div class="comment-action-buttons">
                            <?php if (Yii::$app->getUser()->can('admin')): ?>
                                <?php echo Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Yii::t('yii2mod.comments', 'Delete'), '#', ['data' => ['action' => 'delete', 'url' => Url::to(['/comment/default/delete', 'id' => $comment->id]), 'comment-id' => $comment->id]]); ?>
                            <?php endif; ?>
                            <?php if (!Yii::$app->user->isGuest && ($comment->level < $maxLevel || is_null($maxLevel))): ?>
                                <?php echo Html::a("<span class='glyphicon glyphicon-share-alt'></span> " . Yii::t('yii2mod.comments', 'Reply'), '#', ['class' => 'comment-reply', 'data' => ['action' => 'reply', 'comment-id' => $comment->id]]); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php echo Html::tag('meta', null, ['content' => Yii::$app->formatter->asDatetime($comment->createdAt, 'php:c'), 'itemprop' => 'dateCreated']); ?>
                    <?php echo Html::tag('meta', null, ['content' => Yii::$app->formatter->asDatetime($comment->updatedAt, 'php:c'), 'itemprop' => 'dateModified']); ?>
                    <div class="comment-author-name" itemprop="creator" itemscope itemtype="http://schema.org/Person">
                        <span itemprop="name"><?php echo $comment->getAuthorName(); ?></span>
                        <span class="comment-date">
                            <?php echo $comment->getPostedDate(); ?>
                        </span>
                    </div>
                    <div class="comment-body" itemprop="text">
                        <?php echo $comment->getContent(); ?>
                    </div>
                </div>
            </div>
            <?php if ($comment->hasChildren()): ?>
                <ul class="children">
                    <?php echo $this->render('_list', ['comments' => $comment->children, 'maxLevel' => $maxLevel]) ?>
                </ul>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
<?php endif; ?>

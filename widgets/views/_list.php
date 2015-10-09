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
        <li class="comment" id="comment-<?php echo $comment->id ?>">
            <div class="comment-content" data-comment-content-id="<?php echo $comment->id ?>">
                <div class="comment-author-avatar">
                    <?php echo $comment->getAvatar(['alt' => $comment->getAuthorName()]); ?>
                </div>
                <div class="comment-details">
                    <?php if ($comment->isActive): ?>
                        <div class="comment-action-buttons">
                            <?php if (Yii::$app->getUser()->can('admin')): ?>
                                <?php echo Html::a('<span class="glyphicon glyphicon-trash"></span> Delete', '#', ['data' => ['action' => 'delete', 'url' => Url::to(['/comment/default/delete', 'id' => $comment->id]), 'comment-id' => $comment->id]]); ?>
                            <?php endif; ?>
                            <?php if (!Yii::$app->user->isGuest && ($comment->level < $maxLevel || is_null($maxLevel))): ?>
                                <?php echo Html::a("<span class='glyphicon glyphicon-share-alt'></span> Reply", '#', ['class' => 'comment-reply', 'data' => ['action' => 'reply', 'comment-id' => $comment->id]]); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="comment-author-name">
                        <span><?php echo $comment->getAuthorName(); ?></span>
                        <span class="comment-date">
                            <?php echo $comment->getPostedDate(); ?>
                        </span>
                    </div>
                    <div class="comment-body">
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

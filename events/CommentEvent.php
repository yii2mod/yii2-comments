<?php

namespace yii2mod\comments\events;

use yii\base\Event;
use yii2mod\comments\models\CommentModel;

/**
 * Class CommentEvent
 *
 * @package yii2mod\comments\events
 */
class CommentEvent extends Event
{
    /**
     * @var CommentModel
     */
    private $_commentModel;

    /**
     * @return CommentModel
     */
    public function getCommentModel()
    {
        return $this->_commentModel;
    }

    /**
     * @param CommentModel $commentModel
     */
    public function setCommentModel(CommentModel $commentModel)
    {
        $this->_commentModel = $commentModel;
    }
}

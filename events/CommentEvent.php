<?php

namespace yii2mod\comments\events;

use yii\base\Event;
use yii\web\User;
use yii2mod\comments\models\CommentModel;

/**
 * Class CommentEvent
 * @package yii2mod\comments\events
 */
class CommentEvent extends Event
{
    /**
     * @var User
     */
    private $_user;

    /**
     * @var CommentModel
     */
    private $_commentModel;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
    }

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
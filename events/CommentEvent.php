<?php

namespace yii2mod\comments\events;

use yii\base\Event;
use yii\web\User;

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
}
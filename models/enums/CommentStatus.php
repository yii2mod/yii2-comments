<?php

namespace yii2mod\comments\models\enums;

use yii2mod\enum\helpers\BaseEnum;

/**
 * Class CommentStatus
 * @package yii2mod\comments\models\enums
 */
class CommentStatus extends BaseEnum
{
    const ACTIVE = 1;
    const DELETED = 2;

    /**
     * @var array
     */
    public static $list = [
        self::ACTIVE => 'Active',
        self::DELETED => 'Deleted'
    ];
}
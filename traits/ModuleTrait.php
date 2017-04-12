<?php

namespace yii2mod\comments\traits;

use Yii;
use yii2mod\comments\Module;

/**
 * Class ModuleTrait
 *
 * @package yii2mod\comments\traits
 */
trait ModuleTrait
{
    /**
     * @return Module
     */
    public function getModule()
    {
        return Yii::$app->getModule('comment');
    }
}

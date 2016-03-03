<?php

namespace yii2mod\comments;

use Yii;
use yii2mod\comments\models\CommentModel;

/**
 * Class Module
 * @package yii2mod\comments
 */
class Module extends \yii\base\Module
{
    /**
     * @var string module name
     */
    public static $name = 'comment';

    /**
     * @var string|null
     */
    public $userIdentityClass = null;

    /**
     * @var string comment model class, by default its yii2mod\comments\models\CommentModel::className();
     * You can override functions (getAuthor, getAvatar, ect) in your own comment model class
     */
    public $commentModelClass = null;

    /**
     * @var string the namespace that controller classes are in.
     * This namespace will be used to load controller classes by prepending it to the controller
     * class name.
     */
    public $controllerNamespace = 'yii2mod\comments\controllers';

    /**
     * Initializes the module.
     *
     * This method is called after the module is created and initialized with property values
     * given in configuration. The default implementation will initialize [[controllerNamespace]]
     * if it is not set.
     *
     * If you override this method, please make sure you call the parent implementation.
     */
    public function init()
    {
        if ($this->userIdentityClass === null) {
            $this->userIdentityClass = Yii::$app->getUser()->identityClass;
        }
        if ($this->commentModelClass === null) {
            $this->commentModelClass = CommentModel::className();
        }
        parent::init();
    }

}

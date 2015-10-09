<?php

namespace yii2mod\comments\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Json;
use yii2mod\comments\CommentAsset;
use yii2mod\comments\Module;

/**
 * Class Comment
 * @package app\components\comment\widgets
 */
class Comment extends Widget
{
    /**
     * @var \yii\db\ActiveRecord|null Widget model
     */
    public $model;

    /**
     * @var null|integer maximum comments level, level starts from 1, null - unlimited level;
     */
    public $maxLevel = 7;

    /**
     * @var string entity id attribute
     */
    public $entityIdAttribute = 'id';

    /**
     * @var array comment widget client options
     */
    public $clientOptions = [];

    /**
     * @var null pjax container id
     */
    private $pjaxContainerId = null;

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
        if ($this->model === null) {
            throw new InvalidConfigException('The "model" property must be set.');
        }
        $this->pjaxContainerId = 'comment-pjax-container-' . $this->getId();
        $this->registerAssets();
    }

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     */
    public function run()
    {
        /* @var $module Module */
        $module = Yii::$app->getModule(Module::$name);
        //Get comment model class from `comment` Module
        $commentModelClass = $module->commentModelClass;
        //Get entity from widget and hash it.
        $entity = $this->model;
        $entityId = $entity->{$this->entityIdAttribute};
        $entity = hash('crc32', $entity::className());
        //Get comment tree by entity and entityId
        $comments = $commentModelClass::getTree($entity, $entityId, $this->maxLevel);
        //Create comment model
        $commentModel = Yii::createObject($commentModelClass);
        //Encrypt entity and entityId values
        $encryptedEntity = Yii::$app->getSecurity()->encryptByKey(Json::encode([
            'entity' => $entity,
            'entityId' => $entityId
        ]), $module::$name);

        return $this->render('index', [
            'comments' => $comments,
            'commentModel' => $commentModel,
            'maxLevel' => $this->maxLevel,
            'encryptedEntity' => $encryptedEntity,
            'pjaxContainerId' => $this->pjaxContainerId
        ]);
    }

    /**
     * Register assets.
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        $this->clientOptions['pjaxContainerId'] = '#' . $this->pjaxContainerId;
        $options = Json::encode($this->clientOptions);
        CommentAsset::register($view);
        $view->registerJs('jQuery.comment(' . $options . ');');
    }

}

<?php

namespace yii2mod\comments\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii2mod\comments\CommentAsset;
use yii2mod\comments\Module;

/**
 * Class Comment
 * @package yii2mod\comments\widgets
 */
class Comment extends Widget
{
    /**
     * @var \yii\db\ActiveRecord|null Widget model
     */
    public $model;

    /**
     * @var string relatedTo custom text, for example: cms url: about-us, john comment about us page, etc.
     * By default - className:primaryKey of the current model
     */
    public $relatedTo = '';

    /**
     * @var string the view file that will render the comment tree and form for posting comments.
     */
    public $commentView = '@vendor/yii2mod/yii2-comments/widgets/views/index';

    /**
     * @var string comment form id
     */
    public $formId = 'comment-form';

    /**
     * @var string pjax container id
     */
    public $pjaxContainerId;

    /**
     * @var null|integer maximum comments level, level starts from 1, null - unlimited level;
     */
    public $maxLevel = 7;

    /**
     * @var boolean show deleted comments. Defaults to `false`.
     */
    public $showDeletedComments = false;

    /**
     * @var string entity id attribute
     */
    public $entityIdAttribute = 'id';

    /**
     * @var array comment widget client options
     */
    public $clientOptions = [];

    /**
     * @var string hash(crc32) from class name of the widget model
     */
    protected $entity;

    /**
     * @var integer primary key value of the widget model
     */
    protected $entityId;

    /**
     * @var string encrypted entity key from params: entity, entityId, relatedTo
     */
    protected $encryptedEntityKey;

    /**
     * Initializes the widget params.
     */
    public function init()
    {
        if (empty($this->model)) {
            throw new InvalidConfigException(Yii::t('yii2mod.comments', 'The "model" property must be set.'));
        }

        if (empty($this->pjaxContainerId)) {
            $this->pjaxContainerId = 'comment-pjax-container-' . $this->getId();
        }

        $this->entity = hash('crc32', get_class($this->model));
        $this->entityId = $this->model->{$this->entityIdAttribute};

        if (empty($this->entityId)) {
            throw new InvalidConfigException(Yii::t('yii2mod.comments', 'The "entityIdAttribute" value for widget model cannot be empty.'));
        }

        if (empty($this->relatedTo)) {
            $this->relatedTo = get_class($this->model) . ':' . $this->entityId;
        }

        $this->encryptedEntityKey = $this->generateEntityKey();

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
        $commentModelClass = $module->commentModelClass;
        $commentModel = Yii::createObject([
            'class' => $commentModelClass,
            'entity' => $this->entity,
            'entityId' => $this->entityId
        ]);

        $comments = $commentModelClass::getTree($this->entity, $this->entityId, $this->maxLevel, $this->showDeletedComments);

        return $this->render($this->commentView, [
            'comments' => $comments,
            'commentModel' => $commentModel,
            'maxLevel' => $this->maxLevel,
            'encryptedEntity' => $this->encryptedEntityKey,
            'pjaxContainerId' => $this->pjaxContainerId,
            'formId' => $this->formId,
            'showDeletedComments' => $this->showDeletedComments
        ]);
    }

    /**
     * Register assets.
     */
    protected function registerAssets()
    {
        $this->clientOptions['pjaxContainerId'] = '#' . $this->pjaxContainerId;
        $this->clientOptions['formSelector'] = '#' . $this->formId;
        $options = Json::encode($this->clientOptions);
        $view = $this->getView();
        CommentAsset::register($view);
        $view->registerJs("jQuery('#{$this->formId}').comment({$options});");
    }

    /**
     * Get encrypted entity key
     *
     * @return string
     */
    protected function generateEntityKey()
    {
        return utf8_encode(Yii::$app->getSecurity()->encryptByKey(Json::encode([
            'entity' => $this->entity,
            'entityId' => $this->entityId,
            'relatedTo' => $this->relatedTo
        ]), Module::$name));
    }
}
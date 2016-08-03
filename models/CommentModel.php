<?php

namespace yii2mod\comments\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii2mod\behaviors\PurifyBehavior;
use yii2mod\comments\models\enums\CommentStatus;
use yii2mod\comments\Module;

/**
 * Class CommentModel
 *
 * @property integer $id
 * @property string $entity
 * @property integer $entityId
 * @property integer $parentId
 * @property string $content
 * @property integer $createdBy
 * @property integer $updatedBy
 * @property string $relatedTo
 * @property integer $status
 * @property integer $level
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 */
class CommentModel extends ActiveRecord
{
    /**
     * @var null|array|ActiveRecord[] Comment children
     */
    protected $_children;

    /**
     * Declares the name of the database table associated with this AR class.
     *
     * @return string the table name
     */
    public static function tableName()
    {
        return '{{%Comment}}';
    }

    /**
     * Returns the validation rules for attributes.
     *
     * @return array validation rules
     */
    public function rules()
    {
        return [
            [['entity', 'entityId'], 'required'],
            ['content', 'required', 'message' => Yii::t('yii2mod.comments', 'Comment cannot be blank.')],
            [['content', 'entity', 'relatedTo'], 'string'],
            ['status', 'default', 'value' => CommentStatus::ACTIVE],
            ['status', 'in', 'range' => [CommentStatus::ACTIVE, CommentStatus::DELETED]],
            ['level', 'default', 'value' => 1],
            ['parentId', 'validateParentID'],
            [['entityId', 'parentId', 'createdBy', 'updatedBy', 'status', 'createdAt', 'updatedAt', 'level'], 'integer'],
        ];
    }

    /**
     * Validate parentId attribute
     *
     * @param $attribute
     */
    public function validateParentID($attribute)
    {
        if ($this->{$attribute} !== null) {
            $comment = self::find()->where(['id' => $this->{$attribute}, 'entity' => $this->entity, 'entityId' => $this->entityId])->active()->exists();
            if ($comment === false) {
                $this->addError('content', Yii::t('yii2mod.comments', 'Oops, something went wrong. Please try again later.'));
            }
        }
    }

    /**
     * Returns a list of behaviors that this component should behave as.
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdBy',
                'updatedByAttribute' => 'updatedBy',
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt'
            ],
            'purify' => [
                'class' => PurifyBehavior::className(),
                'attributes' => ['content'],
                'config' => [
                    'HTML.SafeIframe' => true,
                    'URI.SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%',
                    'AutoFormat.Linkify' => true,
                    'HTML.TargetBlank' => true,
                    'HTML.Allowed' => 'a[href], iframe[src|width|height|frameborder], img[src]'
                ]
            ]
        ];
    }

    /**
     * Returns the attribute labels.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('yii2mod.comments', 'ID'),
            'content' => Yii::t('yii2mod.comments', 'Content'),
            'entity' => Yii::t('yii2mod.comments', 'Entity'),
            'entityId' => Yii::t('yii2mod.comments', 'Entity ID'),
            'parentId' => Yii::t('yii2mod.comments', 'Parent ID'),
            'status' => Yii::t('yii2mod.comments', 'Status'),
            'level' => Yii::t('yii2mod.comments', 'Level'),
            'createdBy' => Yii::t('yii2mod.comments', 'Created by'),
            'updatedBy' => Yii::t('yii2mod.comments', 'Updated by'),
            'relatedTo' => Yii::t('yii2mod.comments', 'Related to'),
            'createdAt' => Yii::t('yii2mod.comments', 'Created date'),
            'updatedAt' => Yii::t('yii2mod.comments', 'Updated date'),
        ];
    }

    /**
     * @inheritdoc
     *
     * @return CommentQuery
     */
    public static function find()
    {
        return new CommentQuery(get_called_class());
    }

    /**
     * This method is called at the beginning of inserting or updating a record.
     *
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->parentId > 0) {
                $parentNodeLevel = (int)self::find()->select('level')->where(['id' => $this->parentId])->scalar();
                $this->level = $parentNodeLevel + 1;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * This method is called at the end of inserting or updating a record.
     *
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            // Mark all the nested comments as `deleted` after the comment was deleted
            if (array_key_exists('status', $changedAttributes) && $this->status == CommentStatus::DELETED) {
                self::updateAll(['status' => CommentStatus::DELETED], ['parentId' => $this->id]);
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Author relation
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        $module = Yii::$app->getModule(Module::$name);

        return $this->hasOne($module->userIdentityClass, ['id' => 'createdBy']);
    }

    /**
     * Get comments tree.
     *
     * @param $entity string model class id
     * @param $entityId integer model id
     * @param null $maxLevel
     * @param bool $showDeletedComments
     * @return array|\yii\db\ActiveRecord[] Comments tree
     */
    public static function getTree($entity, $entityId, $maxLevel = null, $showDeletedComments = true)
    {
        $query = self::find()->where([
            'entityId' => $entityId,
            'entity' => $entity,
        ])->with(['author']);

        if ($maxLevel > 0) {
            $query->andWhere(['<=', 'level', $maxLevel]);
        }

        if (!$showDeletedComments) {
            $query->active();
        }

        $models = $query->orderBy(['parentId' => SORT_ASC, 'createdAt' => SORT_ASC])->all();

        if (!empty($models)) {
            $models = self::buildTree($models);
        }

        return $models;
    }

    /**
     * Build comments tree.
     *
     * @param array $data Records array
     * @param int $rootID parentId Root ID
     * @return array|ActiveRecord[] Comments tree
     */
    protected static function buildTree(&$data, $rootID = 0)
    {
        $tree = [];

        foreach ($data as $id => $node) {
            if ($node->parentId == $rootID) {
                unset($data[$id]);
                $node->children = self::buildTree($data, $node->id);
                $tree[] = $node;
            }
        }

        return $tree;
    }

    /**
     * Delete comment.
     *
     * @return boolean whether comment was deleted or not
     */
    public function deleteComment()
    {
        $this->status = CommentStatus::DELETED;

        return $this->save(false, ['status', 'updatedBy', 'updatedAt']);
    }

    /**
     * $_children getter.
     *
     * @return null|array|ActiveRecord[] Comment children
     */
    public function getChildren()
    {
        return $this->_children;
    }

    /**
     * $_children setter.
     *
     * @param array|ActiveRecord[] $value Comment children
     */
    public function setChildren($value)
    {
        $this->_children = $value;
    }

    /**
     * Check if comment has children comment
     *
     * @return bool
     */
    public function hasChildren()
    {
        return !empty($this->_children);
    }

    /**
     * @return boolean whether comment is active or not
     */
    public function getIsActive()
    {
        return $this->status === CommentStatus::ACTIVE;
    }

    /**
     * @return boolean whether comment is deleted or not
     */
    public function getIsDeleted()
    {
        return $this->status === CommentStatus::DELETED;
    }

    /**
     * Get comment posted date as relative time
     *
     * @return string
     */
    public function getPostedDate()
    {
        return Yii::$app->formatter->asRelativeTime($this->createdAt);
    }

    /**
     * Get author name
     *
     * @return mixed
     */
    public function getAuthorName()
    {
        if ($this->author->hasMethod('getUsername')) {
            return $this->author->getUsername();
        }

        return $this->author->username;
    }

    /**
     * Get comment content
     *
     * @param string $deletedCommentText
     * @return string
     */
    public function getContent($deletedCommentText = 'Comment has been deleted.')
    {
        return $this->isDeleted ? Yii::t('yii2mod.comments', $deletedCommentText) : nl2br($this->content);
    }

    /**
     * Get avatar user
     *
     * @return string
     */
    public function getAvatar()
    {
        if ($this->author->hasMethod('getAvatar')) {
            return $this->author->getAvatar();
        }

        return "http://www.gravatar.com/avatar/00000000000000000000000000000000?d=mm&f=y&s=50";
    }

    /**
     * Get a list of the authors of the comments
     *
     * This function used for filter in gridView, for attribute `createdBy`.
     *
     * @return array
     */
    public static function getListAuthorsNames()
    {
        return ArrayHelper::map(self::find()->joinWith('author')->all(), 'createdBy', 'author.username');
    }

    /**
     * Get comments count
     *
     * @param bool $onlyActiveComments
     * @return int|string
     */
    public function getCommentsCount($onlyActiveComments = true)
    {
        $query = self::find()->where(['entity' => $this->entity, 'entityId' => $this->entityId]);

        if ($onlyActiveComments) {
            $query->active();
        }

        return $query->count();
    }
}
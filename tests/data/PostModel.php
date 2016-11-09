<?php

namespace yii2mod\comments\tests\data;

use yii\db\ActiveRecord;

/**
 * Class PostModel
 * @package yii2mod\comments\tests\data
 */
class PostModel extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'description'], 'required']
        ];
    }/**
     * This method is called at the end of inserting or updating a record.
     *
     * @param bool $insert
     * @param array $changedAttributes
     */
}
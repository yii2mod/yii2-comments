<?php

namespace yii2mod\comments\models;

use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Class CommentSearchModel
 * @package yii2mod\comments\models
 */
class CommentSearchModel extends CommentModel
{
    /**
     * Returns the validation rules for attributes.
     * @return array validation rules
     */
    public function rules()
    {
        return ArrayHelper::merge([
            [['id', 'createdBy', 'content', 'status'], 'safe'],
        ], parent::rules());
    }

    /**
     * Setup search function for filtering and sorting based on fullName field
     * @param $params
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 20)
    {
        $query = self::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize
            ]
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['id' => SORT_DESC],
        ]);

        // load the search form data and validate
        if (!($this->load($params))) {
            return $dataProvider;
        }

        //adjust the query by adding the filters
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['createdBy' => $this->createdBy]);
        $query->andFilterWhere(['status' => $this->status]);
        $query->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
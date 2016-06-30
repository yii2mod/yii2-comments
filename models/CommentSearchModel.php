<?php

namespace yii2mod\comments\models;

use yii\data\ActiveDataProvider;

/**
 * Class CommentSearchModel
 * @package yii2mod\comments\models
 */
class CommentSearchModel extends CommentModel
{
    /**
     * @return array validation rules
     */
    public function rules()
    {
        return [
            [['id', 'createdBy', 'content', 'status', 'relatedTo'], 'safe'],
        ];
    }

    /**
     * Setup search function for filtering and sorting.
     *
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
        $query->andFilterWhere(['like', 'relatedTo', $this->relatedTo]);

        return $dataProvider;
    }
}
<?php

namespace yii2mod\comments;

use yii\web\AssetBundle;

/**
 * Class CommentAsset
 *
 * @package yii2mod\comments
 */
class CommentAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@vendor/yii2mod/yii2-comments/assets';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/comment.js',
    ];

    /**
     * @inheritdoc
     */
    public $css = [
        'css/comment.css',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
    ];
}

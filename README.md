Comments module for Yii2 
========================

This module provide a comments managing system for Yii2 application.

[![Latest Stable Version](https://poser.pugx.org/yii2mod/yii2-comments/v/stable)](https://packagist.org/packages/yii2mod/yii2-comments) 
[![Total Downloads](https://poser.pugx.org/yii2mod/yii2-comments/downloads)](https://packagist.org/packages/yii2mod/yii2-comments) 
[![License](https://poser.pugx.org/yii2mod/yii2-comments/license)](https://packagist.org/packages/yii2mod/yii2-comments)
[![Build Status](https://travis-ci.org/yii2mod/yii2-comments.svg?branch=master)](https://travis-ci.org/yii2mod/yii2-comments)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yii2mod/yii2-comments "*"
```

or add

```json
"yii2mod/yii2-comments": "*"
```

to the require section of your composer.json.


Configuration
-----------------------

**Database Migrations**

Before using Comments Widget, we'll also need to prepare the database.
```php
php yii migrate --migrationPath=@vendor/yii2mod/yii2-comments/migrations
```

**Module setup**

To access the module, you need to add the following code to your application configuration:
```php
'modules' => [
    'comment' => [
        'class' => 'yii2mod\comments\Module',
        // when admin can edit comments on frontend
        'enableInlineEdit' => false,
    ],
]
```

**Comments management section setup**

To access the comments management section, you need to add the following code to your application configuration:
```php
  'controllerMap' => [
      'comments' => 'yii2mod\comments\controllers\ManageController',
      // Also you can override some controller properties in following way:
      'comments' => [
          'class' => 'yii2mod\comments\controllers\ManageController',
          'searchClass' => [
              'class' => 'yii2mod\comments\models\search\CommentSearch',
              'pageSize' => 25
          ],
          'indexView' => 'custom path to index view file',
          'updateView' => 'custom path to update view file',
      ],
  ]  
```
> Basically the above code must be placed in the `admin` module

You can then access to management section through the following URL:
http://localhost/path/to/index.php?r=comments/index

> If you have added this controller in the module admin, the URL will look like the following
> http://localhost/path/to/index.php?r=admin/comments/index

**Notes:**
> 1) Delete button visible only for user with `admin` role. 

> 2) When you delete a comment, all nested comments will be marked as `deleted`.

> 3) For change the any function in the CommentModel you can create your own model and change the property `commentModelClass` in the Comment Module.

> 4) You can implement your own methods `getAvatar` and `getUsername` in the `userIdentityClass`. Just create this methods in your User model. For example:

```php

public function getAvatar()
{
    // your custom code
}

public function getUsername()
{
    // your custom code
}

```

Usage
-------------------
**Basic example:**
```php
// the model to which are added comments, for example:
$model = Post::find()->where(['title' => 'some post title'])->one();

<?php echo \yii2mod\comments\widgets\Comment::widget([
    'model' => $model,
]); ?>
```

**You can use your own template for render comments:**

  ```php
<?php echo \yii2mod\comments\widgets\Comment::widget([
    'model' => $model,
    'commentView' => '@app/views/site/comments/index' // path to your template
]); ?>
  ```
  
**Use the following code for multiple widgets on the same page:**
  ```php
<?php echo \yii2mod\comments\widgets\Comment::widget([
        'model' => $model,
]); ?>

<?php echo \yii2mod\comments\widgets\Comment::widget([
        'model' => $model2,
        'formId' => 'comment-form2',
        'pjaxContainerId' => 'unique-pjax-container-id'
]); ?>
  ```
  
**To enable the pagination for comments list use the following code:**
```php
<?php echo \yii2mod\comments\widgets\Comment::widget([
      'model' => $model,
      'dataProviderConfig' => [
          'pagination' => [
              'pageSize' => 10
          ],
      ]
]); ?>
```

**Advanced example:**
```php
<?php echo \yii2mod\comments\widgets\Comment::widget([
      'model' => $model,
      'relatedTo' => 'User ' . \Yii::$app->user->identity->username . ' commented on the page ' . \yii\helpers\Url::current(),
      'maxLevel' => 2,
      // set `pageSize` with custom sorting
      'dataProviderConfig' => [ 
          'sort' => [
              'attributes' => ['id'],
              'defaultOrder' => ['id' => SORT_DESC],
          ],
          'pagination' => [
              'pageSize' => 10
          ],
      ],
       // your own config for comments ListView, for example:
      'listViewConfig' => [
          'emptyText' => Yii::t('app', 'No comments found.'),
      ]
]); ?>
```

## Using Events

You may use the following events:

```php
'modules' => [
    'comment' => [
        'class' => 'yii2mod\comments\Module',
        'controllerMap' => [
            'default' => [
                'class' => 'yii2mod\comments\controllers\DefaultController',
                'on beforeCreate' => function ($event) {
                    $event->getCommentModel();
                    // your custom code
                },
                'on afterCreate' => function ($event) {
                    $event->getCommentModel();
                    // your custom code
                },
                'on beforeDelete' => function ($event) {
                    $event->getCommentModel();
                    // your custom code
                },
                'on afterDelete' => function ($event) {
                    $event->getCommentModel();
                    // your custom code
                },
            ]
        ]
    ]
]
```

## Internationalization

All text and messages introduced in this extension are translatable under category 'yii2mod.comments'.
You may use translations provided within this extension, using following application configuration:

```php
return [
    'components' => [
        'i18n' => [
            'translations' => [
                'yii2mod.comments' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@yii2mod/comments/messages',
                ],
                // ...
            ],
        ],
        // ...
    ],
    // ...
];
```

  
#### Example comments
-----
![Alt text](http://res.cloudinary.com/zfort/image/upload/v1467214676/comments-preview.png "Example comments")

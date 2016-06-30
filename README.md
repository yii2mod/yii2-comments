Comments module for Yii2 
========================

This module provide a comments managing system for Yii2 application.

[![Latest Stable Version](https://poser.pugx.org/yii2mod/yii2-comments/v/stable)](https://packagist.org/packages/yii2mod/yii2-comments) 
[![Total Downloads](https://poser.pugx.org/yii2mod/yii2-comments/downloads)](https://packagist.org/packages/yii2mod/yii2-comments) 
[![License](https://poser.pugx.org/yii2mod/yii2-comments/license)](https://packagist.org/packages/yii2mod/yii2-comments)

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

To access the module, you need to add this to your application configuration:
```php
'modules' => [
    'comment' => [
        'class' => 'yii2mod\comments\Module'
    ]
]
```

**Comments management section setup**

To access the comments management section, you need to add this to your application configuration:
```php
  'controllerMap' => [
      'comments' => 'yii2mod\comments\controllers\ManageController'
  ]  
```
> Basically the above code must be placed in the `admin` module

You can then access to management section through the following URL:
http://localhost/path/to/index.php?r=comments/index

> If you have added this controller in the module admin, the URL will look like the following
> http://localhost/path/to/index.php?r=admin/comments/index

Usage
-------------------
> Delete button visible only for user with `admin` role.

**Basic example:**
```php
<?php echo \yii2mod\comments\widgets\Comment::widget([
    'model' => $model,
    'relatedTo' => 'User ' . \Yii::$app->user->identity->username . ' commented on the page ' . \yii\helpers\Url::current(), // for example
    'maxLevel' => 3, // maximum comments level, level starts from 1, null - unlimited level. Defaults to `7`
    'showDeletedComments' => true // show deleted comments. Defaults to `false`.
]); ?>
```

**You can use your own template for render comments:**

  ```php
<?php echo \yii2mod\comments\widgets\Comment::widget([
    'model' => $model,
    'commentView' => '@app/views/site/comments/index' //path to your template
]); ?>
  ```
  
**Use the following code for multiple widgets on the same page:**
  ```php
echo \yii2mod\comments\widgets\Comment::widget([
        'model' => $model,
    ]);

echo \yii2mod\comments\widgets\Comment::widget([
        'model' => $model2,
        'formId' => 'comment-form2'
    ]); 
  ```
  
#### Example comments
-----
![Alt text](http://res.cloudinary.com/zfort/image/upload/v1467214676/comments-preview.png "Example comments")

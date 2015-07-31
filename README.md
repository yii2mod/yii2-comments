Comments module for Yii2 
========================

This module provide a comments managing system for Yii2 application.

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


Usage
======================================

**1. Manage comments in admin panel:** 

    1.1 Add the following code to admin module section in main config

  ```php
  'controllerMap' => [
        'comments' => 'yii2mod\comments\controllers\ManageController'
  ]  
  ```
    1.2 Run migrations:
  
  ```php
      php yii migrate --migrationPath=@vendor/yii2mod/yii2-comments/migrations
  ```
  
**You can then access to management section through the following URL:**
  ```
    http://localhost/path/to/index.php?r=admin/comments/index
  ```
  

**2. Usage comment widget in view:**

    2.1 Add module to config section:
```php
'modules' => [
    'comment' => [
        'class' => 'yii2mod\comments\Module'
    ]
]
```
```
   2.2 Use in view:
```

```php
<?php echo \yii2mod\comments\widgets\Comment::widget(['model' => $model]); ?>
```

#### Example comments
-----
![Alt text](http://res.cloudinary.com/zfort/image/upload/v1438350103/comments_fooguz.png "Example comments")

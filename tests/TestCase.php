<?php

namespace yii2mod\comments\tests;

use yii\helpers\ArrayHelper;
use Yii;
use yii\helpers\FileHelper;
use yii2mod\comments\tests\data\Session;

/**
 * This is the base class for all yii framework unit tests.
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->mockApplication();

        $this->setupTestDbData();

        $this->createRuntimeFolder();
    }

    protected function tearDown()
    {
        $this->destroyApplication();
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    protected function mockApplication($config = [], $appClass = '\yii\web\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => $this->getVendorPath(),
            'components' => [
                'db' => [
                    'class' => 'yii\db\Connection',
                    'dsn' => 'sqlite::memory:',
                ],
                'request' => [
                    'hostInfo' => 'http://domain.com',
                    'scriptUrl' => 'index.php'
                ],
                'user' => [
                    'identityClass' => 'yii2mod\comments\tests\data\User',
                ],
                'i18n' => [
                    'translations' => [
                        'yii2mod.comments' => [
                            'class' => 'yii\i18n\PhpMessageSource',
                            'basePath' => '@yii2mod/comments/messages',
                        ],
                    ],
                ],
            ],
            'modules' => [
                'comment' => [
                    'class' => 'yii2mod\comments\Module',
                    'userIdentityClass' => '',
                    'controllerNamespace' => 'yii2mod\comments\tests\data'
                ]
            ],
        ], $config));
    }

    /**
     * @return string vendor path
     */
    protected function getVendorPath()
    {
        return dirname(__DIR__) . '/vendor';
    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    protected function destroyApplication()
    {
        Yii::$app = null;
    }

    /**
     * Setup tables for test ActiveRecord
     */
    protected function setupTestDbData()
    {
        $db = Yii::$app->getDb();

        // Structure :

        $db->createCommand()->createTable('Comment', [
            'id' => 'pk',
            'entity' => 'char(10) not null',
            'entityId' => 'integer not null',
            'content' => 'text not null',
            'parentId' => 'integer null',
            'level' => 'smallint not null default 1',
            'createdBy' => 'integer not null',
            'updatedBy' => 'integer not null',
            'relatedTo' => 'string(500) not null',
            'status' => 'smallint not null default 1',
            'createdAt' => 'integer not null',
            'updatedAt' => 'integer not null',
        ])->execute();

        $db->createCommand()->createTable('User', [
            'id' => 'pk',
            'username' => 'string',
            'email' => 'string',
        ])->execute();

        $db->createCommand()->createTable('Post', [
            'id' => 'pk',
            'title' => 'string',
            'description' => 'string',
            'createdAt' => 'integer'
        ])->execute();

        // Data :

        $db->createCommand()->insert('Comment', [
            'entity' => '025c69f4',
            'entityId' => 1,
            'content' => 'test content',
            'createdBy' => 1,
            'updatedBy' => 1,
            'relatedTo' => 'test comment',
            'createdAt' => time(),
            'updatedAt' => time()
        ])->execute();

        $db->createCommand()->insert('User', [
            'username' => 'John Doe',
            'email' => 'johndoe@domain.com'
        ])->execute();

        $db->createCommand()->insert('Post', [
            'title' => 'Post Title',
            'description' => 'some description',
            'createdAt' => time(),
        ])->execute();
    }

    /**
     * Create runtime folder
     */
    protected function createRuntimeFolder()
    {
        FileHelper::createDirectory(dirname(__DIR__) . '/tests/runtime');
    }
}
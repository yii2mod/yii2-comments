<?php

namespace yii2mod\comments\tests;

use yii\helpers\ArrayHelper;
use Yii;

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
    protected function mockApplication($config = [], $appClass = '\yii\console\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => $this->getVendorPath(),
            'components' => [
                'db' => [
                    'class' => 'yii\db\Connection',
                    'dsn' => 'sqlite::memory:',
                ]
            ],
            'modules' => [
                'comment' => [
                    'class' => 'yii2mod\comments\Module',
                    'userIdentityClass' => ''
                ]
            ]
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
            'entity' => 'string',
            'entityId' => 'integer',
            'content' => 'string',
            'parentId' => 'integer',
            'level' => 'integer',
            'createdBy' => 'integer',
            'updatedBy' => 'integer',
            'relatedTo' => 'string',
            'status' => 'integer',
            'createdAt' => 'integer',
            'updatedAt' => 'integer',
        ])->execute();

        // Data :

        $db->createCommand()->batchInsert('Comment', ['entity', 'entityId', 'content', 'parentId', 'level', 'createdBy', 'updatedBy', 'relatedTo', 'status', 'createdAt', 'updatedAt'], [
            ['73ccdea0', 1, 'content', null, 1, 1, 1, 'related to', 1, time(), time()],
            ['73ccdea0', 1, 'content 2', null, 1, 1, 1, 'related to', 2, time(), time()],
        ])->execute();
    }
}
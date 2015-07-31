<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m010101_100001_init_comment
 */
class m010101_100001_init_comment extends Migration
{
    /**
     * Create table `Comment`
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%Comment}}', [
            'id' => Schema::TYPE_PK,
            'entity' => 'CHAR(10) NOT NULL',
            'entityId' => Schema::TYPE_INTEGER . ' NOT NULL',
            'content' => Schema::TYPE_TEXT . ' NOT NULL',
            'parentId' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'level' => 'TINYINT(3) NOT NULL DEFAULT 1',
            'createdBy' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updatedBy' => Schema::TYPE_INTEGER . ' NOT NULL',
            'status' => 'TINYINT(2) NOT NULL DEFAULT 1',
            'createdAt' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updatedAt' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

        $this->createIndex('entity_index', '{{%Comment}}', 'entity');
        $this->createIndex('status_index', '{{%Comment}}', 'status');
    }

    /**
     * Drop table `Comment`
     */
    public function down()
    {
        $this->dropTable('{{%Comment}}');
    }

}
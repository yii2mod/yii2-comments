<?php

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
            'id' => $this->primaryKey(),
            'entity' => $this->char(10)->notNull(),
            'entityId' => $this->integer()->notNull(),
            'content' => $this->text()->notNull(),
            'parentId' => $this->integer()->null(),
            'level' => $this->smallInteger()->notNull()->defaultValue(1),
            'createdBy' => $this->integer()->notNull(),
            'updatedBy' => $this->integer()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'createdAt' => $this->integer()->notNull(),
            'updatedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-Comment-entity', '{{%Comment}}', 'entity');
        $this->createIndex('idx-Comment-status', '{{%Comment}}', 'status');
    }

    /**
     * Drop table `Comment`
     */
    public function down()
    {
        $this->dropTable('{{%Comment}}');
    }
}

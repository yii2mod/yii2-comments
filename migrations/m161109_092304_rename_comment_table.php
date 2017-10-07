<?php

use yii\db\Migration;

class m161109_092304_rename_comment_table extends Migration
{
    public function up()
    {
        if (null === Yii::$app->db->schema->getTableSchema('{{%comment}}')) {
            $this->renameTable('{{%Comment}}', '{{%comment}}');
        }
    }

    public function down()
    {
        if (null === Yii::$app->db->schema->getTableSchema('{{%Comment}}')) {
            $this->renameTable('{{%comment}}', '{{%Comment}}');
        }
    }
}

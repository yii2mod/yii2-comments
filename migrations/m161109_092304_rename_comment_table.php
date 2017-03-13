<?php

use yii\db\Migration;

class m161109_092304_rename_comment_table extends Migration
{
    public function up()
    {
        if (Yii::$app->db->schema->getTableSchema('{{%comment}}') === null) {
            $this->renameTable('{{%Comment}}', '{{%comment}}');
        }
    }

    public function down()
    {
        if (Yii::$app->db->schema->getTableSchema('{{%Comment}}') === null) {
            $this->renameTable('{{%comment}}', '{{%Comment}}');
        }
    }
}

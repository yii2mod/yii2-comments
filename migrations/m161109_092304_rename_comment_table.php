<?php

use app\components\Migration;

class m161109_092304_rename_comment_table extends Migration
{
    public function up()
    {
        $this->renameTable('{{%Comment}}', '{{%comment}}');
    }

    public function down()
    {
        $this->renameTable('{{%comment}}', '{{%Comment}}');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}

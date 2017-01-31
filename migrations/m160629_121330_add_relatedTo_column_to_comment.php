<?php

use yii\db\Migration;

/**
 * Handles adding relatedTo_column to table `comment`.
 */
class m160629_121330_add_relatedTo_column_to_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%Comment}}', 'relatedTo', $this->string(500)->notNull()->after('updatedBy'));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%Comment}}', 'relatedTo');
    }
}

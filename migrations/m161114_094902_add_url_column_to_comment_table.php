<?php

use yii\db\Migration;

/**
 * Handles adding url to table `comment`.
 */
class m161114_094902_add_url_column_to_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%comment}}', 'url', $this->text()->after('relatedTo'));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%comment}}', 'url');
    }
}

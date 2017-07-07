<?php

use yii\db\Migration;

/**
 * Handles the creation for table `parse_news_added_table`.
 */
class m170706_123548_create_parse_news_added_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('parse_news_added_table', [
            'id'          => $this->primaryKey(),
            'group_id'    => $this->string(),
            'src'         => $this->string(),
            'date_update' => $this->integer(),
            'date_create' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('parse_news_added_table');
    }
}

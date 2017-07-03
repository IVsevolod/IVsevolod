<?php

use yii\db\Migration;

/**
 * Handles the creation for table `item_pages_table`.
 */
class m170517_101829_create_item_pages_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('item_pages', [
            'id'          => $this->primaryKey(),
            'entity_type' => $this->string(),
            'entity_id'   => $this->integer(),
            'page'        => $this->integer(),
            'description' => $this->text(),
            'date_update' => $this->integer(),
            'date_create' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('item_pages');
    }
}

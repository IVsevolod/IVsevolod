<?php

use yii\db\Migration;

/**
 * Handles the creation for table `bulls_and_cows_table`.
 */
class m200304_093746_create_bulls_and_cows_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('bulls_and_cows', [
            'id'          => $this->primaryKey(),
            'number'      => $this->string(),
            'length'      => $this->integer(),
            'alias'       => $this->string(),
            'date_update' => $this->integer(),
            'date_create' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('bulls_and_cows');
    }
}

<?php

use yii\db\Migration;

/**
 * Handles the creation for table `bulls_and_cows_user_table`.
 */
class m200427_174323_create_bulls_and_cows_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('bulls_and_cows_user', [
            'id'          => $this->primaryKey(),
            'bac_id'      => $this->integer(),
            'number'      => $this->string(),
            'bulls'       => $this->integer(4),
            'cows'        => $this->integer(4),
            'date_update' => $this->integer(),
            'date_create' => $this->integer(),
        ]);

        $this->addForeignKey('bulls_and_cows_user-bulls_and_cows-fk', 'bulls_and_cows_user', 'bac_id', 'bulls_and_cows', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('bulls_and_cows_user');
    }
}

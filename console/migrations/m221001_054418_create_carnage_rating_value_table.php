<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%carnage_rating_value}}`.
 */
class m221001_054418_create_carnage_rating_value_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%carnage_rating_value}}', [
            'id'                => $this->primaryKey(),
            'carnage_rating_id' => $this->integer(),
            'carnage_user_id'   => $this->integer(),
            'value'             => $this->float(),
            'place'             => $this->integer(),
            'date_update'       => $this->integer(),
            'date_create'       => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey('carnage_rating_value-carnage_user-fk', 'carnage_rating_value', 'carnage_user_id', 'carnage_user', 'id');
        $this->addForeignKey('carnage_rating_value-carnage_rating-fk', 'carnage_rating_value', 'carnage_rating_id', 'carnage_rating', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%carnage_rating_value}}');
    }
}

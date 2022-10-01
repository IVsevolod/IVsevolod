<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%carnage_rating}}`.
 */
class m221001_054216_create_carnage_rating_table extends Migration
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

        $this->createTable('{{%carnage_rating}}', [
            'id'          => $this->primaryKey(),
            'type'        => $this->string(),
            'url'         => $this->string(),
            'title'       => $this->string(),
            'html_rating' => $this->text(),
            'date_update' => $this->integer(),
            'date_create' => $this->integer(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%carnage_rating}}');
    }
}

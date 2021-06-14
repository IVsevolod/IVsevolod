<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%carnage_log}}`.
 */
class m210614_140649_create_carnage_log_table extends Migration
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

        $this->createTable('{{%carnage_log}}', [
            'id'          => $this->primaryKey(),
            'url'         => $this->string(128),
            'city'        => $this->string(24),
            'log_id'      => $this->bigInteger(),
            'date_update' => $this->integer(),
            'date_create' => $this->integer(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%carnage_log}}');
    }
}

<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%carnage_user}}`.
 */
class m210614_140702_create_carnage_user_table extends Migration
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

        $this->createTable('{{%carnage_user}}', [
            'id'             => $this->primaryKey(),
            'username'       => $this->string(),
            'clan_img'       => $this->string(),
            'guild_img'      => $this->string(),
            'align_img'      => $this->string(),
            'a1'             => $this->integer(),
            'a2'             => $this->integer(),
            'a3'             => $this->integer(),
            'a4'             => $this->integer(),
            'b1'             => $this->integer(),
            'b2'             => $this->integer(),
            'b3'             => $this->integer(),
            'b4'             => $this->integer(),
            'mcount_attacks' => $this->integer(),
            'ma1'            => $this->integer(),
            'ma2'            => $this->integer(),
            'ma3'            => $this->integer(),
            'ma4'            => $this->integer(),
            'mb1'            => $this->integer(),
            'mb2'            => $this->integer(),
            'mb3'            => $this->integer(),
            'mb4'            => $this->integer(),
            'count_attacks'  => $this->integer(),
            'count_fights'   => $this->integer(),
            'date_update'    => $this->integer(),
            'date_create'    => $this->integer(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%carnage_user}}');
    }
}

<?php

use yii\db\Schema;
use yii\db\Migration;

class m160725_125012_addVkPost extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%vkpost}}', [
            'id'          => Schema::TYPE_PK,
            'post_id'     => Schema::TYPE_INTEGER . ' NOT NULL',
            'from_id'     => Schema::TYPE_INTEGER . ' NOT NULL',
            'owner_id'    => Schema::TYPE_INTEGER . ' NOT NULL',
            'date'        => Schema::TYPE_INTEGER . ' NOT NULL',
            'post_type'   => Schema::TYPE_STRING . ' NOT NULL',
            'text'        => Schema::TYPE_TEXT,
            'category'    => Schema::TYPE_STRING . ' DEFAULT "none"',
            'attachments' => Schema::TYPE_TEXT,
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%vkpost}}');
    }

}

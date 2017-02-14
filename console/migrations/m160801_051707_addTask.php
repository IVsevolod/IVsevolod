<?php

use yii\db\Schema;
use yii\db\Migration;

class m160801_051707_addTask extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%vk_task_run}}', [
            'id'       => Schema::TYPE_PK,
            'group_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'time'     => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%vk_task_run}}');
    }

}

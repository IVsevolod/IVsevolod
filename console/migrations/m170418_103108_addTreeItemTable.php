<?php

use yii\db\Migration;

class m170418_103108_addTreeItemTable extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tree_item}}', [
            'id'          => $this->primaryKey(),
            'user_id'     => $this->integer(),
            'title'       => $this->string(),
            'alias'       => $this->string(),
            'parent_id'   => $this->integer(),
            'deleted'     => $this->boolean()->defaultValue(0),
            'date_update' => $this->integer(),
            'date_create' => $this->integer(),
        ], $tableOptions);

        $this->addColumn('{{%item}}', 'entity_type', $this->string());
    }

    public function down()
    {
        $this->dropColumn('{{%item}}', 'entity_type');
        $this->dropTable('{{%tree_item}}');
    }

}

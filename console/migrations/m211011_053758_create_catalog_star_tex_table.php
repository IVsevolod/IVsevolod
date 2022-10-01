<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%catalog_star_tex}}`.
 */
class m211011_053758_create_catalog_star_tex_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%catalog_star_tex}}', [
            'id'          => $this->primaryKey(),
            'data'        => 'json',
            'info'        => 'json',
            'date_update' => $this->integer(),
            'date_create' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%catalog_star_tex}}');
    }
}

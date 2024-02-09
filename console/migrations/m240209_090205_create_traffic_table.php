<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%traffic}}`.
 */
class m240209_090205_create_traffic_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%traffic}}', [
            'id'          => $this->primaryKey(),
            'userAgent'   => $this->string(),
            'referer'     => $this->string(),
            'remoteIp'    => $this->string(32),
            'userIp'      => $this->string(32),
            'url'         => $this->string(),
            'get'         => $this->text(),
            'post'        => $this->text(),
            'date_update' => $this->dateTime(),
            'date_create' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%traffic}}');
    }
}

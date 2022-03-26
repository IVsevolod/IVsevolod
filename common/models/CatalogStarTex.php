<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * @property int @id
 * @property string $data
 * @property string $info
 * @property integer $date_update
 * @property integer $date_create
 */
class  CatalogStarTex extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_star_tex';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['date_create', 'date_update'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['date_update'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_update', 'date_create'], 'integer'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'data'        => 'Данные из api',
            'info'        => 'Данные из страницы',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

}
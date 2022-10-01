<?php

namespace common\models\carnage;

use yii\db\ActiveRecord;

/**
 * Class CarnageRating
 *
 * @package common\models\carnage
 *
 * @property int    $id
 * @property string $type
 * @property string $url
 * @property string $title
 * @property string $html_rating
 * @property int    $date_update
 * @property int    $date_create
 *
 */
class CarnageRating extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'carnage_rating';
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
            [['id', 'date_update', 'date_create'], 'integer'],
            [['type', 'url', 'title'], 'string'],
            [['html_rating'], 'string'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'type'        => 'Тип рейтинга',
            'url'         => 'Ссылка на рейтинг',
            'title'       => 'Заголовок',
            'html_rating' => 'Последний текст рейтинга',
            'date_update' => 'Дата последнего обновления',
            'date_create' => 'Date Create',
        ];
    }
}
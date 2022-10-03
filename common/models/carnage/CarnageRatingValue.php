<?php

namespace common\models\carnage;

use yii\db\ActiveRecord;

/**
 * Class CarnageRatingValue
 *
 * @package common\models\carnage
 *
 * @property int   $id
 * @property int   $carnage_rating_id
 * @property int   $carnage_user_id
 * @property float $value
 * @property int   $place
 * @property int   $date_update
 * @property int   $date_create
 *
 */
class CarnageRatingValue extends ActiveRecord
{

    /** @var string */
    public $nik;

    /** @var string */
    public $clanImg;

    /** @var string */
    public $guildImg;

    /** @var string */
    public $alignImg;

    /** @var string */
    public $title;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'carnage_rating_value';
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
            [['id', 'date_update', 'date_create', 'carnage_rating_id', 'carnage_user_id', 'place'], 'integer'],
            [['value'], 'number'],
            [['nik'], 'string']
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'carnage_rating_id' => 'Ссылка на рейтинг',
            'carnage_user_id'   => 'Ссылка на игрока',
            'value'             => 'Значение в рейтинге',
            'place'             => 'Место в рейтинге',
            'date_update'       => 'Дата обновление',
            'date_create'       => 'Date Create',
            'nik'               => 'Игрок',
            'clanImg'           => 'Клан',
            'guildImg'          => 'Гильдия',
            'alignImg'          => 'Склонность',
        ];
    }
}
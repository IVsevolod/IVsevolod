<?php

namespace common\models\carnage;

use yii\db\ActiveRecord;

/**
 * Class CarnageUser
 * @package common\models\carnage
 *
 * @property int    $id
 * @property string $username
 * @property int    $a1
 * @property int    $a2
 * @property int    $a3
 * @property int    $a4
 * @property int    $b1
 * @property int    $b2
 * @property int    $b3
 * @property int    $b4
 * @property int    $count_attacks
 * @property int    $count_fights
 * @property int    $date_update
 * @property int    $date_create
 *
 */
class CarnageUser extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'carnage_user';
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
            [['date_update', 'date_create', 'a1', 'a2', 'a3', 'a4', 'b1', 'b2', 'b3', 'b4', 'count_attacks', 'count_fights'], 'integer'],
            [['username'], 'string'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'username'      => 'Имя персонажа',
            'a1'            => 'Атака по голове',
            'a2'            => 'Атака по корпусу',
            'a3'            => 'Атака по поясу',
            'a4'            => 'Атака по ногам',
            'b1'            => 'Блок головы',
            'b2'            => 'Блок корпуса',
            'b3'            => 'Блок пояса',
            'b4'            => 'Блок ног',
            'count_attacks' => 'Количество атак',
            'count_fights'  => 'Количество боёв',
            'date_update'   => 'Date Update',
            'date_create'   => 'Date Create',
        ];
    }
}
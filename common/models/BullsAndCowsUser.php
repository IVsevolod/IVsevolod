<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Class BullsAndCows
 *
 * @property int     $id
 * @property int     $bac_id
 * @property string  $number
 * @property integer $bulls
 * @property integer $cows
 * @property integer $date_update
 * @property integer $date_create
 */
class BullsAndCowsUser extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bulls_and_cows_user';
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
            [['date_update', 'date_create', 'bac_id', 'bulls', 'cows'], 'integer'],
            [['number'], 'string'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'bac_id'      => 'Bac id',
            'number'      => 'Загаданное число',
            'bulls'       => 'Быков',
            'cows'        => 'оров',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

}

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
 * @property string  $number
 * @property integer $length
 * @property string  $alias
 * @property integer $date_update
 * @property integer $date_create
 */
class BullsAndCows extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bulls_and_cows';
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
            [['date_update', 'date_create', 'length'], 'integer'],
            [['number', 'alias'], 'string'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'number'      => 'Загаданное число',
            'length'      => 'Длина числа',
            'alias'       => 'Группировка',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

    public function generateAlias()
    {
        return md5($this->number . '_' . rand(10, 99) . '_'. $this->length);
    }

    public function generateNumber($length = 5)
    {
        $this->length = 5;
        $this->number = '49537';
        $this->alias = $this->generateAlias();
    }

}

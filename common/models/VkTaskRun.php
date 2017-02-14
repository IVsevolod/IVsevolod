<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * vktaskrun model
 *
 * @property integer $id
 * @property int $group_id
 * @property int $time
 */
class VkTaskRun extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vk_task_run';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }


}

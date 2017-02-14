<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * vkpost model
 *
 * @property integer $id
 * @property int $post_id
 * @property int $from_id
 * @property int $owner_id
 * @property int $date
 * @property string $post_type
 * @property string $text
 * @property string $category
 * @property string $attachments
 *
 */
class Vkpost extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vkpost';
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

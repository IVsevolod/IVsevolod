<?php
namespace common\models;

use common\models\User;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * ItemPage model
 *
 * @property integer $id
 * @property string $entity_type
 * @property integer $entity_id
 * @property integer $page
 * @property string $description
 * @property integer $date_update
 * @property integer $date_create
 *
 * @property TagEntity[] $tagEntity
 * @property User $user
 * @property Item $item
 */
class ItemPage extends VoteModel
{

    const THIS_ENTITY = 'item_page';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item_pages';
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
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date_create', 'date_update'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['date_update'],
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
            [['entity_id', 'page'], 'integer'],
            [['description'], 'default', 'value' => ''],
            [['date_update', 'date_create'], 'integer'],
            [['entity_type'], 'string', 'max' => 255],
            [['description'], 'string'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }

    public function generatePage()
    {
        $count = ItemPage::find()->where(['entity_type' => $this->entity_type, 'entity_id' => $this->entity_id])->count();
        $this->page = $count + 1;
    }

    /**
     * @return ActiveQuery $this
     */
    public function getItem()
    {
        return $this->hasOne(Item::class, ['id' => 'entity_id'])->andOnCondition(['entity_type' => Item::ENTITY_TYPE_LIBRARY]);
    }
}

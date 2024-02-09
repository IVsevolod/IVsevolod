<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * Class Tags
 *
 * @property int     $id
 * @property string  $userAgent
 * @property string  $referer
 * @property string  $remoteIp
 * @property string  $userIp
 * @property string  $url
 * @property string  $get
 * @property string  $post
 * @property integer $date_update
 * @property integer $date_create
 */
class Traffic extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'traffic';
    }

    public function getName()
    {
        return strip_tags($this->name);
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
                'value' => new Expression('NOW()'),
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
            [['userAgent', 'referer', 'remoteIp', 'userIp', 'url', 'get', 'post',], 'string'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'        => 'ID',
            'userAgent' => '',
            'referer'   => '',
            'remoteIp'  => '',
            'userIp'    => '',
            'url'       => '',
            'get'       => '',
            'post'      => '',
        ];
    }
}

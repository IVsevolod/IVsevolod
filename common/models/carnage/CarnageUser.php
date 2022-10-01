<?php

namespace common\models\carnage;

use common\models\User;
use yii\db\ActiveRecord;

/**
 * Class CarnageUser
 * @package common\models\carnage
 *
 * @property int    $id
 * @property string $username
 * @property string $align_img
 * @property string $clan_img
 * @property string $guild_img
 * @property int    $a1
 * @property int    $a2
 * @property int    $a3
 * @property int    $a4
 * @property int    $b1
 * @property int    $b2
 * @property int    $b3
 * @property int    $b4
 * @property int    $count_attacks
 * @property int    $ma1
 * @property int    $ma2
 * @property int    $ma3
 * @property int    $ma4
 * @property int    $mb1
 * @property int    $mb2
 * @property int    $mb3
 * @property int    $mb4
 * @property int    $mcount_attacks
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
            [
                [
                    'date_update',
                    'date_create',
                    'a1',
                    'a2',
                    'a3',
                    'a4',
                    'b1',
                    'b2',
                    'b3',
                    'b4',
                    'count_attacks',
                    'ma1',
                    'ma2',
                    'ma3',
                    'ma4',
                    'mb1',
                    'mb2',
                    'mb3',
                    'mb4',
                    'mcount_attacks',
                    'count_fights',
                ],
                'integer',
            ],
            [
                [
                    'a1', 'a2', 'a3', 'a4', 'b1', 'b2', 'b3', 'b4', 'ma1', 'ma2', 'ma3', 'ma4', 'mb1', 'mb2', 'mb3', 'mb4',
                    'count_attacks', 'mcount_attacks', 'count_fights'
                ], 'default', 'value' => 0
            ],
            [['username', 'clan_img', 'align_img', 'guild_img'], 'string'],
            [['clan_img', 'align_img', 'guild_img'], 'default', 'value' => ''],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'username'       => 'Имя персонажа',
            'clan_img'       => 'Изображение клана',
            'align_img'      => 'Изображение склонности',
            'guild_img'      => 'Изображение гильдии',
            'a1'             => 'Атака по голове',
            'a2'             => 'Атака по корпусу',
            'a3'             => 'Атака по поясу',
            'a4'             => 'Атака по ногам',
            'b1'             => 'Блок головы',
            'b2'             => 'Блок корпуса',
            'b3'             => 'Блок пояса',
            'b4'             => 'Блок ног',
            'count_attacks'  => 'Количество атак',
            'ma1'            => 'Атака по голове',
            'ma2'            => 'Атака по корпусу',
            'ma3'            => 'Атака по поясу',
            'ma4'            => 'Атака по ногам',
            'mb1'            => 'Блок головы',
            'mb2'            => 'Блок корпуса',
            'mb3'            => 'Блок пояса',
            'mb4'            => 'Блок ног',
            'mcount_attacks' => 'Количество атак',
            'count_fights'   => 'Количество боёв',
            'date_update'    => 'Date Update',
            'date_create'    => 'Date Create',
        ];
    }

    /**
     * @param string $username
     * @param array  $params
     *
     * @return CarnageUser
     */
    public static function getOrCreateUser(string $username, $params = [])
    {
        static $_usersByUsername = [];
        if (!isset($_usersByUsername[$username])) {
            $carnageUser = CarnageUser::find()->andWhere(['username' => $username])->one();
            if (empty($carnageUser) && !empty($username)) {
                $carnageUser = new CarnageUser([
                    'username'  => $username,
                    'align_img' => $params['align_img'] ?? '',
                    'clan_img'  => $params['clan_img'] ?? '',
                    'guild_img' => $params['guild_img'] ?? '',
                ]);
                if (!$carnageUser->save()) {
                    $carnageUser = null;
                }
            }
            $_usersByUsername[$username] = $carnageUser;
        }

        return $_usersByUsername[$username] ?? null;
    }

    public static function transformUrl($type, $src)
    {
        $path = '/app/frontend/web/img/carnage';
        $url = '/img/carnage';
        $newSrc = $src;
        switch ($type) {
            case 'align':
                $imgName = str_replace('http://img.carnage.ru/i/', '', $newSrc);
                $url .= '/align';
                $path .= '/align';
                break;
            case 'clan':
                $imgName = str_replace('http://img.carnage.ru/i/klan/', '', $newSrc);
                $url .= '/clan';
                $path .= '/clan';
                break;
            case 'guild':
                $imgName = str_replace('http://img.carnage.ru/i/guild/', '', $newSrc);
                $url .= '/guild';
                $path .= '/guild';
                break;
        }

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $fullName = $path . '/' . $imgName;
        $url = $url . '/' . $imgName;
        if (!file_exists($fullName)) {
            file_put_contents($fullName, file_get_contents($src));
        }

        return $url;
    }
}
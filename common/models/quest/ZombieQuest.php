<?php
namespace common\models\quest;

use yii\helpers\HtmlPurifier;

class ZombieQuest extends BaseQuest
{

    const FRAME_HOSPITAL_CORRIDOR = 'hospital_corridor';

    public $hospitalWarpFlag = [
        'look'     => false,
        'read'     => false,
    ];

    protected function getSessionName()
    {
        return 'questZombie';
    }

    public function getMap()
    {
        $map = [
            self::FRAME_START    => [
                'title'    => 'Начало',
                'view'     => 'main',
                'textView' => 'textViews/hospital/start',
                'infoView' => 'infoViews/main',
                'image'    => 'hospital_warp.jpg',
                'actions'  => [
                    'look' => [
                        'function' => function ($data) {
                            $this->hospitalWarpFlag['look'] = true;
                        }
                    ],
                    'read' => [
                        'function' => function ($data) {
                            $this->hospitalWarpFlag['read'] = true;
                        }
                    ],
                    'door' => [
                        'function' => function ($data) {
                            $this->frame = self::FRAME_HOSPITAL_CORRIDOR;
                        }
                    ],
                ],
            ],
            self::FRAME_HOSPITAL_CORRIDOR    => [
                'title'    => 'Начало',
                'view'     => 'main',
                'textView' => 'textViews/hospital/corridor',
                'infoView' => 'infoViews/main',
                'image'    => 'hospital_warp.jpg',
                'actions'  => [

                ]
            ]
        ];
        return $map;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['hospitalWarpFlag'], 'safe'],
            ]
        );
    }

    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [

            ]
        );
    }


}
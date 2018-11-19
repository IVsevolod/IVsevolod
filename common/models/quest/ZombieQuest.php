<?php
namespace common\models\quest;

use yii\helpers\HtmlPurifier;

class ZombieQuest extends BaseQuest
{
    const FRAME_HOSPITAL_CORRIDOR = 'hospital_corridor';
    const FRAME_HOSPITAL_YARD = 'hospital_yard';

    public $health = 80; // уровень здоровья

    public $hunger = 10; // уровень голода

    const OBJECT_LOCATION_SELF = 1;

    const OBJECT_TYPE_SHORT_RANGE_WEAPON = 1;
    const OBJECT_TYPE_LONG_RANGE_WEAPON = 2;
    const OBJECT_TYPE_BREAKING_WEAPON = 3;

    public $barik = false; // посещали барикаду в коридоре хоть раз?

//    const OBJECT_TYPE_LONG_RANGE_WEAPON = 2;


    public $objects = [
        1 => [
            'id'       => 1, // завязываемся на id=1 - это топор
            'type'     => self::OBJECT_TYPE_BREAKING_WEAPON, // тип предмета
            'title'    => 'пожарный топор', // название
            'weight'   => 5, // вес
            'location' => self::FRAME_HOSPITAL_CORRIDOR, // расположение
        ],
    ];

    /**
     * Возвращает список предметов в определенной локации
     *
     * @param $location
     *
     * @return array
     */
    public function getObjectsByLocation($location)
    {
        $objects = [];
        foreach ($this->objects ?? [] as $object) {
            if ($object['location'] == $location) {
                $objects[] = $object;
            }
        }
        return $objects;
    }


    /**
     * Взять предмет
     * @param $id
     * @param $location
     *
     * @return bool
     */
    public function takeObject($id, $location)
    {
        $object = $this->objects[$id] ?? null;
        if (($object['id'] ?? null) != $id) {
            $object = null;
        }
        if (($object['location'] ?? null) != $location) {
            $object = null;
        }
        if ($object) {
            $this->objects[$object['id']]['location'] = self::OBJECT_LOCATION_SELF;
            return true;
        }
        return false;
    }

    public $hospitalWarpFlag = [
        'look'             => false, // смотрели телик?
        'read'             => false, // читали записку?
        'corridorLocation' => 0,
        'tryToBreak'       => false,
    ];

    protected function getSessionName()
    {
        return 'questZombie';
    }

    public function getMap()
    {
        $map = [
            self::FRAME_START             => [
                'title'    => 'Начало',
                'view'     => 'main',
                'textView' => 'textViews/hospital/start',
                'infoView' => 'infoViews/main',
                'image'    => 'hospital_warp.jpg',
                'actions'  => [
                    'look'  => [
                        'function' => function ($data) {
                            $this->hospitalWarpFlag['look'] = true;
                        },
                    ],
                    'read'  => [
                        'function' => function ($data) {
                            $this->hospitalWarpFlag['read'] = true;
                        },
                    ],
                    'goout' => [
                        'function' => function ($data) {
                            $this->frame = self::FRAME_HOSPITAL_CORRIDOR;
                        },
                    ],
                ],
            ],
            self::FRAME_HOSPITAL_CORRIDOR => [
                'title'    => 'Начало',
                'view'     => 'main',
                'textView' => 'textViews/hospital/corridor',
                'infoView' => 'infoViews/main',
                'image'    => 'hospital_warp.jpg',
                'actions'  => [
                    'dark'      => [
                        'function' => function ($data) {
                            // Идем в темный конец корридора
                            $this->hospitalWarpFlag['corridorLocation'] = 1;
                        },
                    ],
                    'barricade' => [
                        'function' => function ($data) {
                            // Идем к баррикаде
                            $this->hospitalWarpFlag['corridorLocation'] = 2;                            
                        },
                    ],
                    'take-object' => [
                        'function' => function ($data) {
                            if ($data['id'] ?? false) {
                                $this->takeObject($data['id'], self::FRAME_HOSPITAL_CORRIDOR);
                            }
                        },
                    ],
                    'bop' => [
                        'function' => function ($data) {
                            if (!$this->hospitalWarpFlag['tryToBreak']) {
                                $this->health -= 2;
                                $this->hospitalWarpFlag['tryToBreak'] = true;
                            }
                        },
                    ],
                    'crash' => [
                        'function' => function ($data) {
                            $this->frame = self::FRAME_HOSPITAL_YARD;
                        },
                    ]
                ],
            ],
            self::FRAME_HOSPITAL_YARD => [
                'title'    => 'Двор больницы',
                'view'     => 'main',
                'textView' => 'textViews/hospital/yard',
                'infoView' => 'infoViews/main',
                'image'    => 'hospital_warp.jpg',
                'actions'  => [

                ]
            ],
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
                [['health', 'hunger', 'barik'], 'integer'],
                [['hospitalWarpFlag', 'objects',], 'safe'],
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
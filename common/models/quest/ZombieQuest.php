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

    /**
     * @var array Счетчик посещений локации. Больше нужно для определения, где он впервые
     */
    public $locationCount = [];

    /**
     * Переход на новую локацию, вызывает увеличение счетчика
     * @param       $newFrame
     * @param array $additionalKeys
     */
    public function setStepFrame($newFrame, $additionalKeys = [])
    {
        $this->frame = $newFrame;
        $countKey = $newFrame . join('#', $additionalKeys);
        if (!isset($this->locationCount[$countKey])) {
            $this->locationCount[$countKey] = 0;
        }
        $this->locationCount[$countKey]++;
    }

    /**
     * Получить количество посещений с учетом дополнительного параметра
     * @param       $frame
     * @param array $additionalKeys
     *
     * @return mixed|string
     */
    public function getLocationCount($frame, $additionalKeys = [])
    {
        $countKey = $frame . join('#', $additionalKeys);
        return $this->locationCount[$countKey] ?? '';
    }

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
                            $this->setStepFrame(self::FRAME_HOSPITAL_CORRIDOR);
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
                            $this->setStepFrame(self::FRAME_HOSPITAL_CORRIDOR, ['dark']);
                        },
                    ],
                    'barricade' => [
                        'function' => function ($data) {
                            // Идем к баррикаде
                            $this->hospitalWarpFlag['corridorLocation'] = 2;
                            $this->setStepFrame(self::FRAME_HOSPITAL_CORRIDOR, ['barricade']);
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
                            // Находим все предметы в руках
                            $objects = $this->getObjectsByLocation(ZombieQuest::OBJECT_LOCATION_SELF);
                            // Наличие разрушающего оружия, например топор
                            $hasBreakingWeapon = false;
                            foreach ($objects ?? [] as $object) {
                                if ($object['type'] == ZombieQuest::OBJECT_TYPE_BREAKING_WEAPON) {
                                    $hasBreakingWeapon = true;
                                }
                            }
                            if ($hasBreakingWeapon) {
                                $this->setStepFrame(self::FRAME_HOSPITAL_YARD);
                            } else {
                                $this->hospitalWarpFlag['corridorLocation'] = 2;
                                $this->setStepFrame(self::FRAME_HOSPITAL_CORRIDOR, ['barricade']);
                            }
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
                [['health', 'hunger'], 'integer'],
                [['hospitalWarpFlag', 'objects', 'locationCount'], 'safe'],
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
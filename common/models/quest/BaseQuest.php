<?php
namespace common\models\quest;

use yii\base\Model;

class BaseQuest extends Model
{

    const FRAME_RESTART = 'restart';
    const FRAME_START = 'start';

    /**
     * Полная карта событий
     * @return array
     */
    public function getMap()
    {
        $map = [];
        return $map;
    }

    /**
     * Шаг квеста по имени
     * @param $frame
     *
     * @return mixed|null
     */
    public function getFrame($frame)
    {
        $map = $this->getMap();
        return $map[$frame] ?? null;
    }

    public $frame = self::FRAME_START;

    public $oldFrame = '';

    public $actionKeyPress = '';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['frame', 'oldFrame', 'actionKeyPress'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'frame' => 'Кадр в квесте',
        ];
    }

    public function init()
    {
        parent::init();
        $this->loadFromSession();
    }

    protected function getSessionName()
    {
        return 'quest';
    }

    public function saveToSession()
    {
        \Yii::$app->session->set($this->getSessionName(), $this->getAttributes());
    }

    public function clearSession()
    {
        \Yii::$app->session->set($this->getSessionName(), []);
    }

    public function loadFromSession()
    {
        $attributes = \Yii::$app->session->get($this->getSessionName(), []);
        if (!empty($attributes)) {
            $this->setAttributes($attributes);
        }
    }

    /**
     * Установить значение, если валидно
     * @param $attribute
     * @param $value
     *
     * @return bool
     */
    public function setIfValid($attribute, $value)
    {
        $attributes = $this->getAttributes();
        if (key_exists($attribute, $attributes)) {
            $oldValue = $this->$attribute;
            $this->$attribute = $value;
            if ($this->validate([$attribute])) {
                return true;
            }
            $this->$attribute = $oldValue;
        }
        return false;
    }

    public function getPiece()
    {
        return $this->getFrame($this->frame);
    }

    public function getView()
    {
        $piece = $this->getPiece();
        return $piece['view'] ?? 'main';
    }

    public function process($data = [])
    {
        $piece = $this->getPiece();
        if (empty($piece)) {
            $this->frame = self::FRAME_START;
            $piece = $this->getPiece();
            if (empty($piece)) {
                return false;
            }
        }

        if ($data['action'] ?? false) {
            $action = $piece['actions'][$data['action']] ?? [];
            if ($action['newStep'] ?? false) {
                $this->oldFrame = $this->frame;
                $this->actionKeyPress = $data['action'];
                $this->frame = $action['newStep'];
            }
            if (is_callable($action['function'])) {
                $action['function']($data);
            }
        }
        return true;
    }
}
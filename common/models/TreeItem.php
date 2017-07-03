<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * Class TreeItem
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $alias
 * @property int $parent_id
 * @property bool $deleted
 * @property int $date_update
 * @property int $date_create
 *
 * @property User $user
 * @property TreeItem $parent
 */
class TreeItem extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tree_item';
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
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_update', 'date_create', 'parent_id', 'user_id', 'id'], 'integer'],
            [['title', 'alias'], 'string', 'max' => 255],
        ];
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->alias == "") {
                $title = $this->encodestring($this->title);
                $alias = $this->toAscii($title);
                $baseAlias = substr($alias, 0, 250);
                $alias = $baseAlias;
                $i = 1;
                $wheres = ['alias = :alias'];
                $params[':alias'] = $alias;
                if (!is_null($this->id)) {
                    $wheres[] = 'id <> :id';
                    $params = [':id' => $this->id];
                }
                $where = join(' AND ', $wheres);
                while ($findItem = Item::find()->where($where, $params)->one()) {
                    $alias = $baseAlias . '-' . $i;
                    $params[':alias'] = $alias;
                    $i++;
                    if ($i > 30) {
                        $alias = '';
                        break;
                    }
                }
                $this->alias = $alias;
            }
            return true;
        }
        return false;
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(TreeItem::className(), ['id' => 'parent_id']);
    }


    // функция превода текста с кириллицы в траскрипт
    function encodestring($str)
    {
        $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
        $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
        return str_replace($rus, $lat, $str);
    }

    function toAscii($str, $replace = array(), $delimiter = '-')
    {
        $str = trim($str);
        if (!empty($replace)) {
            $str = str_replace((array)$replace, ' ', $str);
        }

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

        return $clean;
    }

    public static function getTags($tagGroup)
    {
        $tags = Tags::findAll(['tag_group' => $tagGroup]);
        $result = [];
        foreach ($tags as $tag) {
            $result[] = $tag->getName();
        }
        return $result;
    }

    public static function getAllIdTitles()
    {
        /** @var TreeItem $trees */
        $trees = TreeItem::find()->all();
        $result = [];
        foreach ($trees as $tree) {
            $result[] = $tree->id . "." . $tree->title;
        }
        return $result;
    }

    public function getTree()
    {
        $child = [];
        foreach (TreeItem::find()->where(['parent_id' => $this->id])->all() ?? [] as $treeItem) {
            /** @var TreeItem $treeItem */
            $child[] = $treeItem->getTree();
        }
        return [
            'item' => $this,
            'child' => $child,
        ];
    }

    public function getList($attribute = 'id')
    {
        $list = [];
        $list[$this->$attribute] = $this;
        foreach (TreeItem::find()->where(['parent_id' => $this->id])->all() ?? [] as $treeItem) {
            /** @var TreeItem $treeItem */
            foreach ($treeItem->getList($attribute) as $item) {
                $list[$item->$attribute] = $item;
            }
        }

        return $list;
    }
}

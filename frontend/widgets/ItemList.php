<?php
namespace frontend\widgets;

use common\models\Item;
use common\models\TagEntity;
use common\models\Tags;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\data\Sort;
use yii\db\ActiveQuery;

class ItemList extends \yii\bootstrap\Widget
{

    const ITEM_LIST_DISPLAY_MAIN = 'main';
    const ITEM_LIST_DISPLAY_MINI = 'mini';

    const ORDER_BY_ID        = 'order_by_id';
    const ORDER_BY_LIKE      = 'order_by_like';
    const ORDER_BY_SHOW      = 'order_by_show';
    const ORDER_BY_LIKE_SHOW = 'order_by_like_show';

    const DATE_CREATE_LAST  = 'last';
    const DATE_CREATE_WEEK  = 'week';
    const DATE_CREATE_MONTH = 'month';
    const DATE_CREATE_ALL   = 'popular';

    public $lastId = 0;

    public $onlyItem = false;

    public $orderBy = self::ORDER_BY_ID;

    public $dateCreateType = self::DATE_CREATE_LAST;

    public $searchTag = "";

    public $userId = false;

    public $display = self::ITEM_LIST_DISPLAY_MAIN;

    public $limit = false;

    public $tagEntity = TagEntity::ENTITY_ITEM;

    public $tagGroup = Tags::TAG_GROUP_ALL;

    public $entity = Item::ENTITY_TYPE_ITEM;

    public $action = 'list';

    public function init()
    {
    }

    public function run()
    {
        if ($this->action == 'list') {
            $items = $this
                ->getAllItems($this->lastId, $this->orderBy, $this->dateCreateType, $this->searchTag, $this->userId, $this->limit)
                ->all();
            return $this->render(
                'itemList/list',
                [
                    'items'          => $items,
                    'onlyItem'       => $this->onlyItem,
                    'dateCreateType' => $this->dateCreateType,
                    'searchTag'      => $this->searchTag,
                    'display'        => $this->display,
                ]
            );
        } else if ($this->action == 'listview') {
            $query = $this
                ->getAllItems($this->lastId, $this->orderBy, $this->dateCreateType, $this->searchTag, $this->userId);
            if (is_null($this->orderBy)) {
                $sort = new Sort([
                    'attributes' => [
                        'id',
                        'show_count',
                        'title',
                    ],
                    'defaultOrder' => [
                        'show_count' => SORT_ASC
                    ],
                ]);
                $query->orderBy($sort->orders);
            }
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => $this->limit ?: 10,
                ],
            ]);
            return $this->render('itemList/listView', [
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * @param int $lastId
     * @param string $orderBy
     * @param string $dateCreateType
     * @param string $searchTag
     * @param bool $userId
     * @param bool $limit
     * @return ActiveQuery
     */
    public function getAllItems($lastId = 0, $orderBy = self::ORDER_BY_ID, $dateCreateType = self::DATE_CREATE_LAST, $searchTag = "", $userId = false, $limit = false)
    {
        $query = Item::find()->from(["t" => Item::tableName()])->andWhere('t.deleted = 0')->addSelect('*');
        if ($lastId != 0) {
            $query = $query->andWhere('t.id < :id', [':id' => $lastId]);
        }
        // Определяем сортировку
        if ($orderBy == self::ORDER_BY_ID) {
            $query = $query->orderBy('id DESC');
        } elseif ($orderBy == self::ORDER_BY_LIKE) {
            $query = $query->orderBy('like_count DESC');
        } elseif ($orderBy == self::ORDER_BY_SHOW) {
            $query = $query->orderBy('show_count DESC');
        } elseif ($orderBy == self::ORDER_BY_LIKE_SHOW) {
            $query = $query->addSelect(['(like_count * 15 + show_count) as like_show_count'])->orderBy('like_show_count DESC');
        }
        // Определяем за какой период будем показывать
        if (!empty($limit)) {
            $query = $query->limit((int)$limit);
        } elseif ($dateCreateType == self::DATE_CREATE_LAST) {
            $query = $query->limit(10);
        } elseif ($dateCreateType == self::DATE_CREATE_ALL) {
            $query = $query->limit(50);
        } elseif ($dateCreateType == self::DATE_CREATE_WEEK) {
            $query = $query->andWhere('t.date_create >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 WEEK))');
        } elseif ($dateCreateType == self::DATE_CREATE_MONTH) {
            $query = $query->andWhere('t.date_create >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 MONTH))');
        }

        if (!empty($userId)) {
            $query = $query->andWhere('user_id = :userId', [':userId' => $userId]);
        }

        if ($searchTag != "") {
            if (is_string($searchTag)) {
                $tags = Tags::findAll(['name' => $searchTag]);
            } else {
                $tags = Tags::find()->where(['name' => $searchTag, 'tag_group' => $this->tagGroup])->all();
            }

            $tagsId = [];
            foreach ($tags as $tag) {
                $tagsId[] = (int)$tag->id;
            }

            if (count($tagsId) > 0) {
                $query = $query
                    ->andWhere('(SELECT COUNT(*) as tagCount FROM `' . TagEntity::tableName() . '` te WHERE te.entity = "' . $this->tagEntity . '" AND te.entity_id = t.id  AND te.tag_id IN (' . join(',', $tagsId) . ')) > 0');
            } else {
                if (!is_string($searchTag)) {
                    $query->andWhere('false');
                }
            }
        }
        $query->andWhere(['entity_type' => $this->entity]);
        $query = $query->with(['tagEntity', 'tagEntity.tags']);

        return $query;
    }
}
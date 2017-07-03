<?php
namespace frontend\controllers;

use common\models\Alarm;
use common\models\Item;
use common\models\EntityLink;
use common\models\TagEntity;
use common\models\Tags;
use common\models\User;
use common\models\Video;
use common\models\Vote;
use frontend\models\Lang;
use frontend\widgets\ItemList;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Url;

/**
 * List controller
 */
class ListController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['add', 'edit', 'delete', 'alarm'],
                'rules' => [
                    [
                        'actions' => ['add', 'edit', 'delete', 'alarm'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }


    public function actionAdd()
    {
        if (Yii::$app->user->identity->username != 'IVsevolod') {
            return Yii::$app->getResponse()->redirect(Url::home());
        }
        $item = new Item();
        if ($item->load(Yii::$app->request->post())) {
            $item->description = \yii\helpers\HtmlPurifier::process($item->description, []);
            $item->user_id = Yii::$app->user->identity->getId();
            $item->like_count = 0;
            $item->show_count = 0;
            if ($item->save()) {
                // Добавляем теги
                $tags = explode(',', Yii::$app->request->post('tags'));
                if (is_array($tags)) {
                    $item->saveTags($tags);
                }

                return Yii::$app->getResponse()->redirect($item->getUrl());
            }
        }
        Yii::$app->params['jsVar']['tagsAll'] = Tags::getTags(Tags::TAG_GROUP_ALL);

        return $this->render(
            'add',
            ['item' => $item]
        );
    }

    public function actionIndex()
    {
        $searchTag = Yii::$app->request->get('tag', '');
        return $this->render('index', ['searchTag' => $searchTag]);
    }

    public function actionView()
    {
        $item = null;
        if ($id = Yii::$app->request->get('index', null)) {
            $item = Item::findOne((int)$id);
        } elseif ($id = Yii::$app->request->get('id', null)) {
            $item = Item::findOne((int)$id);
        } else {
            $alias = Yii::$app->request->get('alias', null);
            $item = Item::findOne(['alias' => $alias]);
        }
        if (empty($item)) {
            return;
        }

        if ($item->deleted) {
            return $this->render('viewDeleted');
        }

        if ($anchor = Yii::$app->request->get('comment', null)) {
            Yii::$app->params['jsVar']['anchor'] = $anchor;
        }

        if (!User::isBot()) {
            $item->addShowCount();
        }
        $thisUser = User::thisUser();
        $vote = !empty($thisUser) ? $thisUser->getVoteByEntity(Vote::ENTITY_ITEM, $id) : null;

        return $this->render(
            'view',
            [
                'item' => $item,
                'vote' => $vote,
            ]
        );
    }

    public function actionEdit($id)
    {
        /** @var Item $item */
        $item = Item::findOne($id);
        if ($item->user_id != User::thisUser()->id || $item->deleted) {
            return Yii::$app->getResponse()->redirect($item->getUrl());
        }
        if ($item && $item->load(Yii::$app->request->post())) {
            $item->description = \yii\helpers\HtmlPurifier::process($item->description, []);
            if ($item->save()) {
                TagEntity::deleteAll(['entity' => TagEntity::ENTITY_ITEM, 'entity_id' => $item->id]);
                $tags = explode(',', Yii::$app->request->post('tags'));
                if (is_array($tags)) {
                    $item->saveTags($tags);
                }

                return Yii::$app->getResponse()->redirect($item->getUrl());
            }
        }
        Yii::$app->params['jsZoukVar']['tagsAll'] = Tags::getTags(Tags::TAG_GROUP_ALL);

        return $this->render(
            'edit',
            ['item' => $item]
        );
    }

    public function actionDelete($id)
    {
        /** @var Item $item */
        $item = Item::findOne($id);
        $thisUser = User::thisUser();
        if ($item &&
            (
                $item->user_id == $thisUser->id ||
                $thisUser->reputation > Item::MIN_REPUTAION_BAD_ITEM_DELETE && $item->like_count < 0
            )
        ) {
            $item->deleted = 1;
            if ($item->save()) {
                return Yii::$app->getResponse()->redirect(Url::home());
            };
        }

        return Yii::$app->getResponse()->redirect($item->getUrl());
    }

    public function actionItems()
    {
        $lastId = Yii::$app->request->post('lastId', 0);
        $order = Yii::$app->request->post('order', ItemList::ORDER_BY_ID);
        $searchTag = Yii::$app->request->post('tag', '');
        return ItemList::widget(['lastId' => $lastId, 'onlyItem' => true, 'orderBy' => $order, 'searchTag' => $searchTag]);
    }

    public function actionWeek()
    {
        $searchTag = Yii::$app->request->get('tag', '');
        return $this->render('listWeek', ['searchTag' => $searchTag]);
    }

    public function actionMonth()
    {
        $searchTag = Yii::$app->request->get('tag', '');
        return $this->render('listMonth', ['searchTag' => $searchTag]);
    }

    public function actionPopular()
    {
        $searchTag = Yii::$app->request->get('tag', '');
        return $this->render('listPopular', ['searchTag' => $searchTag]);
    }
    
}
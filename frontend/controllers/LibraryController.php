<?php
namespace frontend\controllers;

use Cassandra\Date;
use common\models\Item;
use common\models\ItemPage;
use common\models\TagEntity;
use common\models\Tags;
use common\models\TreeItem;
use common\models\User;
use common\models\Vkpost;
use common\models\Vote;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class LibraryController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['add', 'edit', 'delete', 'addPage', 'editPage', 'create'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionCreate($category)
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        if ($user->reputation < 100) {
            return Yii::$app->getResponse()->redirect(Url::home());
        }
        $vkpost = new Vkpost();
        $vkpost->category=$category;
        $request= Yii::$app->request;
        if ($request->isPost) {
            $vkpost->load($request->post());
            $vkpost->post_id = 0;
            $vkpost->from_id = 0;
            $vkpost->owner_id = 0;
            $vkpost->date = time();
            $vkpost->post_type = 'post';
            if (!empty($vkpost->text) && $vkpost->save()) {
                return $this->redirect(["library/$category", 'id' => $vkpost->id]);
            }

        }
        return $this->render('create', [
            'vkpost' => $vkpost,
        ]);
    }

    public function actionIndex($id = null, $sort = null)
    {
        $list = [];
        if (empty($id)) {
            $id = Yii::$app->params['mainLibraryId'] ?? null;
        }
        if (!empty($id)) {
            $treeItem = TreeItem::findOne($id);
            $list = $treeItem ? $treeItem->getList() : [];
        }

        return $this->render('index', [
            'list'       => array_keys($list),
            'selectedId' => $id,
        ]);
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
            $item->entity_type = Item::ENTITY_TYPE_LIBRARY;
            if ($item->save()) {
                // Добавляем теги
                $tags = explode(',', Yii::$app->request->post('tags'));
                $tagsTree = [];
                foreach ($tags as $tag) {
                    $parentId = (int)(explode('.', $tag)[0]);
                    $tagsTree[] = $parentId;
                }
                if (is_array($tagsTree)) {
                    $item->saveTags($tagsTree, Tags::TAG_GROUP_LIBRARY_TREE, TagEntity::ENTITY_LIBRARY);
                }

                return Yii::$app->getResponse()->redirect($item->getUrl());
            }
        }
        Yii::$app->params['jsVar']['tagsAll'] = TreeItem::getAllIdTitles();

        return $this->render(
            'add',
            ['item' => $item]
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
                TagEntity::deleteAll(['entity' => TagEntity::ENTITY_LIBRARY, 'entity_id' => $item->id]);
                $tags = explode(',', Yii::$app->request->post('tags'));
                $tagsTree = [];
                foreach ($tags as $tag) {
                    $parentId = (int)(explode('.', $tag)[0]);
                    $tagsTree[] = $parentId;
                }
                if (is_array($tagsTree)) {
                    $item->saveTags($tagsTree, Tags::TAG_GROUP_LIBRARY_TREE, TagEntity::ENTITY_LIBRARY);
                }

                return Yii::$app->getResponse()->redirect($item->getUrl());
            }
        }
        Yii::$app->params['jsVar']['tagsAll'] = TreeItem::getAllIdTitles();

        return $this->render(
            'edit',
            ['item' => $item]
        );
    }

    public function actionDelete($id)
    {
        /** @var Item $item */
        $item = Item::findOne($id);
        if ($item->user_id != User::thisUser()->id || $item->deleted) {
            return Yii::$app->getResponse()->redirect($item->getUrl());
        }
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

    public function actionAddPage($id)
    {
        if (Yii::$app->user->identity->username != 'IVsevolod') {
            return Yii::$app->getResponse()->redirect(Url::home());
        }
        $item = Item::findOne($id);
        if (empty($item)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $itemPage = new ItemPage();
        $itemPage->entity_type = Item::THIS_ENTITY;
        $itemPage->entity_id = $item->id;
        $itemPage->generatePage();
        if ($itemPage->load(Yii::$app->request->post())) {
            $itemPage->description = \yii\helpers\HtmlPurifier::process($itemPage->description, []);
            if ($itemPage->save()) {
                return Yii::$app->getResponse()->redirect($item->getUrl());
            }
        }

        return $this->render(
            'addPage',
            ['item' => $itemPage]
        );
    }


    public function actionEditPage($id)
    {
        if (Yii::$app->user->identity->username != 'IVsevolod') {
            return Yii::$app->getResponse()->redirect(Url::home());
        }
        $itemPage = ItemPage::findOne($id);
        if (empty($itemPage)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        if ($itemPage->load(Yii::$app->request->post())) {
            $itemPage->description = \yii\helpers\HtmlPurifier::process($itemPage->description, []);
            if ($itemPage->save()) {
                /** @var Item $item */
                $item = $itemPage->item;
                $nPage = ItemPage::find()
                    ->where(['entity_type' => Item::ENTITY_TYPE_ITEM, 'entity_id' => $item->id])
                    ->andWhere(['<', 'page', $itemPage->page])
                    ->count() + 1;
                return Yii::$app->getResponse()->redirect($item->getUrl(false, ['page' => $nPage]));
            }
        }

        return $this->render(
            'editPage',
            ['item' => $itemPage]
        );
    }



    public function actionView($page = null)
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
            throw new NotFoundHttpException('The requested page does not exist.');
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

        $itemPage = ItemPage::find()->where([
            'entity_type' => Item::THIS_ENTITY,
            'entity_id'   => $item->id,
        ])->orderBy(['page' => SORT_ASC])->limit(1)->offset($page - 1)->one();
        if (empty($itemPage)) {
            $itemPage = ItemPage::findOne([
                'entity_type' => Item::THIS_ENTITY,
                'entity_id'   => $item->id,
            ]);
        }
        $pages = new Pagination([
            'totalCount' => ItemPage::find()->where([
                'entity_type' => Item::THIS_ENTITY,
                'entity_id'   => $item->id,
            ])->count(),
            'pageSize' => 1,
            'pageSizeParam' => false,
        ]);

        return $this->render(
            'view',
            [
                'item'     => $item,
                'vote'     => $vote,
                'itemPage' => $itemPage,
                'pages'    => $pages,
            ]
        );
    }

    public function actionHumor($id)
    {
        $vkPost = Vkpost::find()
            ->where(['category' => 'humor'])
            ->andWhere(['not', ['text' => '']])
            ->andWhere(['id' => $id])
            ->limit(1)
            ->one();
        return $this->render('vkpost', ['vkPost' => $vkPost]);
    }

    public function actionStory($id)
    {
        $vkPost = Vkpost::find()
            ->where(['category' => 'story'])
            ->andWhere(['not', ['text' => '']])
            ->andWhere(['id' => $id])
            ->limit(1)
            ->one();
        return $this->render('vkpost', ['vkPost' => $vkPost]);
    }

    public function actionHappy($id)
    {
        $vkPost = Vkpost::find()
            ->where(['category' => 'happy'])
            ->andWhere(['not', ['text' => '']])
            ->andWhere(['id' => $id])
            ->limit(1)
            ->one();
        return $this->render('vkpost', ['vkPost' => $vkPost]);
    }
    public function actionPrediction($id)
    {
        $vkPost = Vkpost::find()
            ->where(['category' => 'prediction'])
            ->andWhere(['not', ['text' => '']])
            ->andWhere(['id' => $id])
            ->limit(1)
            ->one();
        return $this->render('vkpost', ['vkPost' => $vkPost]);
    }
}
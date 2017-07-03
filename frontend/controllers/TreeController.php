<?php
namespace frontend\controllers;

use common\models\TreeItem;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;

class TreeController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
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
        $tree = new TreeItem();
        if ($tree->load(Yii::$app->request->post())) {
            $tree->user_id = Yii::$app->user->id;

            $parentId = (int)(explode('.', Yii::$app->request->post('parent'))[0]);
            if (!empty($parentId)) {
                $tree->parent_id = $parentId;
            }
            if ($tree->save()) {
                return $this->redirect(['library/index']);
            }
        }
        Yii::$app->params['jsVar']['tagsAll'] = TreeItem::getAllIdTitles();

        return $this->render('add', [
            'item' => $tree,
        ]);
    }
}
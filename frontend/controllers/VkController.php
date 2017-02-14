<?php
namespace frontend\controllers;

use common\components\VkontakteComponent;
use common\models\Vkpost;
use Yii;
use yii\db\Expression;
use yii\web\Controller;

/**
 * Visit controller
 */
class VkController extends Controller
{

    public function actionIndex()
    {


        return $this->render('index');
    }

    public function actionLoad()
    {
        $request = Yii::$app->getRequest();
        $access_token = empty($request->post('access_token')) ? '' : $request->post('access_token');
        $group_id = empty($request->post('group_id')) ? '' : $request->post('group_id');
        $group_name = empty($request->post('group_name')) ? '' : $request->post('group_name');
        $offset = empty($request->post('offset')) ? '0' : $request->post('offset');
        $limit = empty($request->post('limit')) ? '50' : $request->post('limit');
        $category = empty($request->post('category')) ? '' : $request->post('category');
        $vkpostsSave = [];
        if ($request->isPost) {
            /** @var VkontakteComponent $vkapi */
            $vkapi = Yii::$app->vkapi;
            $vkapi->initAccessToken($access_token);
            $vkposts = $vkapi->vkGet($group_id, $group_name, $offset, $limit);

            $count = array_shift($vkposts);
            while (count($vkposts) > 0) {
                $vkpostItem = array_shift($vkposts);

                $vkpost = new Vkpost();
                $vkpost->post_id = $vkpostItem->id;
                $vkpost->owner_id = isset($vkpostItem->owner_id) ? $vkpostItem->owner_id : 0;
                $vkpost->from_id = $vkpostItem->from_id;
                $vkpost->date = $vkpostItem->date;
                $vkpost->post_type = $vkpostItem->post_type;
                $vkpost->text = $vkpostItem->text;
                if (isset($vkpostItem->attachments)) {
                    $vkpost->attachments = json_encode($vkpostItem->attachments);
                }
                $vkpost->category = $category;

                if ($vkpost->save()) {
                    $vkpostsSave[] = $vkpost;
                }
            }
        }

        return $this->render(
            'load',
            [
                'access_token' => $access_token,
                'group_id'     => $group_id,
                'group_name'   => $group_name,
                'offset'       => $offset,
                'limit'        => $limit,
                'category'     => $category,
                'vkpostsSave'  => $vkpostsSave,
            ]
        );
    }

    public function actionPost()
    {
        $request = Yii::$app->getRequest();
        $access_token = empty($request->post('access_token')) ? '' : $request->post('access_token');
        $group_id = empty($request->post('group_id')) ? '' : $request->post('group_id');
        $category = empty($request->post('category')) ? '' : $request->post('category');
        $date = empty($request->post('date')) ? '' : $request->post('date');
        $count = empty($request->post('count')) ? '' : $request->post('count');
        $interval = empty($request->post('interval')) ? '' : $request->post('interval');


        if ($request->isPost) {
            $datestart = strtotime($date);

            if ($count > 25) {
                $count = 25;
            }

            /** @var VkontakteComponent $vkapi */
            $vkapi = Yii::$app->vkapi;
            $vkapi->initAccessToken($access_token);

            /** @var Vkpost[] $vkposts */
            $vkposts = Vkpost::find()
                ->where(['category' => $category])
                ->orderBy(new Expression('rand()'))
                ->limit($count)
                ->all();
            while ($count > 0) {
                /** @var Vkpost $vkpost */
                $vkpost = array_shift($vkposts);

                $response = $vkapi->vkPostFromModel($group_id, $datestart, $vkpost, []);

                echo date('Y.m.d H:i:s', $datestart) . '<br/>';
                echo $vkpost->text . '<br/>';

                $count--;
                $datestart = strtotime(' + ' . $interval . ' min', $datestart);
            }

            exit;
        }

        return $this->render(
            'post',
            [
                'access_token' => $access_token,
                'group_id'     => $group_id,
                'category'     => $category,
                'date'         => $date,
                'count'        => $count,
                'interval'     => $interval,
            ]
        );
    }

}
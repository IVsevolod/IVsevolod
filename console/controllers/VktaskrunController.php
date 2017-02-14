<?php

namespace console\controllers;

use common\models\Vkpost;
use common\models\VkTaskRun;
use yii\console\Controller;
use yii\db\Expression;

class VktaskrunController extends Controller
{
    public $defaultAction = 'init';

    private function runTask($access_token, $group_id, $category, $tags, $publicInterval)
    {
        $vkTaskRun = VkTaskRun::find()->andWhere(['group_id' => $group_id])->orderBy('time desc')->one();
        if (empty($vkTaskRun) || ($vkTaskRun->time < strtotime('+ 120 min', time()) )) {
            $vkapi = \Yii::$app->vkapi;
            $vkapi->initAccessToken($access_token);
            $vkposts = Vkpost::find()
                ->where(['category' => $category])
                ->orderBy(new Expression('rand()'))
                ->limit(3)
                ->all();

            $interval = rand(25, 45);
            if (empty($vkTaskRun)) {
                $datestart = strtotime(' + ' . $interval . ' min', time());
            } else {
                $datestart = strtotime(' + ' . $interval . ' min', $vkTaskRun->time);
            }
            foreach ($vkposts as $vkpost) {
                if ($datestart < time()) {
                    $datestart = strtotime('+7 min', time());
                }

                $response = $vkapi->vkPostFromModel($group_id, $datestart, $vkpost, $tags);
                $minValue = 25;
                $maxValue = 45;
                if (is_array($publicInterval)) {
                    $minValue = isset($publicInterval[0]) ? $publicInterval[0] : $minValue;
                    $maxValue = isset($publicInterval[0]) ? $publicInterval[0] : $maxValue;
                } else if (is_int($publicInterval)) {
                    $minValue = $publicInterval;
                    $maxValue = $publicInterval;
                }
                if ($minValue < 25) {
                    $minValue = 25;
                }
                if ($maxValue < $minValue) {
                    $maxValue = $minValue;
                }
                $interval = rand($minValue, $maxValue);
                if ($response) {
                    $vknewtaskrun = new VkTaskRun();
                    $vknewtaskrun->time = $datestart;
                    $vknewtaskrun->group_id = $group_id;
                    $vknewtaskrun->save();
                    $datestart = strtotime(' + ' . $interval . ' min', $datestart);
                } else {
                    var_dump($response);
                    break;
                }

            }

        }
    }

    public function actionInit()
    {
        $access_token = '7e8c6a1d84ad87b030212e02811ec1ab276c19a74831fd350d9fda18751edb87c46aa177b5096b7dc1fd7';
        $this->runTask($access_token, '40768668', ['happy', 'video', 'story'], ['happy', 'my_home_happy', 'для_души'], [25, 45]);
        $this->runTask($access_token, '124470635', ['humor', 'gif', 'story'], ['humor','анекдоты', 'приколы', 'юмор'], [25, 45]);

        $access_token = \Yii::$app->params['nurVkAccessToken'];
        if (!empty($access_token)) {
            $this->runTask($access_token, '2411559', ['humor', 'story'], [], [600, 840]);
        }

    }
}
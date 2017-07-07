<?php

namespace console\controllers;

use common\models\ParseNewsAdded;
use common\models\Vkpost;
use common\models\VkTaskRun;
use yii\console\Controller;
use yii\db\Expression;
use common\components\SimpleHtmlDom;

class VktaskrunController extends Controller
{
    public $defaultAction = 'init';

    private function runTask($access_token, $group_id, $category, $tags, $publicInterval, $limit = 3)
    {
        $vkTaskRun = VkTaskRun::find()->andWhere(['group_id' => $group_id])->orderBy('time desc')->one();
        if (empty($vkTaskRun) || ($vkTaskRun->time < strtotime('+ 120 min', time()))) {
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

    public function actionChebNews()
    {
        $group_id = '2411559';

        $html = SimpleHtmlDom::file_get_html('http://gov.cap.ru/Info.aspx?type=news');
        $items = $html->find('div.ListItem a.LI_Caption');
        $urls = [];
        foreach ($items as $item) {
            $url = $item->href;
            $url = 'http://gov.cap.ru/print.aspx' . substr($url, strpos($url, '?'));
            $url = str_replace('&amp;', '&', $url);
            $urls[] = $url;
        }

        /** @var ParseNewsAdded $urlsModel */
        $urlsModel = ParseNewsAdded::find()->where(['group_id' => $group_id, 'src' => $urls])->all();


        $existUrl = [];
        $existUrl[$group_id] = [];
        foreach ($urlsModel as $item) {
            $existUrl[$item->group_id][] = $item->src;
        }

        $i = 0;

        $access_token = \Yii::$app->params['nurVkAccessToken'];
        $vkapi = \Yii::$app->vkapi;
        $vkapi->initAccessToken($access_token);

        $vkTaskRun = VkTaskRun::find()->andWhere(['group_id' => $group_id])->orderBy('time desc')->one();

        $interval = rand(7, 25);
        if (empty($vkTaskRun)) {
            $datestart = strtotime(' + ' . $interval . ' min', time());
        } else {
            $datestart = strtotime(' + ' . $interval . ' min', $vkTaskRun->time);
        }
        foreach ($urls as $url) {
            if (!in_array($url, $existUrl[$group_id] ?? [])) {
                $i++;
                $htmlNews = SimpleHtmlDom::file_get_html($url);
                $titleElement = $htmlNews->find('#PrintTitle');
                $titleElement = reset($titleElement);
                $title = $titleElement->plaintext;
                $title = strip_tags($title);
                $textElements = $htmlNews->find('#PrintText p');
                $text = "";
                foreach ($textElements as $textElement) {
                    $newP = $textElement->plaintext;
                    $newP = strip_tags($newP);
                    if (!empty($newP)) {
                        $text .= "<br><br>" . $newP;
                    }
                }

                if ($datestart < time()) {
                    $datestart = strtotime('+7 min', time());
                }

                $imgElements = $htmlNews->find('#PrintText img');
                $attachments = [];
                foreach ($imgElements ?? [] as $imgElement) {
                    $src = $imgElement->src;
                    $src = str_replace('../', 'http://gov.cap.ru/', $src);
                    $attachments[] = [
                        'type'  => 'photo',
                        'photo' => [
                            'src_big' => $src,
                        ],
                    ];
                }


                $vkpost = new Vkpost();
                $vkpost->text = $title . '<br><br>' . $text;
                $vkpost->text = html_entity_decode($vkpost->text);

                $vkpost->attachments = json_encode($attachments);
                $vkpost->post_id = null;
                $response = $vkapi->vkPostFromModel($group_id, $datestart, $vkpost, ['новости', 'Чебоксары', 'Cheboksary']);

                if ($response) {
                    $vknewtaskrun = new VkTaskRun();
                    $vknewtaskrun->time = $datestart;
                    $vknewtaskrun->group_id = $group_id;
                    $vknewtaskrun->save();
                    $datestart = strtotime(' + ' . $interval . ' min', $datestart);

                    $newsAdded = new ParseNewsAdded();
                    $newsAdded->group_id = $group_id;
                    $newsAdded->src = $url;
                    $newsAdded->save();
                } else {
                    var_dump($response);
                }

            }
            if ($i > 1) {
                break;
            }
        }
    }

    public function actionInit()
    {
        //$access_token = '7e8c6a1d84ad87b030212e02811ec1ab276c19a74831fd350d9fda18751edb87c46aa177b5096b7dc1fd7';
        $access_token = '83b9dfd44fe0d74a000fe2e66f0c1c036ee451461745ed6c71aee3a58291bed35a84c438d6065d76e92d5';
        $this->runTask($access_token, '40768668', ['happy', 'video', 'story'], ['happy', 'my_home_happy', 'для_души'], [120, 600]);
        $this->runTask($access_token, '124470635', ['humor', 'gif', 'story'], ['humor', 'анекдоты', 'приколы', 'юмор'], [40, 120]);

        $access_token = \Yii::$app->params['nurVkAccessToken'];
        if (!empty($access_token)) {
            $this->runTask($access_token, '2411559', ['humor', 'story'], [], [600, 840], 1);
        }
    }
}
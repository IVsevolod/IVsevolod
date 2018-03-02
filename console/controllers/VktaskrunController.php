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

        $interval = rand(25, 37);
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
                array_shift($textElements);
                array_pop($textElements);
                $text = "";
                foreach ($textElements as $textElement) {
                    $newP = $textElement->plaintext;
                    $newP = strip_tags($newP);
                    if (!empty($newP)) {
                        $text .= "<br><br>" . $newP;
                    }
                }
                if (mb_strpos($text, 'Чебоксар') === false) {
                    $newsAdded = new ParseNewsAdded();
                    $newsAdded->group_id = $group_id;
                    $newsAdded->src = $url;
                    $newsAdded->save();

                    continue;
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

    public function actionChebFilms()
    {
        $group_id = '2411559';

        $html = SimpleHtmlDom::file_get_html('https://afisha.cheb.ru/kino/');
        $cinemas = $html->find('table.showfilm');

        $allFilms = [];
        foreach ($cinemas as $cinema) {
            if (!is_null($cinema)) {
                // Кинотеатры
                $titleCinema = $cinema->find('a p b');
                $titleCinema = array_shift($titleCinema);
                $titleCinema = $titleCinema->plaintext;
                $titleCinema = strip_tags($titleCinema);

                $rows = $cinema->find('tr');
                array_shift($rows);

                foreach ($rows ?? [] as $row) {
                    if (!empty($row)) {
                        // Фильм по кинотеатру в нужное время
                        $titleFilm = $row->find('td a');
                        $titleFilm = array_shift($titleFilm);
                        $urlFilm = $titleFilm->href;
                        $titleFilm = $titleFilm->plaintext;
                        $titleFilm = trim(strip_tags($titleFilm));

                        $aboutFilm = $row->find('td span');
                        $aboutFilm = array_shift($aboutFilm);
                        $aboutFilm = $aboutFilm->plaintext;
                        $aboutFilm = trim(strip_tags($aboutFilm));

                        $timeFilm = $row->find('td');
                        $timeFilm = array_shift($timeFilm);
                        $timeFilm = $timeFilm->plaintext;
                        $timeFilm = trim(strip_tags($timeFilm));

                        if (!empty($timeFilm) && !empty($aboutFilm) && !empty($titleFilm)) {
                            $allFilms[$titleFilm]['url'] = $urlFilm;
                            $allFilms[$titleFilm]['about'] = $aboutFilm;
                            $allFilms[$titleFilm]['cinemas'][$titleCinema][$timeFilm] = true;
                        }

                    }
                }
            }

        }


        $attachments = [];
        $attachmentsSrc = [];
        $text = "";
        if (!empty($allFilms)) {
            $text = '&#8505; Киноафиша на сегодня: <br>';
            foreach ($allFilms ?? [] as $title => $film) {
                $text .= '&#127909; ' . $title . ' ' . $film['about'] . '<br>';
                foreach ($film['cinemas'] as $titleCinema => $cinema) {
                    $text .= $titleCinema . ': ';
                    $times = array_keys($cinema);
                    $text .= join(', ', $times) . '<br>';
                }
                $text .= '<br>';

                if (count($attachments) < 10) {
                    parse_str(parse_url($film['url'], PHP_URL_QUERY), $urlParams);
                    if (!empty($urlParams['film'])) {
                        $src = 'https://afisha.cheb.ru/pics/big/' . $urlParams['film'] . '.jpg';
                        if (!in_array($src, $attachmentsSrc)) {
                            $attachments[] = [
                                'type'  => 'photo',
                                'photo' => [
                                    'src_big' => $src,
                                ],
                            ];
                            $attachmentsSrc[] = $src;
                        }
                    }
                }
            }
        }

        if (!empty($text)) {
            $interval = rand(1, 11);
            $datestart = strtotime('+ ' . $interval . ' min', time());

            $vkpost = new Vkpost();
            $vkpost->text = $text;
            $vkpost->text = html_entity_decode($vkpost->text);

            $access_token = \Yii::$app->params['nurVkAccessToken'];
            $vkapi = \Yii::$app->vkapi;
            $vkapi->initAccessToken($access_token);

            $vkpost->attachments = json_encode($attachments);
            $response = $vkapi->vkPostFromModel($group_id, $datestart, $vkpost, ['фильмы', 'Чебоксары', 'Cheboksary', 'кионтеатры', 'чтопосмотреть']);
        }
    }

    public function actionInit()
    {
        $access_token = \Yii::$app->params['nurVkAccessToken'];
        $this->runTask($access_token, '40768668', ['happy', 'video', 'story', 'picture'], ['happy', 'my_home_happy', 'для_души'], [120, 600]);
        $this->runTask($access_token, '124470635', ['humor', 'gif', 'story', 'picture'], ['humor', 'анекдоты', 'приколы', 'юмор'], [40, 120]);

        $access_token = \Yii::$app->params['nurVkAccessToken'];
        if (!empty($access_token)) {
            $this->runTask($access_token, '2411559', ['humor', 'story', 'picture'], [], [40, 100], 2);
        }
    }
}
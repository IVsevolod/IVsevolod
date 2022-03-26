<?php

namespace console\controllers;

use common\models\CatalogStarTex;
use yii\console\Controller;

class StarTexController extends Controller
{

    public function actionLoadList($page = 1)
    {
        $resultIsset = true;
        while ($resultIsset) {
            echo "Start load page $page\n";
            $url = "https://star-tex.ru/api/catalog/?page=$page&url=%2Fevropejskie_tkani%2F";
            $result = json_decode($this->curl($url), true);
            if (is_array($result['results'])) {
                foreach ($result['results'] as $item) {
                    $newModel = new CatalogStarTex();
                    $newModel->data = $item;
                    if (!$newModel->save()) {
                        var_dump($newModel->getErrors());
                        exit;
                    }
                    echo $item['id'] . ", ";
                }
            } else {
                $resultIsset = false;
            }
            echo "End load page $page\n\n";
            $page++;
        }
    }

    public function actionLoadItem()
    {
        $query = CatalogStarTex::find()->andWhere(['info' => null])->limit(1000);
        $count2 = 0;
        $i = 0;
        $allCount = $query->count();
        foreach ($query->each() as $starTex) {
            $i++;
            echo $i . " / " . $allCount . ": ";
            /** @var CatalogStarTex $starTex */
            $data = $starTex->data;
            $items = $data['items'];
            if (is_array($items) && count($items) > 0) {
                if (count($items) > 1) {
                    $count2++;
                }
                $firstItem = array_shift($items);
                $url = "https://star-tex.ru/api/catalog/item/?item_url=" . $firstItem['url'];
                $result = json_decode($this->curl($url), true);
                if (is_array($result)) {
                    $starTex->info = $result;
                    if (!$starTex->save()) {
                        var_dump($starTex->getErrors());
                        exit;
                    }
                    echo " Успех";
                }
                unset($result);
                unset($firstItem);
            }
            unset($items);
            unset($data);
            unset($starTex);
            echo "\n";
        }
    }

    protected function curl($url)
    {
        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);
        // return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // disable SSL verifying
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        // $output contains the output string
        $result = curl_exec($ch);

        if (!$result) {
            $errno = curl_errno($ch);
            $error = curl_error($ch);
        }

        // close curl resource to free up system resources
        curl_close($ch);

        if (isset($errno) && isset($error)) {
            throw new \Exception($error, $errno);
        }

        return $result;
    }
}
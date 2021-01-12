<?php

namespace frontend\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;

class ToolsController extends Controller
{


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['index', 'timestamp', 'base64'],
                'rules' => [
                    [
                        'actions' => ['index', 'timestamp', 'base64'],
                        'allow'   => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return string
     */
    public function actionTimestamp()
    {
        $request = \Yii::$app->request;
        $timestampEnter = $request->get('timestamp');
        if ($request->isPost) {
            $timestampForm = $request->post('timestamp');
            $timestampEnter = $timestampForm['value'] ?: $timestampEnter;

            if (empty($timestampForm['value']) && (!empty($timestampForm['year']) || !empty($timestampForm['month']) || !empty($timestampForm['day'])
                || !empty($timestampForm['hour']) || !empty($timestampForm['minute']) || !empty($timestampForm['second']))
            ) {
                $dateNow = new \DateTime("now", new \DateTimeZone("UTC"));
                $year = intval($timestampForm['year'] ?: $dateNow->format('Y'));
                $month = intval($timestampForm['month'] ?: $dateNow->format('m'));
                $day = intval($timestampForm['day'] ?: $dateNow->format('d'));
                $hour = intval($timestampForm['hour'] ?: 0);
                $minute = intval($timestampForm['minute'] ?: 0);
                $second = intval($timestampForm['second'] ?: 0);
                $dateNow->setDate($year, $month, $day);
                $dateNow->setTime($hour, $minute, $second);
                $timestampEnter = $dateNow->getTimestamp();
            }
        }

        return $this->render('timestamp', [
            'timestampEnter' => $timestampEnter
        ]);
    }

    public function actionBase64()
    {
        $request = \Yii::$app->request;
        $type = $request->post('type', $request->get('type', 'fromBase64'));
        $value = $request->post('inputText', $request->get('value', ''));
        if ($type === 'toBase64') {
            $result = base64_encode($value);
        } else {
            $result = base64_decode($value);
        }

        return $this->render('base64', [
            'type'   => $type,
            'value'  => $value,
            'result' => $result,
        ]);
    }

}
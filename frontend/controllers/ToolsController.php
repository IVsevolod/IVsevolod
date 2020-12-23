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
                'only'  => ['timestamp'],
                'rules' => [
                    [
                        'actions' => ['timestamp'],
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

}
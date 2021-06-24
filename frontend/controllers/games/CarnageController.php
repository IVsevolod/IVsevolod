<?php

namespace frontend\controllers\games;


use common\models\carnage\CarnageLog;
use common\models\carnage\CarnageUser;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class CarnageController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['index', 'stat', 'load-logs'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $logUrl = $request->post('logUrl', null);
            if (!empty($logUrl)) {
                if (CarnageLog::loadLog($logUrl)) {
                    \Yii::$app->session->addFlash('success', 'Лог успешно обработан');
                    return $this->redirect('index');
                } else {
                    return $this->redirect('index');
                }
            }
            $listLogText = $request->post('listUrl', null);
            if (!empty($listLogText)) {
                $urls = CarnageLog::parseUrls($listLogText, $request->post('city', 'lutecia'));
                $countTrue = 0;
                foreach ($urls as $url) {
                    if (CarnageLog::addLogStatusDraft($url)) {
                        $countTrue++;
                    }
                }
                \Yii::$app->session->addFlash('success', "Успешно добавлено в обработку $countTrue");
                return $this->redirect('index');
            }
        }

        return $this->render('index');
    }

    public function actionLoadLogs()
    {
        $carnageLogs = CarnageLog::find()->andWhere(['status' => CarnageLog::STATUS_NEW])->limit(10)->all();
        $countTrue = 0;
        $countFalse = 0;
        foreach ($carnageLogs as $carnageLog) {
            if (CarnageLog::loadLog($carnageLog, false)) {
                $countTrue++;
            } else {
                $countFalse++;
            }
        }
        if ($countFalse > 0) {
            \Yii::$app->session->addFlash('success', "Успешно обработано $countTrue; вышло ошибок $countFalse;");
        } else {
            \Yii::$app->session->addFlash('success', "Успешно обработано $countTrue");
        }

        return $this->render('loadLogs');
    }

    public function actionStat($username = '')
    {
        $carnageUser = CarnageUser::find()->andWhere(['username' => $username])->one();
        if (empty($carnageUser)) {
            \Yii::$app->session->addFlash('error', 'Персонаж не найден в базе');
        }

        return $this->render('stat', ['carnageUser' => $carnageUser]);
    }

}
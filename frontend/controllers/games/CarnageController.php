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
                'only'  => ['index', 'stat'],
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
        }

        return $this->render('index');
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
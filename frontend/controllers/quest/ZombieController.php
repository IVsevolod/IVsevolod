<?php
namespace frontend\controllers\quest;

use common\models\quest\CompetitionQuest;
use common\models\quest\ZombieQuest;
use yii\filters\AccessControl;
use yii\web\Controller;

class ZombieController extends Controller
{

    public $layout = 'quest/zombie/main';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only'  => [],
                'rules' => [
                    [
                        'actions' => [],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $quest = new ZombieQuest();

        $request = \Yii::$app->request;
        if ($request->isPost) {
            $data = $request->post();
        } else {
            $data = [];
        }

        if ($data['action'] == ZombieQuest::FRAME_RESTART) {
            $quest->clearSession();
            $quest->loadFromSession();
            $quest = new ZombieQuest();
        }

        $quest->process($data);

        $quest->saveToSession();
        return $this->render($quest->getView(), ['quest' => $quest]);
    }

}
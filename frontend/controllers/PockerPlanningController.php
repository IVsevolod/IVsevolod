<?php
namespace frontend\controllers;


use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * List controller
 */
class PockerPlanningController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['add', 'edit', 'delete', 'list'],
                'rules' => [
                    [
                        'actions' => ['add', 'edit', 'delete', 'list'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }


    public function actionList()
    {

        return $this->render('list', [

        ]);
    }

}
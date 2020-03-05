<?php
namespace frontend\controllers\games;

use common\models\BullsAndCows;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Game controller
 */
class BullsAndCowsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['index', 'start', 'enter-value'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {

        return $this->render('index');
    }

    public function actionStart()
    {
        $bullsAndCows = new BullsAndCows();
        $bullsAndCows->generateNumber(5);
        if ($bullsAndCows->save()) {
            return $this->redirect(['games/bulls-and-cows/enter-value', 'alias' => $bullsAndCows->alias]);
        }
        return $this->redirect(['games/bulls-and-cows/index']);
    }

    public function actionEnterValue($alias)
    {
        $request = \Yii::$app->request;
        /** @var BullsAndCows $bullsAndCows */
        $bullsAndCows = BullsAndCows::find()->andWhere(['alias' => $alias])->one();
        if (!$bullsAndCows) {
            return $this->redirect(['games/bulls-and-cows/index']);
        }

        if ($request->isPost) {
            $post = $request->post();
            $value = $post['value'];
            list($bulls, $cows) = $this->testBullAndCow($bullsAndCows->number, $value);
        }

        return $this->render('enterValue', [
            'oldValue' => $value ?? false,
            'bulls'    => $bulls ?? false,
            'cows'     => $cows ?? false,
            'alias'    => $alias,
        ]);
    }


    private function testBullAndCow($num1, $num2)
    {
        $b = 0;
        $c = 0;
        $chars = strlen($num2);
        for ($i = 0; $i < $chars; $i++) {
            if ($num2[$i] == $num1[$i]) {
                $b++;
            } elseif (mb_strpos($num1, $num2[$i]) !== false) {
                $c++;
            }
        }
        return [$b, $c];
    }


}

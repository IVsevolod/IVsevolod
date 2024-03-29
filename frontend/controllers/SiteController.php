<?php
namespace frontend\controllers;

use common\models\Item;
use common\models\Traffic;
use Yii;
use yii\base\InvalidParamException;
use yii\db\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
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
        $this->layout = 'mainOnePage';

        $request = \Yii::$app->request;
        $traffic = new Traffic([
            'userAgent' => $request->getUserAgent(),
            'referer'   => $request->getReferrer(),
            'remoteIp'  => $request->getRemoteIP(),
            'userIp'    => $request->getUserIP(),
            'url'       => $request->getUrl(),
            'get'       => json_encode($request->get()),
            'post'      => json_encode($request->post()),
        ]);
        $traffic->save();
        return $this->render('indexPage');
    }

    public function actionSitemap()
    {
        $urls = array();

        $items = Item::find()->where(['deleted' => 0])->all();

        foreach ($items as $item) {
            /** @var Item $item */
            $urls[] = [
                'url'      => $item->getUrl(true),
                'priority' => 0.8,
            ];
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
        echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $url) {
            echo '<url>';
            echo '<loc>' . $url['url'] . '</loc>';
            echo '<changefreq>weekly</changefreq>';
            echo '<priority>' . $url['priority'] . '</priority>';
            echo '</url>';
        }
        echo '</urlset>';
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionVote()
    {
        return $this->render('vote');
    }

    public function actionVoteAuto()
    {
        if (rand(1,100) > 60) {
            return $this->redirect(['site/index', 'tour' => true]);
        }
        return $this->render('voteAuto');
    }

    public function actionTriangle()
    {
        $this->layout = 'blank';
        $result = 0;
        $request = \Yii::$app->request;

        $session = Yii::$app->session;
        $count = $session->get('count', 0);
        $count++;
        $session->set('count', $count);

        if ($request->isPost) {
            $a = round($request->post('triangleA', 0), 4);
            $b = round($request->post('triangleB', 0));
            $c = $request->post('triangleC', "");

            if ($c === "") {
                $result = 'Неизвестная ошибка';
            }
            if (empty($a) || empty($b) || empty($c)) {
                $result = 'Сторона не должна быть нулевой';
            }
            if ($a < 0 || $c < 0) {
                $result = 'Сторона должна быть положительной';
            }

            $p = ($a + $b + $c) / 2;
            if ($count % 4 == 0) {
                $result = '';
            } else {
                $result = round(sqrt($p * ($p - $a) * ($p - $b) * ($p - $c)), 2);
            }
            if ($count % 7 === 0) {
                $c = '';
            }
        }
        \Yii::error(['$result' => $result, '$count' => $count, '$a' => $a ?? '', '$b' => $b ?? '', '$c' => $c ?? ''], 'triangle');

        return $this->render('triangle', [
            'result' => $result,
            'count'  => $count,
            'a'      => $a ?? '',
            'b'      => $b ?? '',
            'c'      => $c ?? '',
        ]);
    }
}

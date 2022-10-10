<?php

namespace frontend\controllers\games;


use common\models\carnage\CarnageLog;
use common\models\carnage\CarnageRating;
use common\models\carnage\CarnageRatingValue;
use common\models\carnage\CarnageUser;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

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
                'only'  => [
                    'index', 'stat', 'load-logs',
                    'rating-list', 'rating-index', 'rating-view', 'rating-init',
                    'version-extension', 'extension',
                ],
                'rules' => [
                    [
                        'actions' => [
                            'rating-list', 'rating-index', 'rating-view', 'user-list', 'user-view',
                            'version-extension', 'extension',
                        ],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
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


    public function actionRatingInit()
    {
        $ratings = CarnageRating::find()->indexBy('type')->all();
        if (!isset($ratings['fame_today'])) {
            $newCarnageRating = new CarnageRating([
                'title' => 'Доблесть',
                'type'  => 'fame_today',
                'url'   => 'http://top.carnage.ru/daily_rating/fame/today/',
            ]);
            $newCarnageRating->save();
        }
        if (!isset($ratings['fame_series_today'])) {
            $newCarnageRating = new CarnageRating([
                'title' => 'Увеличения победной серии',
                'type'  => 'fame_series_today',
                'url'   => 'http://top.carnage.ru/daily_rating/fame_series/today/',
            ]);
            $newCarnageRating->save();
        }
        if (!isset($ratings['base_expa_today'])) {
            $newCarnageRating = new CarnageRating([
                'title' => 'Базовый опыт',
                'type'  => 'base_expa_today',
                'url'   => 'http://top.carnage.ru/daily_rating/base_expa/today/',
            ]);
            $newCarnageRating->save();
        }
        if (!isset($ratings['karma_today'])) {
            $newCarnageRating = new CarnageRating([
                'title' => 'Изменение кармы',
                'type'  => 'karma_today',
                'url'   => 'http://top.carnage.ru/daily_rating/karma/today/',
            ]);
            $newCarnageRating->save();
        }

        return $this->redirect(['games/carnage/rating-list']);
    }

    /**
     * Список всех рейтингов
     * @return string
     */
    public function actionRatingList()
    {
        $query = CarnageRating::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('rating/list', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Список конкретного рейтинга
     * @param $id
     *
     * @return string
     */
    public function actionRatingIndex($id)
    {
        $carnageRating = CarnageRating::find()->andWhere(['id' => $id])->one();
        $query = CarnageRatingValue::find()
            ->andWhere(['carnage_rating_id' => $carnageRating->id ?? null])
            ->leftJoin(['cu' => CarnageUser::tableName()], "cu.id=carnage_user_id")
            ->groupBy(['carnage_user_id'])
            ->select([
                'nik'               => 'cu.username',
                'clanImg'           => 'cu.clan_img',
                'alignImg'          => 'cu.align_img',
                'guildImg'          => 'cu.guild_img',
                'value'             => new Expression("CAST(SUBSTRING_INDEX(GROUP_CONCAT(value ORDER BY carnage_rating_value.id DESC SEPARATOR '-'), '-', 1) as DECIMAL)"),
                'place'             => new Expression("CAST(SUBSTRING_INDEX(GROUP_CONCAT(place ORDER BY carnage_rating_value.id DESC SEPARATOR '-'), '-', 1) as SIGNED)"),
                'date_update'       => 'max(carnage_rating_value.date_update)',
                'carnage_rating_id' => 'carnage_rating_value.carnage_rating_id',
                'carnage_user_id'   => 'carnage_rating_value.carnage_user_id',
            ])
        ;

        $searchModel = new CarnageRatingValue();
        $request = \Yii::$app->request;
        $searchModel->load($request->get());

        $query->andFilterWhere(['place' => $searchModel->place]);
        $query->andFilterWhere(['like', 'value', $searchModel->value]);
        $query->andFilterWhere(['like', 'cu.username', $searchModel->nik]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'place' => SORT_ASC,
                ],
                'attributes' => [
                    'nik',
                    'value',
                    'place' => [
                        'asc' => new Expression("CASE WHEN CAST(SUBSTRING_INDEX(GROUP_CONCAT(place ORDER BY carnage_rating_value.id DESC SEPARATOR '-'), '-', 1) as SIGNED)=0 THEN 9999 ELSE CAST(SUBSTRING_INDEX(GROUP_CONCAT(place ORDER BY carnage_rating_value.id DESC SEPARATOR '-'), '-', 1) as SIGNED) END ASC"),
                        'desc' => new Expression("CASE WHEN CAST(SUBSTRING_INDEX(GROUP_CONCAT(place ORDER BY carnage_rating_value.id DESC SEPARATOR '-'), '-', 1) as SIGNED)=0 THEN 0 ELSE CAST(SUBSTRING_INDEX(GROUP_CONCAT(place ORDER BY carnage_rating_value.id DESC SEPARATOR '-'), '-', 1) as SIGNED) END DESC"),
                    ],
                    'date_update',
                ],
            ],
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        return $this->render('rating/index', [
            'dataProvider'  => $dataProvider,
            'carnageRating' => $carnageRating,
            'searchModel'   => $searchModel,
        ]);
    }

    public function actionRatingView($ratingId, $userId)
    {
        $carnageUser = CarnageUser::find()->andWhere(['id' => $userId])->one();
        $carnageRating = CarnageRating::find()->andWhere(['id' => $ratingId])->one();
        $query = CarnageRatingValue::find()->andWhere([
            'carnage_rating_id' => $carnageRating->id ?? null,
            'carnage_user_id'   => $carnageUser->id ?? null,
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date_update' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('rating/view', [
            'dataProvider'  => $dataProvider,
            'carnageRating' => $carnageRating,
            'carnageUser'   => $carnageUser,
        ]);
    }

    /**
     * @return string
     */
    public function actionUserList()
    {
        $query = CarnageUser::find();

        $searchModel = new CarnageUser();
        $request = \Yii::$app->request;
        $searchModel->load($request->get());


        $query->andFilterWhere(['like', 'username', $searchModel->username]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date_update' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('user/list', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    public function actionUserView($id)
    {
        $carnageUser = CarnageUser::find()->andWhere(['id' => $id])->one();

        $query = CarnageRatingValue::find()
            ->leftJoin(['cr' => CarnageRating::tableName()], "cr.id=carnage_rating_id")
            ->andWhere(['carnage_user_id' => $carnageUser->id ?? null])
            ->andWhere(['>', 'carnage_rating_value.place', 0])
            ->groupBy(['carnage_rating_id'])
            ->select([
                'title'             => 'cr.title',
                'place'             => 'max(carnage_rating_value.place)',
                'date_update'       => 'max(carnage_rating_value.date_update)',
                'carnage_rating_id' => 'carnage_rating_value.carnage_rating_id',
            ])
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);


        return $this->render('user/view', [
            'carnageUser'  => $carnageUser,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Проверка версии дополнения
     * @param string $version
     *
     * @return array
     */
    public function actionVersionExtension($version)
    {
        $lastVersion = 'v0.1';

        $actual = $version === $lastVersion;

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'actual' => $actual,
        ];
    }

    /**
     * Страница для скачивания расширения
     * @return string
     */
    public function actionExtension()
    {
        return $this->render('extension', []);
    }
}
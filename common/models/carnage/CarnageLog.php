<?php

namespace common\models\carnage;

use yii\base\BaseObject;
use yii\db\ActiveRecord;

/**
 * Class CarnageLog
 * @package common\models\carnage
 *
 * @property int    $id
 * @property string $url
 * @property string $city
 * @property int    $log_id
 * @property string $status
 * @property string $type
 * @property int    $start_date
 * @property int    $date_update
 * @property int    $date_create
 */
class CarnageLog extends ActiveRecord
{

    const STATUS_DONE = 'done';
    const STATUS_NEW = 'new';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'carnage_log';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['date_create', 'date_update'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['date_update'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_update', 'date_create', 'log_id', 'start_date'], 'integer'],
            [['url', 'city', 'status', 'type'], 'string'],
            [['status'], 'default', 'value' => CarnageLog::STATUS_DONE],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'url'         => 'Ссылка на лог',
            'city'        => 'Город лога',
            'log_id'      => 'Id лога',
            'status'      => 'Статус лога',
            'type'        => 'Тип боя',
            'start_date'  => 'Дата боя',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

    /**
     * @param $url
     *
     * @return array|false
     */
    public static function validateLogUrl($url)
    {
        $parseUrl = parse_url($url);
        $parseHost = explode('.', $parseUrl['host'] ?? '');
        if (count($parseHost) !== 3) {
            return false;
        }
        parse_str($parseUrl['query'] ?? '', $parseQuery);
        if (!isset($parseQuery['id'])) {
            return false;
        }
        $city = $parseHost[0];
        $id = $parseQuery['id'];
        return ['city' => $city, 'log_id' => $id];
    }

    public static function parseUrls($text, $city)
    {
        preg_match_all('!/log.pl\?id=(.*?)"!si', $text, $idLogs);
        $urls = [];
        foreach ($idLogs[1] ?? [] as $idLogItem) {
            $id = intval($idLogItem);
            if (!empty($id)) {
                $url = "http://{$city}.carnage.ru/log.pl?id=$id";
                $urls[] = $url;
            }
        }
        return $urls;
    }

    public static function addLogStatusDraft($url)
    {
        $logInfo = self::validateLogUrl($url);
        if ($logInfo === false) {
            return false;
        }
        $carnageLog = CarnageLog::find()->andWhere(['city' => $logInfo['city'], 'log_id' => $logInfo['log_id']])->one();
        if ($carnageLog) {
            return false;
        }

        $urlStats = "http://{$logInfo['city']}.carnage.ru/log.pl?cmd=stats&id={$logInfo['log_id']}";

        $carnageLog = new CarnageLog();
        $carnageLog->city = $logInfo['city'];
        $carnageLog->log_id = intval($logInfo['log_id']);
        $carnageLog->url = $urlStats;
        $carnageLog->status = CarnageLog::STATUS_NEW;
        $carnageLog->save();

        return $carnageLog->save();
    }

    public static function loadLog($url, $addFlash = true)
    {
        if ($url instanceof CarnageLog) {
            $carnageLog = $url;
            $urlStats = $carnageLog->url;
        } else {
            $logInfo = self::validateLogUrl($url);
            if ($logInfo === false) {
                if ($addFlash) {
                    \Yii::$app->session->addFlash('error', 'Некорректная ссылка лога');
                }
                return false;
            }
            $carnageLog = CarnageLog::find()->andWhere(['city' => $logInfo['city'], 'log_id' => $logInfo['log_id']])->one();
            if ($carnageLog) {
                if ($addFlash) {
                    \Yii::$app->session->addFlash('error', 'Лог уже загружен');
                }
                return false;
            }

            $urlStats = "http://{$logInfo['city']}.carnage.ru/log.pl?cmd=stats&id={$logInfo['log_id']}";

            $carnageLog = new CarnageLog();
            $carnageLog->city = $logInfo['city'];
            $carnageLog->log_id = intval($logInfo['log_id']);
            $carnageLog->url = $urlStats;
        }

        $htmlFight = file_get_contents($urlStats);
        $htmlFight = iconv('cp1251', 'utf-8', $htmlFight);
        $blockHtmlFight = explode('Статистика блоков', $htmlFight);
        if (count($blockHtmlFight) < 2) {
            if ($addFlash) {
                \Yii::$app->session->addFlash('error', 'Произошла ошибка в чтении лога. Попробуйте позже');
            }
            return false;
        }

        $transaction = \Yii::$app->db->beginTransaction();

        $attackStat = array_shift($blockHtmlFight);
        $attackTrStat = explode('nick-col', $attackStat);
        $trCount = 0;
        $userStatsAll = [];
        foreach ($attackTrStat as $trStat) {
            $trCount++;
            if ($trCount === 1) {
                $startDateInfo = explode('Начало боя: ', $trStat);
                $typeFightInfo = explode('Тип боя: ', $startDateInfo[0]);
                // Начало боя
                $startDateInfo = $startDateInfo[1] ?? '';
                preg_match('!<b>(.*?)</b><br />!si', $startDateInfo, $startDateFight);
                $startDateFight = $startDateFight[1] ?? '';
                if (!empty($startDateFight)) {
                    $carnageLog->start_date = strtotime($startDateFight);
                }
                // Тип боя
                $typeFightInfo = $typeFightInfo[1] ?? '';
                preg_match('!<b>(.*?)</b>!si', $typeFightInfo, $typeFight);
                $typeFight = $typeFight[1] ?? '';
                if (!empty($typeFight)) {
                    $carnageLog->type = $typeFight;
                }

                continue;
            }
            preg_match('!u\(\'(.*?)\'!si', $trStat, $arr);
            $username = $arr[1] ?? null;
            if (empty($username)) {
                preg_match('!wr\(\'(.*?)\'!si', $trStat, $arr);
                if (isset($arr[1])) {
                    preg_match('!>(.*?)<!si', $arr[1], $arr);
                    $username = $arr[1] ?? null;
                }
            }

            if (!empty($username)) {
                if (!isset($userStatsAll[$username])) {
                    $userStatsAll[$username] = [
                        'username'    => $username,
                        'a1'          => 0,
                        'a2'          => 0,
                        'a3'          => 0,
                        'a4'          => 0,
                        'b1'          => 0,
                        'b2'          => 0,
                        'b3'          => 0,
                        'b4'          => 0,
                        'count'       => 0,
                        'countAttack' => 0,
                    ];
                }
                $userStatsAll[$username]['count'] += 1;
                $rowStat = explode('row', $trStat);
                $rowCount = -1;
                foreach ($rowStat as $row) {
                    $rowCount++;
                    if ($rowCount === 0) {
                        continue;
                    }
                    preg_match('!\(\d+,\'(.*?)\',!si', $row, $arrA);
                    if (isset($arrA[1]) && $rowCount >= 1 && $rowCount <= 4) {
                        $userStatsAll[$username]["a{$rowCount}"] += intval($arrA[1]);
                        $userStatsAll[$username]["countAttack"] += intval($arrA[1]);
                    }
                }
            }
        }


        $blockStat = array_shift($blockHtmlFight);
        $blockTrStat = explode('nick-col', $blockStat);
        $trCount = 0;
        foreach ($blockTrStat as $trStat) {
            $trCount++;
            if ($trCount === 1) {
                continue;
            }
            preg_match('!u\(\'(.*?)\'!si', $trStat, $arr);
            $username = $arr[1] ?? null;
            if (empty($username)) {
                preg_match('!wr\(\'(.*?)\'!si', $trStat, $arr);
                if (isset($arr[1])) {
                    preg_match('!>(.*?)<!si', $arr[1], $arr);
                    $username = $arr[1] ?? null;
                }
            }
            if (!empty($username)) {
                if (!isset($userStatsAll[$username])) {
                    continue;
                }
                $rowStat = explode('row', $trStat);
                $rowCount = -1;
                foreach ($rowStat as $row) {
                    $rowCount++;
                    if ($rowCount === 0) {
                        continue;
                    }
                    preg_match('!\(\d+,\'(.*?)\',!si', $row, $arrB);
                    if (isset($arrB[1]) && $rowCount >= 1 && $rowCount <= 4) {
                        $userStatsAll[$username]["b{$rowCount}"] += intval($arrB[1]);
                    }
                }
            }
        }

        $countSaveUsers = 0;
        foreach ($userStatsAll as $username => $userStat) {
            $carnageUser = CarnageUser::find()->andWhere(['username' => $username])->one();
            if (empty($carnageUser)) {
                $carnageUser = new CarnageUser();
                $carnageUser->username = $username;
            }
            if (mb_strpos($carnageLog->type, 'Бой с монстром') !== false) {
                $carnageUser->ma1 += $userStat['a1'] ?? 0;
                $carnageUser->ma2 += $userStat['a2'] ?? 0;
                $carnageUser->ma3 += $userStat['a3'] ?? 0;
                $carnageUser->ma4 += $userStat['a4'] ?? 0;
                $carnageUser->mb1 += $userStat['b1'] ?? 0;
                $carnageUser->mb2 += $userStat['b2'] ?? 0;
                $carnageUser->mb3 += $userStat['b3'] ?? 0;
                $carnageUser->mb4 += $userStat['b4'] ?? 0;
                $carnageUser->mcount_attacks += $userStat['countAttack'] ?? 0;
            } else {
                $carnageUser->a1 += $userStat['a1'] ?? 0;
                $carnageUser->a2 += $userStat['a2'] ?? 0;
                $carnageUser->a3 += $userStat['a3'] ?? 0;
                $carnageUser->a4 += $userStat['a4'] ?? 0;
                $carnageUser->b1 += $userStat['b1'] ?? 0;
                $carnageUser->b2 += $userStat['b2'] ?? 0;
                $carnageUser->b3 += $userStat['b3'] ?? 0;
                $carnageUser->b4 += $userStat['b4'] ?? 0;
                $carnageUser->count_attacks += $userStat['countAttack'] ?? 0;
            }
            $carnageUser->count_fights += 1;
            if ($carnageUser->save()) {
                $countSaveUsers++;
            }
        }
        $resultSave = false;
        if ($countSaveUsers > 0) {
            $carnageLog->status = CarnageLog::STATUS_DONE;
            $resultSave = $carnageLog->save();
        }
        if ($resultSave) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }

        return $transaction;
    }
}
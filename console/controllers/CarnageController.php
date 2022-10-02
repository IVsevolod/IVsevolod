<?php

namespace console\controllers;

use common\components\SimpleHtmlDom;
use common\models\carnage\CarnageRating;
use common\models\carnage\CarnageRatingValue;
use common\models\carnage\CarnageUser;
use yii\console\Controller;
use yii\db\Expression;

class CarnageController extends Controller
{

    public function actionLoadRating()
    {
        foreach (CarnageRating::find()->each() as $carnageRating) {
            /** @var CarnageRating $carnageRating */
            $ratingHtml = SimpleHtmlDom::file_get_html($carnageRating->url);
//            $carnageRating->html_rating = utf8_encode($ratingHtml->getDoc()); // Что-то слишком большой, не хочет сохранять
            $carnageRating->touch('date_update');
            $carnageRating->save();

            $lastUserRatings= CarnageRatingValue::find()
                ->andWhere(['carnage_rating_id' => $carnageRating->id])
                ->leftJoin(['cu' => CarnageUser::tableName()], "cu.id=carnage_user_id")
                ->groupBy(['carnage_user_id'])
                ->select([
                    'nik'       => 'cu.username',
                    'value'     => new Expression("SUBSTRING_INDEX(GROUP_CONCAT(value ORDER BY carnage_rating_value.id DESC SEPARATOR '-'), '-', 1)"),
                    'place'     => new Expression("SUBSTRING_INDEX(GROUP_CONCAT(place ORDER BY carnage_rating_value.id DESC SEPARATOR '-'), '-', 1)"),
                    'clan_img'  => 'cu.clan_img',
                    'align_img' => 'cu.align_img',
                ])
                ->indexBy('nik')
                ->asArray()
                ->all()
            ;

            $usernameInList = [];
            $rows = $ratingHtml->find('table.table-list tbody tr');
            foreach ($rows as $rowTr) {
                $usernameTags = $rowTr->find('.nick b.name');
                $usernameTag = reset($usernameTags);
                $username = $usernameTag ? utf8_encode(strip_tags($usernameTag->plaintext)) : '';
                $username = iconv('utf-8//IGNORE', 'cp1252//IGNORE', $username);
                $username = iconv('cp1251//IGNORE', 'utf-8//IGNORE', $username);
                if (empty($username)) {
                    continue;
                }

                $alignTags = $rowTr->find('img.align');
                $alignTag = reset($alignTags);
                $align = $alignTag ? CarnageUser::transformUrl('align', $alignTag->src) : '';

                $clanImgTags = $rowTr->find('img.clan');
                $clanImgTag = reset($clanImgTags);
                $clanImg = $clanImgTag ? CarnageUser::transformUrl('clan', $clanImgTag->src) : '';

                $guildImgTags = $rowTr->find('img.guild');
                $guildImgTag = reset($guildImgTags);
                $guildImg = $guildImgTag ? CarnageUser::transformUrl('guild', $guildImgTag->src) : '';

                $placeTags = $rowTr->find('td.num');
                $placeTag = reset($placeTags);
                $place = $placeTag ? strip_tags($placeTag->plaintext) : '';

                switch ($carnageRating->type) {
                    case 'base_expa_today':
                    default:
                        $valueTags = $rowTr->find('td.value');
                        $valueTag = reset($valueTags);
                        $value = $valueTag ? strip_tags($valueTag->plaintext) : '';
                }

                if ($username && $value && $place) {
                    $carnageUser = CarnageUser::getOrCreateUser($username, ['align_img' => $align, 'clan_img' => $clanImg, 'guild_img' => $guildImg]);
                    if ($carnageUser instanceof CarnageUser) {
                        if ($carnageUser->align_img != $align || $carnageUser->clan_img != $clanImg || $carnageUser->guild_img != $guildImg) {
                            $carnageUser->align_img = $align;
                            $carnageUser->clan_img = $clanImg;
                            $carnageUser->guild_img = $guildImg;
                            $carnageUser->save();
                        }

                        $lastUserRating = $lastUserRatings[$username] ?? [];
                        $lastValue = floatval($lastUserRating['value'] ?? 0);
                        $lastPlace = intval($lastUserRating['place'] ?? 0);
                        $usernameInList[$username] = $username;

                        if (empty($lastValue) || empty($lastPlace) || $lastValue != floatval($value) || $lastPlace != intval($place)) {
                            $newCarnageRatingValue = new CarnageRatingValue([
                                'carnage_rating_id' => $carnageRating->id,
                                'carnage_user_id'   => $carnageUser->id,
                                'value'             => $value,
                                'place'             => $place,
                            ]);
                            $newCarnageRatingValue->save();
                        }
                    }
                }
            }
            foreach ($lastUserRatings as $username => $userRatingData) {
                $carnageUser = CarnageUser::getOrCreateUser($username);
                if (!($carnageUser instanceof CarnageUser)) {
                    continue;
                }
                if (!isset($usernameInList[$username]) && !empty($userRatingData['value'])) {
                    // Если в новом рейтинге его нет, но раньше был
                    $newCarnageRatingValue = new CarnageRatingValue([
                        'carnage_rating_id' => $carnageRating->id,
                        'carnage_user_id'   => $carnageUser->id,
                        'value'             => 0,
                        'place'             => 0,
                    ]);
                    if (!$newCarnageRatingValue->save()) {
                        var_dump($newCarnageRatingValue->getErrors());
                    }
                }
            }

        }
    }

}
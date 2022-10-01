<?php
/**
 * @var \yii\web\View      $this
 * @var ActiveDataProvider $dataProvider
 * @var CarnageRating      $carnageRating
 * @var CarnageRatingValue $searchModel
 */

use common\models\carnage\CarnageRating;
use common\models\carnage\CarnageRatingValue;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;


?>
<div>
    <?= \yii\helpers\Html::a('← Посмотреть все статистики', ['games/carnage/rating-list'], ['class' => 'btn btn-default']); ?>
    <h1>Статистика <?= \yii\helpers\Html::a($carnageRating->title, $carnageRating->url, ['target' => 'blank']); ?></h1>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            'nik' => [
                'attribute' => 'nik',
                'format' => 'raw',
                'value' => function ($carnageRatingValue) {
                    /** @var CarnageRatingValue $carnageRatingValue */
                    $returnStr = [];
                    if (!empty($carnageRatingValue->alignImg)) {
                        $returnStr[] = \yii\helpers\Html::img($carnageRatingValue->alignImg);
                    }
                    if (!empty($carnageRatingValue->clanImg)) {
                        $returnStr[] = \yii\helpers\Html::img($carnageRatingValue->clanImg);
                    }
                    if (!empty($carnageRatingValue->guildImg)) {
                        $returnStr[] = \yii\helpers\Html::img($carnageRatingValue->guildImg);
                    }

                    $nik = $carnageRatingValue->nik;
                    $returnStr[] = \yii\helpers\Html::a($nik, [
                        'games/carnage/rating-view',
                        'ratingId' => $carnageRatingValue->carnage_rating_id,
                        'userId' => $carnageRatingValue->carnage_user_id
                    ]);


                    return join(' ', $returnStr);
                }
            ],
            'place',
            'value',
            'date_update' => [
                'attribute' => 'date_update',
                'filter'    => false,
                'format'    => ['date', 'php:d.m.Y H:i:s']
            ],
        ],
    ]);
    ?>
</div>

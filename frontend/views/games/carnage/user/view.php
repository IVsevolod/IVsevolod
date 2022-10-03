<?php
/**
 * @var \yii\web\View      $this
 * @var CarnageUser        $carnageUser
 * @var ActiveDataProvider $dataProvider
 */

use common\models\carnage\CarnageRating;
use common\models\carnage\CarnageUser;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;

$userLabels = [];
if (!empty($carnageUser->align_img)) {
    $userLabels[] = \yii\helpers\Html::img($carnageUser->align_img);
}
if (!empty($carnageUser->clan_img)) {
    $userLabels[] = \yii\helpers\Html::img($carnageUser->clan_img);
}
if (!empty($carnageUser->guild_img)) {
    $userLabels[] = \yii\helpers\Html::img($carnageUser->guild_img);
}
$userLabels[] = $carnageUser->username;
$userLabel = join(' ', $userLabels);
?>
<div>
    <?= \yii\helpers\Html::a('← Все игроки', ['games/carnage/user-list'], ['class' => 'btn btn-default']); ?>
    <h1>Профиль игрока: <?= $userLabel; ?></h1>
    <h2>Последние активности игрока</h2>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'title' => [
                'attribute' => 'title',
                'format'    => 'raw',
                'value'     => function ($carnageRatingValue) use ($carnageUser) {
                    /** @var \common\models\carnage\CarnageRatingValue $carnageRatingValue */
                    return \yii\helpers\Html::a($carnageRatingValue->title, [
                        'games/carnage/rating-view',
                        'ratingId' => $carnageRatingValue->carnage_rating_id,
                        'userId'   => $carnageUser->id
                    ]);
                }
            ],
            'place' => [
                'attribute' => 'place',
                'label'     => 'Последнее место в рейтинге'
            ],
            'date_update' => [
                'attribute' => 'date_update',
                'label'     => 'Последняя дата в рейтинге',
                'format'    => ['date', 'php:d.m.Y H:i:s']
            ],
        ],
    ]);
    ?>
</div>

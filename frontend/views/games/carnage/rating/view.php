<?php
/**
 * @var \yii\web\View      $this
 * @var ActiveDataProvider $dataProvider
 * @var CarnageRating      $carnageRating
 * @var CarnageUser        $carnageUser
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
    <?= \yii\helpers\Html::a('← Посмотреть статистику ' . $carnageRating->title, ['games/carnage/rating-index', 'id' => $carnageRating->id], ['class' => 'btn btn-default']); ?>
    <h1>Статистика <?= \yii\helpers\Html::a($carnageRating->title, $carnageRating->url, ['target' => 'blank']); ?> игрока</h1>
    <p><?= $userLabel; ?></p>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'place',
            'value',
            'date_update' => [
                'attribute' => 'date_update',
                'format'    => ['date', 'php:d.m.Y H:i:s']
            ],
        ],
    ]);
    ?>
</div>

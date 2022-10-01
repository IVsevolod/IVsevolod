<?php
/**
 * @var \yii\web\View $this
 * @var ActiveDataProvider $dataProvider
 */

use yii\data\ActiveDataProvider;
use yii\grid\GridView;


?>
<div>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'title' => [
                'attribute' => 'title',
                'format'    => 'raw',
                'value'     => function ($carnageRating) {
                    return \yii\helpers\Html::a($carnageRating->title, ['games/carnage/rating-index', 'id' => $carnageRating->id]);
                }
            ],
            'url',
            'date_update' => [
                'attribute' => 'date_update',
                'format'    => ['date', 'php:d.m.Y H:i:s']
            ],
        ],
    ]);
    ?>
</div>

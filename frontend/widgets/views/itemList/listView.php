<?php
/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $dataProvider
 */

use yii\widgets\ListView;

echo ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView'     => '_listItemView',
    'summary'      => '',
    'pager' => [
        'class' => \common\components\LinkPager::class,
    ]
]);
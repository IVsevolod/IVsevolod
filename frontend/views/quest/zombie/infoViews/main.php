<?php
/**
 * @var \yii\web\View                   $this
 * @var \common\models\quest\PartyQuest $quest
 */

use yii\helpers\Html;

$infoArr = [];

function addInfo(&$infoArr, $key, $label) {
    $infoArr[] = [
        'key'   => $key,
        'label' => $label,
    ];
    return $infoArr;
};


//addInfo($infoArr, 'Здоровье: ', $quest->getMoodLabel());


foreach ($infoArr ?? [] as $item) {
    echo Html::tag(
        'div',
        Html::tag('span', $item['key'])
        . Html::tag('span', $item['label'], ['class' => 'quest-select-text'])
    );
}
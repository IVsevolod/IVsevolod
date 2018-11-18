<?php
/**
 * @var \yii\web\View                    $this
 * @var \common\models\quest\ZombieQuest $quest
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


addInfo($infoArr, 'Здоровье: ', $quest->health . ' %');
addInfo($infoArr, 'Голод: ', $quest->hunger . ' %');


foreach ($infoArr ?? [] as $item) {
    echo Html::tag(
        'div',
        Html::tag('span', $item['key'])
        . Html::tag('span', $item['label'], ['class' => 'quest-select-text'])
    );
}


$objects = $quest->getObjectsByLocation(\common\models\quest\ZombieQuest::OBJECT_LOCATION_SELF);
if (count($objects) > 0) {
    echo "<div>Предметы</div>";
    foreach ($objects ?? [] as $object) {
        echo Html::tag('div', Html::tag('span', $object['title'], ['class' => 'quest-select-text']));
    }
}
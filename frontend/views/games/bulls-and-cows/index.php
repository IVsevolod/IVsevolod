<?php
/**
 * @var \yii\web\View $this
 * @var string        $oldValue
 * @var int $bulls
 * @var int $cows
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

?>

<div id="item-header">
    <h1>Игра Быки и Коровы</h1>
</div>
<div>
    <?= Html::a('Начать игру', ['games/bulls-and-cows/start'], ['class' => 'btn btn-success']); ?>
</div>
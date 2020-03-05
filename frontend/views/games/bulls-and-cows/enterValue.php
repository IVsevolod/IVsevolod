<?php
/**
 * @var \yii\web\View $this
 * @var string        $oldValue
 * @var int           $bulls
 * @var int           $cows
 * @var string        $alias
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

?>

<div id="item-header">
    <h1>Игра Быки и Коровы</h1>
</div>
<div>
    <div>
        <?php if ($oldValue !== false) { ?>
            <span>Введено число: <b><?= $oldValue; ?></b></span><br>
            <?php if (isset($bulls) || isset($cows)) { ?>
                <span>Быков: <b><?= $bulls; ?></b></span><br>
                <span>Коров: <b><?= $cows; ?></b></span><br>
            <?php } ?>
        <?php } ?>

    </div>

    <?php $form = ActiveForm::begin(['action' => ['games/bulls-and-cows/enter-value', ['alias' => $alias]], 'id' => 'bullsForm']); ?>

    <div class="input-group margin-bottom">
        <span class="input-group-addon">Введите число</span>
        <?= Html::input('text', 'value', '', ['class' => 'form-control']); ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'list-add-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
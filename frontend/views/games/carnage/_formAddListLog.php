<?php
/**
 * @var \yii\web\View $this
 */

use yii\helpers\Html;

echo \yii\helpers\Html::beginForm('index', 'POST');
?>
    <div class="input-group margin-bottom">
        <span class="input-group-addon">Добавить лог в статистику боёв</span>
        <?= Html::dropDownList('city', 'lutecia', [
            'lutecia' => 'Лютеция',
            'kitezh' => 'Китеж',
            'arkaim' => 'Ар Каим',
            'helios' => 'Гелиос',
        ], [
            'class' => 'form-control'
        ]); ?>
        <?= Html::textarea('listUrl', '', ['class' => 'form-control', 'autocomplete' => 'off']); ?>
        <span class="input-group-btn">
            <button class="btn btn-default">Добавить</button>
        </span>
    </div>
<?php
echo \yii\helpers\Html::endForm();
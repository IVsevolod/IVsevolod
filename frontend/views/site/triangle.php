<?php
/**
 * @var \yii\web\View $this
 * @var string        $result
 * @var int           $count
 * @var float         $a
 * @var float         $b
 * @var float         $c
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Площадь треугольника';
?>
<div class="container">
    <h1>Вычисление площади треугольника</h1>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">По формуле Герона</h3>
                </div>
                <div class="panel-body">
                    <img src="/img/triangle_1.png">
                    <img src="/img/triangle_2.png">
                    <hr>
                    <?php
                    $form = ActiveForm::begin(['method' => 'POST']);

                    echo Html::tag('label', 'Сторона треугольника a');
                    echo Html::input('text', 'triangleA', $a, ['class' => 'form-control']);

                    echo Html::tag('label', 'Сторона треугольника b');
                    echo Html::input('number', 'triangleB', $b, ['class' => 'form-control', 'step' => 0.00001]);

                    echo Html::tag('label', 'Сторона треугольника a');
                    echo Html::input('number', 'triangleC', $c, ['class' => 'form-control', 'step' => 0.00001]);

                    echo "<br/>";

                    echo Html::submitButton('Вычислить', ['class' => 'btn btn-default']);
                    ActiveForm::end();
                    echo Html::tag('i', 'Количество вычислений: ' . $count);
                    echo "<br/>";

                    echo Html::tag('label', 'Результат');
                    echo Html::input('text', 'result', $result, ['class' => 'form-control']);
                    ?>
                    <br/>
                    Источник: <?= Html::a('Формула Герона — Википедия', 'https://translate.google.com/'); ?><br/>
                    <?= Html::a('Помощь', ''); ?>
                </div>
            </div>
        </div>
    </div>
</div>
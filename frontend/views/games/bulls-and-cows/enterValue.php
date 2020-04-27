<?php
/**
 * @var \yii\web\View $this
 * @var int           $bulls
 * @var int           $cows
 * @var BullsAndCows  $bullsAndCows
 * @var string        $alias
 * @var bool          $bullsAndCowsUserExists
 */

use common\models\BullsAndCows;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;


$errorMessage = Yii::$app->session->getFlash('error');
?>

<div id="item-header">
    <h1>Игра Быки и Коровы</h1>
</div>
<div>
    <?php
    if ($bullsAndCowsUserExists) {
        echo Html::tag('div', 'Вы угадали число <b>' . $bullsAndCows->number . '</b>', ['class' => 'alert-success']);
    } else {
        ?>
        <p>Необходимо угадать число из <?= $bullsAndCows->length; ?> цифр.</p>
        <?php $form = ActiveForm::begin(['action' => ['games/bulls-and-cows/enter-value', 'alias' => $bullsAndCows->alias]]); ?>

        <div class="input-group margin-bottom">
            <span class="input-group-addon">Введите число</span>
            <?= Html::input('text', 'value', '', ['class' => 'form-control', 'autocomplete' => 'off']); ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'list-add-button']) ?>
        </div>

        <?php
        ActiveForm::end();
    }
    ?>

    <div>
        <?php
        $count = $bullsAndCows->getBacUsers()->count();
        if ($count > 0) {
            echo Html::tag('div', '<h4>Попыток: ' . $count . '</h4>');
            echo Html::tag('div', '<h4>Введённые значения:</h4>');
            echo Html::beginTag('ol');
            foreach ($bullsAndCows->bacUsers as $bacUser) {
            echo Html::tag('li',
                    Html::tag('b', Html::encode($bacUser->number))
                    . " (Быков <b>{$bacUser->bulls}</b>, Коров <b>{$bacUser->cows}</b>)"
                );
            }
            echo Html::endTag('ol');
        }
        ?>
    </div>
</div>
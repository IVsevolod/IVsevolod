<?php
/**
 * @var \yii\web\View $this
 * @var \common\models\Vkpost $vkpost
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div>

    <div class="row">
        <div class="col-lg-9">
            <?php $form = ActiveForm::begin(['id' => 'list-add-form']); ?>

            <?= $form->field($vkpost, 'text')->textarea()->label('История/Мысль/Юмор')?>
            <div class="form-group">
                <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary', 'name' => 'list-add-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
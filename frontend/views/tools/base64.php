<?php
/**
 * @var \yii\web\View $this
 * @var string        $type
 * @var string        $value
 * @var string        $result
 */

?>
<div class="tools-timestamp">
    <div class="row">
        <?= \yii\helpers\Html::beginForm(['tools/base64']); ?>
        <div class="col-md-12  margin-bottom">
            <label>Введите значение</label>
            <?= \yii\helpers\Html::textarea('inputText', $value, ['class' => 'form-control']); ?>
        </div>
        <div class="col-md-12 margin-bottom">
            <?= \yii\helpers\Html::submitButton('BASE64 в текст', ['class' => 'btn btn-default btn-success', 'value' => 'fromBase64', 'name' => 'type']); ?>
            <?= \yii\helpers\Html::submitButton('текст в BASE64', ['class' => 'btn btn-default btn-primary', 'value' => 'toBase64', 'name' => 'type']); ?>
        </div>
        <div class="col-md-12">
            <?php
            if (!empty($value) && !empty($type)) {
                if ($type === 'toBase64') {
                    echo \yii\helpers\Html::tag('label', 'текст в BASE64');
                } else {
                    echo \yii\helpers\Html::tag('label', 'BASE64 в текст');
                }
            }
            ?>
            <?= \yii\helpers\Html::textarea('resultText', $result, ['class' => 'form-control']); ?>
        </div>
        <?= \yii\helpers\Html::endForm(); ?>

        <div class="col-md-12">
            <hr>
            <?= $this->render('_listTools', ['active' => 'base64']); ?>
        </div>
    </div>
</div>

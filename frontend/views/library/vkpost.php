<?php
/**
 * @var \yii\web\View         $this
 * @var \common\models\Vkpost $vkPost
 */

$this->title = 'IVsevolod - Библиотека интересностей';
?>
<div class="col-sm-6">
    <div class="well">
        <?= $vkPost->text; ?>
    </div>
</div>
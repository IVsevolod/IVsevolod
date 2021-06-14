<?php
/**
 * @var \yii\web\View $this
 * @var \common\models\carnage\CarnageUser $carnageUser
 */

use yii\helpers\Html;

echo Html::beginForm('stat', 'GET');
?>
    <div class="input-group margin-bottom">
        <span class="input-group-addon">Посмотреть статистику боя</span>
        <?= Html::input('text', 'username', $carnageUser->username ?? '', ['class' => 'form-control', 'autocomplete' => 'off']); ?>
        <span class="input-group-btn">
            <button class="btn btn-default">Найти</button>
        </span>
    </div>
<?php
echo Html::endForm();
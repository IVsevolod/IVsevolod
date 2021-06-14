<?php
/**
 * @var \yii\web\View $this
 */

use yii\helpers\Html;

?>
<div id="item-header">
    <h1>Для игры <a href="http://r.carnage.ru/?1016096774">carnage.ru</a></h1>
</div>

<div>
    <?= $this->render('_formAddLog'); ?>
</div>

<div>
    <?= $this->render('_formFindUser'); ?>
</div>
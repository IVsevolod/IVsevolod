<?php
/**
 * @var \yii\web\View $this
 * @var array $trees
 * @var array $actionPath
 * @var int $selectedId
 */

use yii\helpers\Html;

if (!Yii::$app->user->isGuest) {
    if (Yii::$app->user->identity->username == 'IVsevolod') {
        echo Html::a('Добавить раздел', ['tree/add'], ['class' => 'btn btn-primary margin-bottom']);
    }
}

?>

<div>
    <?= $this->render('item', [
        'trees'      => $trees,
        'actionPath' => $actionPath,
        'selectedId' => $selectedId,
    ]);
    ?>
</div>

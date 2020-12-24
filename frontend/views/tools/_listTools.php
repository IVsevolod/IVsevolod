<?php
/**
 * @var \yii\web\View $this
 * @var string        $active
 */
$active = $active ?? '';
?>

<div class="list-group">
    <h3>Все инструменты</h3>
    <?php
    echo \yii\helpers\Html::a('Преобразовать Timestamp, дату и время', ['tools/timestamp'], ['class' => "list-group-item " . ($active == 'timestamp' ? 'active' : '')]);

    ?>
</div>

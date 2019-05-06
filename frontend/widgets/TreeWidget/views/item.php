<?php
/**
 * @var \yii\web\View $this
 * @var array $trees
 * @var array $actionPath
 * @var int $selectedId
 */

use yii\helpers\Html;

/** @var \common\models\TreeItem $item */
$item = $trees['item'];

$action = $actionPath;
if (empty($item)) {
    return '';
}
$action['id'] = $item->id;
?>

<b><?php
    if ($selectedId == $item->id) {
        echo Html::tag('b', $item->title);
    } else {
        echo Html::a($item->title, $action);
    }
?></b>
<ul>
    <?php
    foreach ($trees['child'] ?? [] as $child) {
        echo "<li>";
        echo $this->render('item', [
            'trees'      => $child,
            'actionPath' => $actionPath,
            'selectedId' => $selectedId,
        ]);
        echo "</li>";
    }
    ?>
</ul>
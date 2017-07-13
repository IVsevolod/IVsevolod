<?php
/**
 * @var yii\web\View $this
 * @var int $selectedId
 * @var array $list
 */
use frontend\widgets\ItemList;
use yii\helpers\Html;


$sort = new \yii\data\Sort([
    'attributes' => [
        'id',
        'like_count',
        'show_count',
    ],
]);
$orders = $sort->orders;
$sortColumn = array_keys($orders);
$sortColumn = reset($sortColumn);
$sortDirection = reset($orders);
?>
<div class="col-sm-3">
    <?php
    if (!Yii::$app->user->isGuest) {
        if (Yii::$app->user->identity->username == 'IVsevolod') {
            echo Html::a('Добавить книгу', ['library/add'], ['class' => 'btn btn-default margin-bottom']);
        }
    }
    echo \frontend\widgets\TreeWidget\TreeWidget::widget(['mainItemId' => Yii::$app->params['mainLibraryId'], 'actionPath' => ['library/index'], 'selectedId' => $selectedId]);
    ?>
</div>
<div class="col-sm-9">
    Сортировка:
    <div class="btn-group" role="group" aria-label="">
        <?php
        if ($sortDirection == SORT_DESC) {
            echo Html::a(
                'По дате создания ' . ($sortColumn == 'id' ? Html::tag('span', '', ['class' => 'glyphicon glyphicon-sort-by-attributes-alt']) : ''),
                ['library/index', 'id' => $selectedId, 'sort' => 'id'],
                ['class' => 'btn btn-default']
            );
        } else {
            echo Html::a(
                'По дате создания ' . ($sortColumn == 'id' ? Html::tag('span', '', ['class' => 'glyphicon glyphicon-sort-by-attributes']) : ''),
                ['library/index', 'id' => $selectedId, 'sort' => '-id'],
                ['class' => 'btn btn-default']
            );
        }
        ?>
        <?php
        if ($sortDirection == SORT_DESC) {
            echo Html::a(
                'По читаемости ' . ($sortColumn == 'show_count' ? Html::tag('span', '', ['class' => 'glyphicon glyphicon-sort-by-attributes-alt']) : ''),
                ['library/index', 'id' => $selectedId, 'sort' => 'show_count'],
                ['class' => 'btn btn-default']
            );
        } else {
            echo Html::a(
                'По читаемости ' . ($sortColumn == 'show_count' ? Html::tag('span', '', ['class' => 'glyphicon glyphicon-sort-by-attributes']) : ''),
                ['library/index', 'id' => $selectedId, 'sort' => '-show_count'],
                ['class' => 'btn btn-default']
            );
        }
        ?>
    </div>
    <?php

    echo ItemList::widget([
        'action'    => 'listview',
        'orderBy'   => null,
        'searchTag' => $list,
        'tagEntity' => \common\models\TagEntity::ENTITY_LIBRARY,
        'tagGroup'  => \common\models\Tags::TAG_GROUP_LIBRARY_TREE,
        'entity'    => \common\models\Item::ENTITY_TYPE_LIBRARY,
    ]);
    ?>
</div>

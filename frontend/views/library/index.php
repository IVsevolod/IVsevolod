<?php
/**
 * @var yii\web\View $this
 * @var int $selectedId
 * @var array $list
 */
use frontend\widgets\ItemList;
use yii\helpers\Html;


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
    <?php

    echo ItemList::widget([
        'orderBy'   => ItemList::ORDER_BY_ID,
        'searchTag' => $list,
        'tagEntity' => \common\models\TagEntity::ENTITY_LIBRARY,
        'tagGroup'  => \common\models\Tags::TAG_GROUP_LIBRARY_TREE,
        'entity'    => \common\models\Item::ENTITY_TYPE_LIBRARY,
    ]);
    ?>
</div>

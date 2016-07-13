<?php


use frontend\widgets\ItemList;

$keywords = 'Личный, блог, программиста';
$description = 'Личный блог Всеволода Иванов.';

$this->registerMetaTag([
    'name'    => 'keywords',
    'content' => $keywords,
], 'keywords');

$this->registerMetaTag([
    'name'    => 'description',
    'content' => $description,
], 'description');

echo $this->render('/list/tabs', ['selectTab' => 4, 'searchTag' => $searchTag]);
?>
<div class="site-index">
    <div class="body-content">

        <?= ItemList::widget(['orderBy' => ItemList::ORDER_BY_LIKE_SHOW, 'dateCreateType' => ItemList::DATE_CREATE_ALL, 'searchTag' => $searchTag]) ?>

    </div>
</div>
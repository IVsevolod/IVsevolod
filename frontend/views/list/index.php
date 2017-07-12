<?php
/**
 * @var yii\web\View $this
 * @var string $searchTag
 */

use frontend\models\Lang;
use frontend\widgets\ItemList;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = 'Блог';

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

echo $this->render('/list/tabs', ['selectTab' => 1, 'searchTag' => $searchTag]);
?>
<div class="site-index">
    <div class="body-content">

        <?= ItemList::widget([
            'action'    => 'listview',
            'orderBy'   => ItemList::ORDER_BY_ID,
            'searchTag' => $searchTag,
        ]) ?>

    </div>
</div>

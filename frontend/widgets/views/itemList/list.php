<?php
/**
 * @var Item[] $items
 * @var bool   $onlyItem
 * @var string $dateCreateType
 * @var string $searchTag
 * @var string $display
 */

use common\models\Item;
use frontend\models\Lang;
use frontend\widgets\ItemList;
use frontend\widgets\ModalDialogsWidget;
use yii\bootstrap\Html;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/list.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
    <div id="blockList">
        <?php
        foreach ($items as $item) {
            if ($display == ItemList::ITEM_LIST_DISPLAY_MAIN) {
                echo $this->render('view', ['item' => $item, 'dateCreateType' => $dateCreateType]);
            } else if ($display == ItemList::ITEM_LIST_DISPLAY_MINI) {
                echo $this->render('viewMini', ['item' => $item, 'dateCreateType' => $dateCreateType]);
            }
        }
        ?>
    </div>

<?php
if (!$onlyItem) {
    if ($dateCreateType == ItemList::DATE_CREATE_LAST) {
        if (count($items) >= 10) {
            echo Html::button('Показать еще', ['class' => 'btn btn-primary', 'id' => 'loadMore', 'data-tag' => $searchTag]);
        }
    }
}
?>
<?php
/**
 * @var int    $selectTab
 * @var string $searchTag
 */
use common\models\User;
use frontend\models\Lang;
use frontend\widgets\ItemList;
use yii\helpers\Html;
use yii\helpers\Url;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/findTagElement.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

Yii::$app->params['jsZoukVar']['selectedTab'] = $selectTab;

$urls = [1 => '/', 3 => 'list/' . ItemList::DATE_CREATE_MONTH, 4 => 'list/' . ItemList::DATE_CREATE_ALL];
$urlNoTag = Url::to([$urls[$selectTab]]);
?>
<?php
    if (!Yii::$app->user->isGuest) {
        if (Yii::$app->user->identity->username == 'IVsevolod') {
            echo Html::a('Добавить запись', ['lists/add'], ['class' => 'btn btn-primary margin-bottom']);
        }
    }
?>
<div>
    <ul class="nav nav-tabs nav-main-tabs">
        <li class="<?= $selectTab == 1 ? 'active ' : '' ?>"><?= Html::a('Все записи', ['lists/index']) ?></li>
        <li class="<?= $selectTab == 4 ? 'active ' : '' ?>"><?= Html::a('Лучшие', ['lists/popular']) ?></li>
    </ul>
    <?php if (!empty($searchTag)) { ?>
        <br/>
        <div class="">
            Поиск по тегу: <span class="label label-tag-element"><?= $searchTag ?></span> <span class="icon-x" data-href="<?= $urlNoTag ?>">&times;</span>
        </div>
    <?php } ?>
</div>

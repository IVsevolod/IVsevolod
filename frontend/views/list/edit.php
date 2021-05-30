<?php
/**
 * @var yii\web\View        $this
 * @var Item $item
 */

use common\models\Img;
use common\models\Item;
use common\models\Music;
use common\models\Tags;
use common\models\Video;
use frontend\models\Lang;
use frontend\widgets\ModalDialogsWidget;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use frontend\widgets\SoundWidget;


\frontend\assets\ClEditorAsset::register($this);
// Tags
$this->registerJsFile(Yii::$app->request->baseUrl . '/component/bootstrap-tokenfield/bootstrap-tokenfield.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile(Yii::$app->request->baseUrl . '/component/bootstrap-tokenfield/bootstrap-tokenfield.min.css');
// Sortable
$this->registerJsFile('//code.jquery.com/ui/1.11.4/jquery-ui.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css');


$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/videoEdit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/soundEdit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/imgEdit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/edit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = 'Редактирование';

$this->params['breadcrumbs'][] = [
    'label' => 'Записи',
    'url' => ['lists/index'],
];
$this->params['breadcrumbs'][] = $this->title;

$tags = $item->tagEntity;
$tagValues = [];
foreach ($tags as $tag) {
    /** @var Tags $tagItem */
    $tagItem = $tag->tags;
    $tagValues[] = $tagItem->getName();
}
$tagValue = join(',', $tagValues);

$thisUser = \common\models\User::thisUser();
?>
<div id="item-header">
    <h1><?= Html::encode($this->title) ?></h1>
</div>
<div>

    <div class="row">
        <div class="col-lg-9">
            <?php $form = ActiveForm::begin(['id' => 'list-edit-form']); ?>

            <?= $form->field($item, 'title')->label('Заголовок') ?>

            <?= $form->field($item, 'source_url')->label('Ресурс') ?>
            
            <?= $form->field($item, 'description')->textarea()->label('Описание') ?>

            <div class="input-group margin-bottom">
                <span class="input-group-addon" id="basic-addon1">Метки</span>
                <?= Html::textInput('tags', $tagValue, array('id' => 'tokenfield', 'data-tokens' => $tagValue, 'class' => 'form-control')) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary', 'name' => 'list-add-button']) ?>
                <?= Html::a('Отмена', $item->getUrl(), ['class' => 'btn btn-default pull-right']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
    
</div>

<?php
/**
 * @var yii\web\View        $this
 * @var \common\models\Item $item
 */

use common\models\Img;
use common\models\Item;
use common\models\Music;
use frontend\widgets\ModalDialogsWidget;
use frontend\widgets\SoundWidget;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

\frontend\assets\ClEditorAsset::register($this);
// Tags
$this->registerJsFile(Yii::$app->request->baseUrl . '/component/bootstrap-tokenfield/bootstrap-tokenfield.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile(Yii::$app->request->baseUrl . '/component/bootstrap-tokenfield/bootstrap-tokenfield.min.css');
// Sortable
$this->registerJsFile('//code.jquery.com/ui/1.11.4/jquery-ui.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css');

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/add.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = 'Добавление записи';

$this->params['breadcrumbs'][] = $this->title;

$thisUser = \common\models\User::thisUser();
?>
<div id="item-header">
    <h1><?= Html::encode($this->title) ?></h1>
</div>
<div>

    <div class="row">
        <div class="col-lg-9">
            <?php $form = ActiveForm::begin(['id' => 'list-add-form']); ?>

            <?= $form->field($item, 'title')->label('Заголовок') ?>

            <?= $form->field($item, 'source_url')->label('Ресурс') ?>

            <?= $form->field($item, 'description')->textarea()->label('Описание') ?>

            <div class="input-group margin-bottom">
                <span class="input-group-addon" id="basic-addon1">Метки</span>
                <?= Html::textInput('tags', '', array('id' => 'tokenfield', 'data-tokens' => '', 'class' => 'form-control')) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary', 'name' => 'list-add-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

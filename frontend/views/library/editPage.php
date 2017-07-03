<?php
/**
 * @var yii\web\View            $this
 * @var \common\models\ItemPage $item
 */

use common\models\Img;
use common\models\Item;
use common\models\Music;
use frontend\widgets\ModalDialogsWidget;
use frontend\widgets\SoundWidget;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

// tinymce
$this->registerJsFile('//cdn.tinymce.com/4/tinymce.min.js');
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/tinymcLang/ru-RU.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
// Tags
$this->registerJsFile(Yii::$app->request->baseUrl . '/component/bootstrap-tokenfield/bootstrap-tokenfield.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile(Yii::$app->request->baseUrl . '/component/bootstrap-tokenfield/bootstrap-tokenfield.min.css');
// Sortable
$this->registerJsFile('//code.jquery.com/ui/1.11.4/jquery-ui.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css');

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/library/addPage.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = 'Добавление страницы';

$this->params['breadcrumbs'][] = $this->title;

$thisUser = \common\models\User::thisUser();
?>
<div id="item-header">
    <h1><?= Html::encode($this->title) ?></h1>
</div>
<div>

    <div class="row">
        <div class="col-lg-9">
            <?php $form = ActiveForm::begin(['id' => 'list-add-page-form']); ?>

            <?= $form->field($item, 'page')->label('Страница') ?>

            <?= $form->field($item, 'description')->textarea()->label('Описание') ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'list-add-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

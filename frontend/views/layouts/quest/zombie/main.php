<?php

/**
 * @var $this \yii\web\View
 * @var $content string
 */

use common\models\form\SearchEntryForm;
use frontend\assets\quest\QuestAsset;
use yii\helpers\Html;
use yii\web\View;

QuestAsset::register($this);

$var = isset(Yii::$app->params['jsZoukVar']) ? Yii::$app->params['jsZoukVar'] : [];
$this->registerJs("var jsZoukVar = " . json_encode($var) . ";", View::POS_HEAD);

$year = date('Y');
$month = date('m');
$thisPage = isset(Yii::$app->controller->thisPage) ? Yii::$app->controller->thisPage : 'main';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name='wmail-verification' content='9cbe0259acf0d32f3a48fc0051ac3f9f' />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="icon" type="image/png" href="<?= '/img/v.png' ?>">
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <div class="container">
        <?= $content ?>
    </div>
</div>

<footer class="footer ">
    <div class="container">
        <div class="pull-left">&copy; IVsevolod <?= date('Y') ?></div>
        <?php if (YII_DEBUG) { ?>
            <div class="pull-right" style="margin-right: 10px">
                <?php
                echo $this->render('../../banners');
                ?>
            </div>
        <?php } ?>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

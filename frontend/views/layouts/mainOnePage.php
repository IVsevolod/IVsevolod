<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\web\View;

\frontend\assets\AppPageAsset::register($this);
$var = isset(Yii::$app->params['jsVar']) ? Yii::$app->params['jsVar'] : [];
$this->registerJs("var jsVar = " . json_encode($var) . ";", View::POS_HEAD);

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
    <link rel="icon" type="image/png" href="<?= '/img/v.png' ?>">
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<?php
/**
 * @var \yii\web\View $this
 */

use common\models\carnage\CarnageLog;

$carnageLogNewCount = CarnageLog::find()->andWhere(['status' => CarnageLog::STATUS_NEW])->count();
$carnageLogAllCount = CarnageLog::find()->count();

?>
<div id="item-header">
    <h1>Для игры <a href="http://r.carnage.ru/?1016096774">carnage.ru</a></h1>
</div>

<div>
    В обработке <b><?= $carnageLogNewCount; ?></b>. Всего обработано логов <b><?= $carnageLogAllCount; ?></b>
</div>
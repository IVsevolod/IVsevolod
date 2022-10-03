<?php
/**
 * @var \yii\web\View      $this
 * @var ActiveDataProvider $dataProvider
 * @var CarnageUser        $searchModel
 */

use common\models\carnage\CarnageUser;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;


?>
<div>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            'id',
            'username' => [
                'attribute' => 'username',
                'format' => 'raw',
                'value' => function ($carnageUser) {
                    /** @var CarnageUser $carnageUser */
                    $returnStr = [];
                    if (!empty($carnageUser->align_img)) {
                        $returnStr[] = \yii\helpers\Html::img($carnageUser->align_img);
                    }
                    if (!empty($carnageUser->clan_img)) {
                        $returnStr[] = \yii\helpers\Html::img($carnageUser->clan_img);
                    }
                    if (!empty($carnageUser->guild_img)) {
                        $returnStr[] = \yii\helpers\Html::img($carnageUser->guild_img);
                    }

                    $nik = $carnageUser->username;
                    $returnStr[] = \yii\helpers\Html::a($nik, [
                        'games/carnage/user-view',
                        'id' => $carnageUser->id
                    ]);

                    return join(' ', $returnStr);
                }
            ],
        ],
    ]);
    ?>
</div>

<?php
/**
 * @var \yii\web\View                      $this
 * @var \common\models\carnage\CarnageUser $carnageUser
 */

?>

<div id="item-header">
    <h1>Для игры <a href="http://r.carnage.ru/?1016096774">carnage.ru</a></h1>
</div>

<div>
    <?= $this->render('_formAddLog'); ?>
</div>

<div>
    <?= $this->render('_formFindUser', ['carnageUser' => $carnageUser]); ?>
</div>

<?php
if ($carnageUser) {
    $ca = $carnageUser->count_attacks;
    $a1 = $carnageUser->a1 ?: 0;
    $a2 = $carnageUser->a2 ?: 0;
    $a3 = $carnageUser->a3 ?: 0;
    $a4 = $carnageUser->a4 ?: 0;
    $b1 = $carnageUser->b1 ?: 0;
    $b2 = $carnageUser->b2 ?: 0;
    $b3 = $carnageUser->b3 ?: 0;
    $b4 = $carnageUser->b4 ?: 0;
    $mca = $carnageUser->mcount_attacks + $ca;
    $ma1 = $carnageUser->ma1 + $a1;
    $ma2 = $carnageUser->ma2 + $a2;
    $ma3 = $carnageUser->ma3 + $a3;
    $ma4 = $carnageUser->ma4 + $a4;
    $mb1 = $carnageUser->mb1 + $b1;
    $mb2 = $carnageUser->mb2 + $b2;
    $mb3 = $carnageUser->mb3 + $b3;
    $mb4 = $carnageUser->mb4 + $b4;
    ?>
    <div>
        <h4>Персонаж <b><?= $carnageUser->username; ?></b></h4>
        <span>Обработано боёв: <?= $carnageUser->count_fights; ?></span>
        <table class="table text-center">
            <tr>
                <td>&nbsp;</td>
                <td colspan="2">Чаще блокирует</td>
                <td colspan="2">Чаще атакует</td>
            </tr>
            <tr>
                <td>Голова</td>
                <td><?= $a1; ?> (<?= $ma1; ?>)</td>
                <td>
                    <?= round($ca > 0 ? 100 * $a1 / $ca : 0, 2); ?>%
                    (<?= round($mca > 0 ? 100 * $ma1 / $mca : 0, 2); ?>%)
                </td>
                <td><?= $b1; ?> (<?= $mb1; ?>)</td>
                <td>
                    <?= round($ca > 0 ? 100 * $b1 / $ca : 0, 2); ?>%
                    (<?= round($mca > 0 ? 100 * $mb1 / $mca : 0, 2); ?>%)
                </td>
            </tr>
            <tr>
                <td>Корпус</td>
                <td><?= $a2; ?> (<?= $ma2; ?>)</td>
                <td>
                    <?= round($ca > 0 ? 100 * $a2 / $ca : 0, 2); ?>%
                    (<?= round($mca > 0 ? 100 * $ma2 / $mca : 0, 2); ?>%)
                </td>
                <td><?= $b2; ?> (<?= $mb2; ?>)</td>
                <td>
                    <?= round($ca > 0 ? 100 * $b2 / $ca : 0, 2); ?>%
                    (<?= round($mca > 0 ? 100 * $mb2 / $mca : 0, 2); ?>%)
                </td>
            </tr>
            <tr>
                <td>Пояс</td>
                <td><?= $a3; ?> (<?= $ma3; ?>)</td>
                <td>
                    <?= round($ca > 0 ? 100 * $a3 / $ca : 0, 2); ?>%
                    (<?= round($mca > 0 ? 100 * $ma3 / $mca : 0, 2); ?>%)
                </td>
                <td><?= $b3; ?> (<?= $mb3; ?>)</td>
                <td>
                    <?= round($ca > 0 ? 100 * $b3 / $ca : 0, 2); ?>%
                    (<?= round($mca > 0 ? 100 * $mb3 / $mca : 0, 2); ?>%)
                </td>
            </tr>
            <tr>
                <td>Ноги</td>
                <td><?= $a4; ?> (<?= $ma4; ?>)</td>
                <td>
                    <?= round($ca > 0 ? 100 * $a4 / $ca : 0, 2); ?>%
                    (<?= round($mca > 0 ? 100 * $ma4 / $mca : 0, 2); ?>%)
                </td>
                <td><?= $b4; ?> (<?= $mb4; ?>)</td>
                <td>
                    <?= round($ca > 0 ? 100 * $b4 / $ca : 0, 2); ?>%
                    (<?= round($mca > 0 ? 100 * $mb4 / $mca : 0, 2); ?>%)
                </td>
            </tr>
        </table>
    </div>
    <?php
}
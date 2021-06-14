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
                <td><?= $carnageUser->a1; ?></td>
                <td><?= round($carnageUser->count_attacks > 0 ? 100 * $carnageUser->a1 / $carnageUser->count_attacks : 0, 2); ?>%</td>
                <td><?= $carnageUser->b1; ?></td>
                <td><?= round($carnageUser->count_attacks > 0 ? 100 * $carnageUser->b1 / $carnageUser->count_attacks : 0, 2); ?>%</td>
            </tr>
            <tr>
                <td>Корпус</td>
                <td><?= $carnageUser->a2; ?></td>
                <td><?= round($carnageUser->count_attacks > 0 ? 100 * $carnageUser->a2 / $carnageUser->count_attacks : 0, 2); ?>%</td>
                <td><?= $carnageUser->b2; ?></td>
                <td><?= round($carnageUser->count_attacks > 0 ? 100 * $carnageUser->b2 / $carnageUser->count_attacks : 0, 2); ?>%</td>
            </tr>
            <tr>
                <td>Пояс</td>
                <td><?= $carnageUser->a3; ?></td>
                <td><?= round($carnageUser->count_attacks > 0 ? 100 * $carnageUser->a3 / $carnageUser->count_attacks : 0, 2); ?>%</td>
                <td><?= $carnageUser->b3; ?></td>
                <td><?= round($carnageUser->count_attacks > 0 ? 100 * $carnageUser->b3 / $carnageUser->count_attacks : 0, 2); ?>%</td>
            </tr>
            <tr>
                <td>Ноги</td>
                <td><?= $carnageUser->a4; ?></td>
                <td><?= round($carnageUser->count_attacks > 0 ? 100 * $carnageUser->a4 / $carnageUser->count_attacks : 0, 2); ?>%</td>
                <td><?= $carnageUser->b4; ?></td>
                <td><?= round($carnageUser->count_attacks > 0 ? 100 * $carnageUser->b4 / $carnageUser->count_attacks : 0, 2); ?>%</td>
            </tr>
        </table>
    </div>
    <?php
}
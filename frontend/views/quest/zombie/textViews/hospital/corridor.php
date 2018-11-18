<?php
/**
 * @var \yii\web\View                    $this
 * @var \common\models\quest\ZombieQuest $quest
 */

if ($quest->hospitalWarpFlag['corridorLocation'] == 1) {
    $objects = $quest->getObjectsByLocation(\common\models\quest\ZombieQuest::FRAME_HOSPITAL_CORRIDOR);
    $hasAxe = false;
    foreach ($objects ?? [] as $object) {
        if ($object['id'] == 1) {
            $hasAxe = true;
        }
    }
} elseif ($quest->hospitalWarpFlag['corridorLocation'] == 2) {
    // Находим все предметы в руках
    $objects = $quest->getObjectsByLocation(\common\models\quest\ZombieQuest::OBJECT_LOCATION_SELF);
    // Наличие разрушающего оружия, например топор
    $hasBreakingWeapon = false;
    foreach ($objects ?? [] as $object) {
        if ($object['type'] == \common\models\quest\ZombieQuest::OBJECT_TYPE_BREAKING_WEAPON) {
            $hasBreakingWeapon = true;
        }
    }
}

?>
<div>
    <p>
        Хотя на улице и светло, кородор в полутьме, освещение не работает. Только в аварийном режиме мигает лампа "Выход".
        Противоположный конец коридора вообще погружен в темноту.
    </p>
    <?php
    if ($quest->hospitalWarpFlag['corridorLocation'] == 2) {
        ?>
        <i>
            Ладно хоть зомби не видать, похоже, меня действительно от них закрыли. Той барикадой из досок, что закрывает дверь
            под лампой "Выход". А мне-то как выйти????
        </i>
        <?php
    } elseif ($quest->hospitalWarpFlag['corridorLocation'] == 1 && $hasAxe) {
        // Топор лежит на месте, его можно подобрать
        // todo: Изменить текст
        ?>
        <p>
            На стене висит пожарный топор.
        </p>
        <?php
    }
    ?>

    <ul class="">
        <?php
        if ($quest->hospitalWarpFlag['corridorLocation'] != 1) {
            ?>
            <li>
                <a href="javascript: void(0);" data-action="dark" class="js--quest-action">Идти в темный конец коридора</a>
            </li>
            <?php
        }
        if ($quest->hospitalWarpFlag['corridorLocation'] == 2) {
            if ($hasBreakingWeapon) {
                ?>
                <li>
                    <a href="javascript: void(0);" data-action="crash" class="js--quest-action">Разбить барикаду топором</a>
                </li>
                <?php
            } elseif (!$quest->hospitalWarpFlag['tryToBreak']) {
                ?>
                <li>
                    <a href="javascript: void(0);" data-action="bop" class="js--quest-action">Разбить барикаду</a>
                </li>
                <?php
            }
        }

        ?>
        <?php
        if ($quest->hospitalWarpFlag['corridorLocation'] != 2) {
            ?>
            <li>
                <a href="javascript: void(0);" data-action="barricade" class="js--quest-action">Идти к барикаде</a>
            </li>
            <?php
            foreach ($objects ?? [] as $object) {
                ?>
                <li>
                    <a href="javascript: void(0);" data-action="take-object" data-id="<?= $object['id']; ?>" class="js--quest-action">Взять <?= $object['title']; ?></a>
                </li>
                <?php
            }
        }
        ?>
    </ul>
</div>

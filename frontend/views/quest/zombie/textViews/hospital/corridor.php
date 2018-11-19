<?php
/**
 * @var \yii\web\View                    $this
 * @var ZombieQuest $quest
 */

//$barik = false; // посещали барикаду?

// если мы в темном конце коридора, то проверим, лежит ли там топор
use common\models\quest\ZombieQuest;

if ($quest->hospitalWarpFlag['corridorLocation'] == 1) {
    $objects = $quest->getObjectsByLocation(ZombieQuest::FRAME_HOSPITAL_CORRIDOR);
    $hasAxe = false;
    foreach ($objects ?? [] as $object) {
        if ($object['id'] == 1) {
            $hasAxe = true;
        }
    }
    // а если мы у баррикады
} elseif ($quest->hospitalWarpFlag['corridorLocation'] == 2) {
    // Находим все предметы в руках
    $objects = $quest->getObjectsByLocation(ZombieQuest::OBJECT_LOCATION_SELF);
    // Наличие разрушающего оружия, например топор
    $hasBreakingWeapon = false;
    foreach ($objects ?? [] as $object) {
        if ($object['type'] == ZombieQuest::OBJECT_TYPE_BREAKING_WEAPON) {
            $hasBreakingWeapon = true;
        }
    }
}

?>
<div>
    <p>
        Хотя на улице и светло, коридор в полутьме, освещение не работает. Только в аварийном режиме мигает лампа "Выход".
        Противоположный конец коридора вообще погружен в темноту.
        <br>
    </p>
    <?php
    // мы около баррикады
    if ($quest->hospitalWarpFlag['corridorLocation'] == 2) {
        ?>
        <i>
            Ладно хоть зомби не видно, похоже, меня действительно от них закрыли. Той баррикадой из досок, что закрывает дверь
            под лампой "Выход". А мне-то как выйти????
        </i>
        <?php

        // флаг, что хоть один раз посетили баррикаду


        // иначе пошли в темный конец коридора
    } elseif ($quest->hospitalWarpFlag['corridorLocation'] == 1 && $hasAxe) {
        // Топор лежит на месте, его можно подобрать

        ?>
        <p>
            Глаза постепенно привыкают к темноте, и мы видим на стене пожарный щит. Предметы на нём не тронуты, сюда
            ещё никто не добирался. На щите висит пожарный топор.
        </p>
        <?php
        if ($quest->getLocationCount(ZombieQuest::FRAME_HOSPITAL_CORRIDOR, ['barricade'])) {
            ?>
            <br><i>О, попробуем разбить ту баррикаду этим топором!</i>
            <?php
        }else {
            ?>
            <br><i>Возьмём, пригодится.</i>
            <?php
        }
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
                    <a href="javascript: void(0);" data-action="crash" class="js--quest-action">Разбить баррикаду топором</a>
                </li>
                <?php
            } elseif (!$quest->hospitalWarpFlag['tryToBreak']) {
                ?>
                <li>
                    <a href="javascript: void(0);" data-action="bop" class="js--quest-action">Попробовать разбить баррикаду</a>
                </li>
                <?php
            }
        }

        ?>
        <?php
        if ($quest->hospitalWarpFlag['corridorLocation'] != 2) {
            ?>
            <li>
                <a href="javascript: void(0);" data-action="barricade" class="js--quest-action">Идти к баррикаде</a>
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

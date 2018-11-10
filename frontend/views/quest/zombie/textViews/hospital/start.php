<?php
/**
 * @var \yii\web\View                    $this
 * @var \common\models\quest\ZombieQuest $quest
 */

$look = $quest->hospitalWarpFlag['look'] ?? false;
$read = $quest->hospitalWarpFlag['read'] ?? false;


?>
<div>
    <?php if (!$look && !$read) { ?>
        Только проснулся
    <?php } elseif ($look && !$read) { ?>
        Посмотрел телик
    <?php } else { ?>
        Почитал записку
    <?php } ?>

    <ul class="">
        <?php if (!$look && !$read) { ?>
        <li>
            <a href="javascript: void(0);" data-action="look" class="js--quest-action">Посмотреть телевизор</a>
        </li>
        <?php } elseif ($look && !$read) { ?>
            <li>
                <a href="javascript: void(0);" data-action="read" class="js--quest-action">Прочитать записку</a>
            </li>
        <?php } else { ?>
            <li>
                <a href="javascript: void(0);" data-action="goout" class="js--quest-action">Выйти в коридор</a>
            </li>
        <?php } ?>
    </ul>
</div>

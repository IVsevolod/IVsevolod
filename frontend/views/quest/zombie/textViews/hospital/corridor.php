<?php
/**
 * @var \yii\web\View                    $this
 * @var \common\models\quest\ZombieQuest $quest
 */
$axe = $quest->hospitalWarpFlag['axe'] ?? false;
?>
<div>
    Хотя на улице и светло, кородор в полутьме, освещение не работает. Только в аварийном режиме мигает лампа "Выход".
    Противоположный конец коридора вообще погружен в темноту.
    <i>Ладно хоть зомби не видать, похоже, меня действительно от них закрыли. Той барикадой из досок, что закрывает дверь
    под лампой "Выход". А мне-то как выйти????
    </i>

    <ul class="">
        <?php if (!$axe) { ?>
        <li>
            <a href="javascript: void(0);" data-action="axe" class="js--quest-action">Идти в темный конец коридора</a>
        </li>
            
        <li>
            <a href="javascript: void(0);" data-action="read" class="js--quest-action">Идти в барикаде</a>
        </li>
        <?php } ?>
    </ul>
</div>

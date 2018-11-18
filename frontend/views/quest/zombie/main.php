<?php
/**
 * @var \yii\web\View                    $this
 * @var \common\models\quest\ZombieQuest $quest
 */

use yii\helpers\Html;
use yii\helpers\Url;

$piece = $quest->getPiece();
$imageStr = 'front/zombie/imgs/' . ($piece['image'] ?? '');
$imageUrl = Url::to($imageStr);
?>
<div class="container">
    <?php \yii\widgets\Pjax::begin(); ?>
    <div class="row">
        <div class="col-sm-8 quest-block-left">
            <div class="block-loader hide">
                <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
            </div>
            <div class="quest-block-text quest-border">
                <?php
                echo Html::beginForm('', 'post', ['id' => 'questForm', 'data-pjax' => '']);
                echo Html::hiddenInput('action', '');
                if ($piece['textView'] ?? false) {
                    echo $this->render($piece['textView'], ['quest' => $quest]);
                } elseif ($piece['text'] ?? false) {
                    echo Html::tag('div', $piece['text'], $piece['options'] ?? []);
                }
                if (is_array($piece['buttons'])) {
                    ?>
                    <div class="quest-choice-action">
                        <?php
                        foreach ($piece['buttons'] ?? [] as $newStep => $label) {
                            echo Html::a($label, 'javascript: void(0);', [
                                'class'       => 'js--quest-action',
                                'data-action' => $newStep,
                            ]);
                        }
                        ?>
                    </div>
                    <?php
                }
                echo Html::endForm();
                ?>
                &nbsp;
                <span class="right-bottom-text">
                    <a href="javascript: void(0);" data-action="restart" class="js--quest-action" style="text-decoration: underline">Начать заново</a>
                </span>
            </div>
        </div>
        <div class="col-sm-4 quest-block-rigth">
            <div class="quest-block-image quest-border ">
                <?= Html::tag(
                    'div',
                    '&nbsp;',
                    [
                        'class' => 'quest-block-image-background',
                        'style' => "background-image: url($imageUrl);",
                    ]
                ); ?>
            </div>
            <div class="quest-block-info quest-border">
                <?php
                if ($piece['infoView'] ?? false) {
                    echo $this->render($piece['infoView'], ['quest' => $quest]);
                } elseif ($piece['info'] ?? false) {
                    echo Html::tag('div', $piece['info']);
                }
                ?>
                &nbsp;
            </div>
        </div>
    </div>
    <?php \yii\widgets\Pjax::end(); ?>
</div>
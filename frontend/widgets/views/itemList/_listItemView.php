<?php
/**
 * @var Item $model
 */


use common\models\Item;
use common\models\Tags;
use yii\helpers\Html;
use yii\helpers\Url;

$item = $model;
$url = $item->getUrl();
$tags = $item->tagEntity;
?>

<div id="item-<?= $item->id ?>" data-id="<?= $item->id ?>"
     class="row block-item-summary margin-bottom <?= $item->like_count < 0 ? 'bad-item' : '' ?>">
    <div class="col-sm-1 visible-sm-block visible-md-block visible-lg-block">
        <div class="cp tac" onclick="window.location.href='<?= $url ?>'">
            <div class="votes">
                <div class="mini-counts">
                    <?= Html::tag('span', $item->like_count, ['title' => $item->like_count]) ?>
                    <i class="glyphicon glyphicon-thumbs-up"></i>
                </div>
            </div>
            <div class="views">
                <div class="mini-counts">
                    <?= $item->show_count ?>
                    <i class="glyphicon glyphicon-eye-open"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-11">
        <div class="summary">
            <h3><?= Html::a($item->getTitle(), $url, ['class' => 'item-hyperlink']) ?></h3>
        </div>

        <?php
        if ($item->like_count >= 0) {
            ?>
            <div class="item-short-description">
                <?= $item->getShortDescription() ?>
            </div>
            <div class="margin-bottom">
                <?php
                foreach ($tags as $tag) {
                    /** @var Tags $tagItem */
                    $tagItem = $tag->tags;
                    $urlTag = Url::to(['/', 'tag' => $tagItem->getName()]);
                    echo Html::a($tagItem->getName(), $urlTag, ['class' => 'label label-tag-element']), " ";
                }
                ?>
            </div>
            <?php
        }
        ?>
        <?php
        $author = $item->user;
        ?>
        <div class="pull-right">
            <table>
                <tr>
                    <td>
                        <div class="mini-like-show  visible-sm-block visible-xs-block">
                            <?php
                            $likeTitle = $item->like_count . " ";
                            $showTitle = $item->show_count . " ";
                            ?>
                            <span title="<?= $likeTitle ?>"><i
                                    class="glyphicon glyphicon-thumbs-up"></i> <?= $item->like_count ?></span><br/>
                            <span title="<?= $showTitle ?>"><i
                                    class="glyphicon glyphicon-eye-open"></i> <?= $item->show_count ?></span>
                        </div>
                    </td>
                    <td>
                        <div class="pull-right user-info">
                            <div class="user-action-time">
                                <?= "Создан " . date("d.m.Y", $item->date_create) . " в " . date("H:i", $item->date_create) ?>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

    </div>
</div>

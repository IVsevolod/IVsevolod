<?php
/**
 * @var yii\web\View $this
 * @var Item $item
 * @var Vote $vote
 * @var ItemPage $itemPage
 * @var \yii\data\Pagination $pages
 */
use common\models\Comment;
use common\models\Item;
use common\models\ItemPage;
use common\models\Music;
use common\models\TagEntity;
use common\models\User;
use common\models\Vote;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/view.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/findTagElement.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/share42.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = $item->getTitle2();
$this->params['breadcrumbs'][] = [
    'label' => 'Книги',
    'url'   => ['library/index'],
];
$this->params['breadcrumbs'][] = $this->title;

$thisUser = User::thisUser();
$voteItem = !empty($thisUser) ? $thisUser->getVoteByEntity(Vote::ENTITY_ITEM, $item->id) : null;
$voteUpHtml = '<span class="glyphicon glyphicon-triangle-top"></span>';
$voteLeftHtml = '<span class="glyphicon glyphicon-triangle-left"></span>';
$voteDownHtml = '<span class="glyphicon glyphicon-triangle-bottom"></span>';
$voteRightHtml = '<span class="glyphicon glyphicon-triangle-right"></span>';
$urlUp = Url::to(['vote/add']);
$urlDown = Url::to(['vote/add']);

$divLikeClass = ['cp', 'vote-up-link'];
if (!empty($voteItem) && $voteItem->vote == Vote::VOTE_UP) {
    $divLikeClass[] = 'voted';
}
$divLikeClass = join(' ', $divLikeClass);

$divDislikeClass = ['cp', 'vote-down-link'];
if (!empty($voteItem) && $voteItem->vote == Vote::VOTE_DOWN) {
    $divDislikeClass[] = 'voted';
}
$divDislikeClass = join(' ', $divDislikeClass);

$url = $item->getUrl();
$tags = $item->tagEntity;

$description = $this->title;
$description .= ". " . $item->getShortDescription(100, '') . "..";
$urlVideo = '';
preg_match_all('/[^\W\d][\w]*/', $this->title, $wordArr);
$this->registerMetaTag([
    'name'    => 'keywords',
    'content' => join(', ', $item->getKeywords()),
], 'keywords');

$this->registerMetaTag([
    'name'    => 'description',
    'content' => $description,
], 'description');

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/library/viewPage.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

?>
<div id="item-header">
    <h1>
        <?= Html::a($item->getTitle(), $url, ['class' => 'item-hyperlink']) ?>
        <?php
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $item->user_id) {
            echo Html::a(
                'Изменить',
                Url::to(['library/edit', 'id' => $item->id]),
                ['class' => 'btn btn-success pull-right']
            );
        }
        ?>
        <?php
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $item->user_id) {
            echo Html::a(
                'Добавить страницу',
                Url::to(['library/add-page', 'id' => $item->id]),
                ['class' => 'btn btn-success pull-right', 'style' => 'margin-right: 10px;']
            );
        }
        ?>
    </h1>

</div>


<div class="row">
    <div class="col-sm-1 text-center vote-block visible-md-block visible-lg-block visible-sm-block">
        <div>
            <?= Html::tag("div", $voteUpHtml, ['data-href' => $urlUp, 'class' => $divLikeClass, 'data-id' => $item->id, 'data-vote' => Vote::VOTE_UP, 'data-entity' => Vote::ENTITY_ITEM]) ?>
        </div>
        <div>
            <span class="vote-count-item">
                <?= $item->like_count ?>
            </span>
        </div>
        <div>
            <?= Html::tag("div", $voteDownHtml, ['data-href' => $urlDown, 'class' => $divDislikeClass, 'data-id' => $item->id, 'data-vote' => Vote::VOTE_DOWN, 'data-entity' => Vote::ENTITY_ITEM]) ?>
        </div>
    </div>
    <div class="col-sm-1 text-center vote-block visible-xs-block">
        <?= Html::tag("i", $voteLeftHtml, ['data-href' => $urlDown, 'class' => $divDislikeClass, 'data-id' => $item->id, 'data-vote' => Vote::VOTE_DOWN, 'data-entity' => Vote::ENTITY_ITEM]) ?>
        <span class="vote-count-item">
            <?= $item->like_count ?>
        </span>
        <?= Html::tag("i", $voteRightHtml, ['data-href' => $urlUp, 'class' => $divLikeClass, 'data-id' => $item->id, 'data-vote' => Vote::VOTE_UP, 'data-entity' => Vote::ENTITY_ITEM]) ?>
    </div>


    <div class="col-sm-11 block-item-view">
        <?php
        if ($item->source_url != "") {
            ?>
            <div>
                <b>Источник</b>: <a href="<?= $item->source_url ?>" target="_blank"><?= $item->source_url ?></a>
            </div>
            <?php
        }
        ?>
        <div class="item-text">
            <?php
            echo HtmlPurifier::process($item->description, []);
            ?>
        </div>
        <div class="margin-bottom tag-line-height">
            <?php
            $tagValues = [];
            foreach ($tags as $tag) {
                $tagItem = $tag->tags;
                $urlTag = Url::to(['/', 'tag' => $tagItem->getName()]);
                echo Html::a($tagItem->getName(), $urlTag, ['class' => 'label label-tag-element']), " ";
            }
            ?>
        </div>
        <div>
            <?php
            if (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $item->user_id) {
                echo Html::button(
                    'Удалить',
                    [
                        'class'       => 'btn btn-link no-focus',
                        'data-toggle' => "modal",
                        'data-target' => ".modal-delete-confirm",

                    ]
                ), ' ';
            }
            if (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $item->user_id) {
                echo Html::a(
                    'Изменить',
                    Url::to(['library/edit', 'id' => $item->id]),
                    ['class' => 'btn btn-link no-focus']
                ), ' ';
            }
            echo Html::button(
                'Поделиться',
                [
                    'id'    => 'btnShare',
                    'class' => 'btn btn-link no-focus',
                ]
            ), ' ';
            /** @var User $author */
            $author = $item->user;
            ?>
            <div class="pull-right">
                <table>
                    <tr>
                        <td>
                            <div class="mini-like-show">
                                <?php
                                $likeTitle = $item->like_count;
                                $showTitle = $item->show_count;
                                ?>
                                <span title="<?= $likeTitle ?>"><i
                                        class="glyphicon glyphicon-thumbs-up"></i> <?= $item->like_count ?></span><br/>
                                <span title="<?= $showTitle ?>"><i
                                        class="glyphicon glyphicon-eye-open"></i> <?= $item->show_count ?></span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="share42init hide"></div>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-lg-7 col-md-8 col-sm-12 text-justify text-indent" id="pageText" data-page="<?= $pages->page; ?>">
        <?php
        echo HtmlPurifier::process($itemPage->description, []);
        ?>
    </div>

    <?php
    if (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $item->user_id) {
        ?>
        <div class="col-sm-12">
            <?php
            echo Html::a(
                'Изменить',
                Url::to(['library/edit-page', 'id' => $itemPage->id]),
                ['class' => 'btn btn-link no-focus']
            ), ' ';
            ?>
        </div>
        <?php
    }
    ?>
</div>
<?php
if ($pages->pageCount > 1) {
    echo LinkPager::widget([
        'pagination' => $pages,
    ]);
}
?>
<div class="row">
    <hr/>
    <div class="col-md-12">
        <div>
            <h3>Комментарии</h3>
            <div>
                <?= \frontend\widgets\CommentsWidget::widget(['entity' => Comment::ENTITY_ITEM, 'entity_id' => $item->id]); ?>
            </div>
        </div>

    </div>
</div>


<div class="modal fade modal-delete-confirm bs-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Удалить запись?</h4>
            </div>
            <div class="modal-body">
                Вы действительно хотите удалить запись?
            </div>
            <div class="modal-footer">
                <a href="<?= Url::to(['library/delete', 'id' => $item->id]) ?>" type="button"
                   class="btn btn-danger">Удалить</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена
                </button>
            </div>
        </div>
    </div>
</div>
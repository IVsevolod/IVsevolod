<?php
namespace frontend\widgets\TreeWidget;

use common\models\Comment;
use common\models\Item;
use common\models\TagEntity;
use common\models\Tags;
use common\models\TreeItem;
use common\models\User;
use common\models\Vote;
use yii\data\Pagination;

class TreeWidget extends \yii\bootstrap\Widget
{

    public $mainItemId = null;

    public $actionPath;

    public $selectedId;

    public function init()
    {
    }

    public function run()
    {
        $treeItem = TreeItem::findOne($this->mainItemId ?: []);
        if (empty($this->mainItemId)) {
            $treeItem = TreeItem::findOne([]);
        }


        return $this->render('index', [
            'trees'      => empty($treeItem) ? null :  $treeItem->getTree(),
            'actionPath' => $this->actionPath,
            'selectedId' => $this->selectedId,
        ]);
    }

}
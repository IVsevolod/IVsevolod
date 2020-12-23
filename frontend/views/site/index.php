<?php
/* @var $this yii\web\View */

use common\models\Vkpost;
use yii\db\Expression;

$this->registerCssFile('pe-icon-social/css/pe-icon-social.css');
$this->registerCssFile('pe-icon-social/css/helper.css');
$this->registerCssFile('pe-icon-social/css/social-style.css');

$this->title = 'IVsevolod';

$vkPostStory = Vkpost::find()
    ->where(['category' => 'story'])
    ->andWhere(['not', ['text' => '']])
    ->orderBy(new Expression('rand()'))
    ->limit(1)
    ->one();

$vkPostHappy = Vkpost::find()
    ->where(['category' => 'happy'])
    ->andWhere(['not', ['text' => '']])
    ->orderBy(new Expression('rand()'))
    ->limit(1)
    ->one();

$vkPostHumor = Vkpost::find()
    ->where(['category' => 'humor'])
    ->andWhere(['not', ['text' => '']])
    ->orderBy(new Expression('rand()'))
    ->limit(1)
    ->one();

?>
<div class="site-index">
    <div class="col-md-6">
        <?php if ($vkPostStory) { ?>
            <b><?= \yii\helpers\Html::a('История', ['library/story', 'id' => $vkPostStory->id], ['target' => '_blank']); ?></b>
            <div class="well">
                <?= $vkPostStory->text; ?>
            </div>
        <?php } ?>

        <?php if ($vkPostHappy) { ?>
            <b><?= \yii\helpers\Html::a('Мысли', ['library/happy', 'id' => $vkPostHappy->id], ['target' => '_blank']); ?></b>
            <div class="well">
                <?= $vkPostHappy->text; ?>
            </div>
        <?php } ?>

        <?php if ($vkPostHumor) { ?>
            <b>Немного <?= \yii\helpers\Html::a('юмора', ['library/humor', 'id' => $vkPostHumor->id], ['target' => '_blank']); ?></b>
            <div class="well">
                <?= $vkPostHumor->text; ?>
            </div>
        <?php } ?>

        <h3>Мои группы, каналы и другое:</h3>
        <ul class="contacts">
            <li>
                <a href="https://vk.com/prozouk">
                    <span class="pe-so-vk"></span> Группа Вконтакте ProZouk
                </a>
            </li>
            <li>
                <a href="https://vk.com/snakebattle">
                    <span class="pe-so-vk"></span> Группа Вконтакте битвы змей
                </a>
            </li>
            <li>
                <a href="https://www.youtube.com/channel/UCTDPXDsQqdMEmQ4aidSDomQ">
                    <span class="pe-so-youtube-1"></span> Канал в Youtube про танец Zouk
                </a>
            <li>
                Почта: <a href="mailto: ivsevolod@ivsevolod.ru">ivsevolod@ivsevolod.ru</a>
            </li>
        </ul>
    </div>
    <div class="col-md-6">
        <h3>Проекты</h3>
        <table class="projects-preview-block">
            <tr>
                <td>
                    <a href="http://battlesnake.ru/">
                        <img src="img/project/battlesnake.png">
                    </a>
                </td>
                <td>
                    <a href="http://battlesnake.ru/">battlesnake.ru</a> - <b>Битва змей.</b><br/>
                    <b>Дата создания:</b> 24.08.2013<br/>
                    <b>Развивал до:</b> 21.07.2016<br/>
                    <b>Framework:</b> Yii<br/>
                    В планах переписать, но это когда время будет...
                </td>
            </tr>
            <tr>
                <td>
                    <a href="http://tavanen.ru/">
                        <img src="img/project/tavan-en.png">
                    </a>
                </td>
                <td>
                    tavanen.ru - <b>Газета.</b><br/>
                    <b>Дата создания:</b> 02.07.2012<br/>
                    <b>Поддерживал до:</b> 01.01.2020<br/>
                    <b>CMS:</b> WP
                </td>
            </tr>
            <tr>
                <td>
                    <a href="http://prozouk.ru/">
                        <img src="img/project/prozouk.png">
                    </a>
                </td>
                <td>
                    <a href="http://prozouk.ru/">prozouk.ru</a> - <b>Портал танца Zouk.</b><br/>
                    <b>Дата создания:</b> 06.03.2016<br/>
                    <b>Framework:</b> Yii2
                </td>
            </tr>
        </table>
    </div>
</div>

<?php
/**
 * @var string $access_token
 * @var int $group_id
 * @var string $group_name
 * @var int $offset
 * @var int $limit
 * @var string $category
 * @var Vkpost[] $vkpostsSave
 */

use common\models\Vkpost;
use yii\helpers\Html;


echo Html::beginForm('', 'post');
echo Html::label('access_token. (' . Html::a('получить токен', 'https://oauth.vk.com/authorize?client_id=5553652&scope=groups,wall,offline,photos,docs&redirect_uri=https://oauth.vk.com/blank.html&display=page&v=5.21&response_type=token', ['target' => '_blank']) . ')' );
echo Html::textInput('access_token', $access_token, ['class' => 'form form-control']);

echo Html::label('ID группы');
echo Html::textInput('group_id', $group_id, ['class' => 'form form-control']);

echo Html::label('Имя группы');
echo Html::textInput('group_name', $group_name, ['class' => 'form form-control']);

echo Html::label('Сдвиг');
echo Html::textInput('offset', $offset, ['class' => 'form form-control']);

echo Html::label('Лимит');
echo Html::textInput('limit', $limit, ['class' => 'form-control']);

echo Html::label('Категория');
echo Html::textInput('category', $category, ['class' => 'form-control']);

echo "<br/>";
echo Html::submitInput('Загрузить', ['class' => 'btn btn-success']);

echo Html::endForm();

if (!empty($vkpostsSave)) {
    echo "<h2>Добавлено</h2>";
    foreach ($vkpostsSave as $vkpost) {
        echo $vkpost->post_id . " - " . mb_substr($vkpost->text, 0, 100) . "<br/>";
    }
}
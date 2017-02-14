<?php
/**
 * @var string $access_token
 * @var int $group_id
 * @var string $category
 * @var string $date
 * @var int $count
 * @var int $interval
 */

use yii\helpers\Html;

echo Html::beginForm('', 'post');

echo Html::label('access_token. (' . Html::a('получить токен', 'https://oauth.vk.com/authorize?client_id=5553652&scope=groups,wall,offline,photos&redirect_uri=https://oauth.vk.com/blank.html&display=page&v=5.21&response_type=token', ['target' => '_blank']) . ')');
echo Html::textInput('access_token', $access_token, ['class' => 'form form-control']);

echo Html::label('ID группы');
echo Html::textInput('group_id', $group_id, ['class' => 'form form-control']);

echo Html::label('Категория');
echo Html::textInput('category', $category, ['class' => 'form-control']);

echo Html::label('Дата публикации');
echo Html::textInput('date', $date, ['class' => 'form-control']);

echo Html::label('Количество публикаций');
echo Html::textInput('count', $count, ['class' => 'form-control']);

echo Html::label('Время между публикациями');
echo Html::textInput('interval', $interval, ['class' => 'form-control']);

echo "<br/>";
echo Html::submitInput('Загрузить', ['class' => 'btn btn-success']);

echo Html::endForm();
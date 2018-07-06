<div style="height: 200px;">
    <form action="http://www.mice-award.ru/vote.php" target="_blank" method="post">
        <input type="hidden" name="nomination" value="37">
        <input type="hidden" name="nominie" value="1100">
        <input type="submit" value="Голосовать">
    </form>
</div>

<div>
    <?php
    $models = common\models\Item::find()->all();
    foreach ($models as $model) {
        ?>
        <a href="<?= $model->getUrl(); ?>"><?= $model->title; ?></a><br/>
        <?php
    }
    ?>
    <a href="http://prozouk.ru">ProZouk</a><br/>
    <a href="http://prozouk.ru/ru/list/popular">ProZouk - записи популярные</a><br/>
    <a href="http://prozouk.ru/ru/list/index">ProZouk -записи новые</a><br/>
    <a href="http://prozouk.ru/ru/events/all">ProZouk - события все</a><br/>
    <a href="http://prozouk.ru/ru/events/after">ProZouk - события предстоящие</a><br/>
    <a href="http://prozouk.ru/ru/events/before">ProZouk - события прошедшие</a><br/>
    <a href="http://prozouk.ru/ru/schools/all">ProZouk - школы</a><br/>
    <a href="http://prozouk.ru/ru/site/about">ProZouk - about</a><br/>
    <a href="http://prozouk.ru/ru/site/contact">ProZouk - обратная связь</a><br/>


</div>

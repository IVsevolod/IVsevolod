<?php
$filename = Yii::getAlias('@app/runtime/tmp/aero.json');

$fp = fopen($filename, 'r');
$statistics = fread($fp, filesize($filename));
fclose($fp);

$statistics = json_decode($statistics, true);
$date = date('Y-m-d');
$userIp = $_SERVER['HTTP_X_REAL_IP'] ?? Yii::$app->request->userIP;
$echoVoteForm = false;
if (!isset($statistics[$date])) {
    $statistics = [];
    $statistics[$date] = [];
}
if (count($statistics[$date]) < 100 && false) {
    // Если голосовавших сегодня меньше 100
    if (!isset($statistics[$date][$userIp])) {
        // Ещё не голосовали
        $statistics[$date][$userIp] = 1;
        $echoVoteForm = true;
    } else {
        // Уже голосовали
        $statistics[$date][$userIp]++;
    }
}

$statistics = json_encode($statistics);
$fp = fopen($filename, 'w+');
fwrite($fp, $statistics);
fclose($fp);

?>


<?php if ($echoVoteForm === true) { ?>
<div style="height: 200px;">
    <form action="http://www.mice-award.ru/vote.php?nomination=37&nominie=1592" <?= Yii::$app->request->get('n', false) ? 'target="_blank"' : ''; ?> method="post">
        <input type="hidden" name="nomination" value="37">
        <input type="hidden" name="nominie" value="1592">
        <input type="submit" value="Голосовать">
    </form>
</div>
<?php } ?>

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

<?php if ($echoVoteForm === true) { ?>
<script>
    function start() {
        $(function() {
            $('input[type="submit"]').click();
        });
    }
    var timeout = <?= Yii::$app->request->get('t') ?? 3000; ?>;
    setTimeout('start()', timeout);
</script>
<?php } ?>
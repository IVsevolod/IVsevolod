<?php
/**
 * @var \yii\web\View $this
 * @var int           $timestampEnter
 */

$dateUtc = new \DateTime("now", new \DateTimeZone("UTC"));
?>

<div class="tools-timestamp">
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Текущий Unix Timestamp</h3>
                </div>
                <div class="panel-body">
                    <b><?= $dateUtc->getTimestamp(); ?></b> <i>секунд с 1 января 1970 г. (UTC)</i>
                    <hr/>
                    <dl>
                        <dt class="col-sm-3">ISO8601</dt>
                        <dd class="col-sm-9"><?= $dateUtc->format(\DateTime::ISO8601); ?></dd>
                        <dt class="col-sm-3">RFC822</dt>
                        <dd class="col-sm-9"><?= $dateUtc->format(\DateTime::RFC822); ?></dd>
                        <dt class="col-sm-3">RFC2822</dt>
                        <dd class="col-sm-9"><?= $dateUtc->format(\DateTime::RFC2822); ?></dd>
                        <dt class="col-sm-3">RFC3339</dt>
                        <dd class="col-sm-9"><?= $dateUtc->format(\DateTime::RFC3339); ?></dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <?php
            if (!empty($timestampEnter)) {
                $dateUtcEnter = new \DateTime("now", new \DateTimeZone("UTC"));
                $dateUtcEnter->setTimestamp($timestampEnter)
                ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Преобразованный Timestamp</h3>
                    </div>
                    <div class="panel-body">
                        <b><?= $dateUtcEnter->getTimestamp(); ?></b> <i>секунд с 1 января 1970 г. (UTC)</i><br/>
                        <b><?= $dateUtcEnter->format("d.m.Y H:i:s"); ?></b>
                        <hr/>
                        <dl>
                            <dt class="col-sm-3">ISO8601</dt>
                            <dd class="col-sm-9"><?= $dateUtcEnter->format(\DateTime::ISO8601); ?></dd>
                            <dt class="col-sm-3">RFC822</dt>
                            <dd class="col-sm-9"><?= $dateUtcEnter->format(\DateTime::RFC822); ?></dd>
                            <dt class="col-sm-3">RFC2822</dt>
                            <dd class="col-sm-9"><?= $dateUtcEnter->format(\DateTime::RFC2822); ?></dd>
                            <dt class="col-sm-3">RFC3339</dt>
                            <dd class="col-sm-9"><?= $dateUtcEnter->format(\DateTime::RFC3339); ?></dd>
                        </dl>
                    </div>
                </div>
                <?php
            }
            ?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Преобразовать Timestamp</h3>
                </div>
                <div class="panel-body">
                    <div>
                        <?= \yii\helpers\Html::beginForm(['tools/timestamp']); ?>
                        <label>Введите timestamp</label>
                        <div class="form-group">
                            <?= \yii\helpers\Html::input('text', 'timestamp[value]', '', ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => $dateUtc->getTimestamp()]); ?>
                        </div>
                        <label>или дату и время</label>
                        <div class="form-group row">
                            <div class="col-md-4 col-lg-2">
                                <?= \yii\helpers\Html::input('text', 'timestamp[year]', '', ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Y']); ?>
                            </div>
                            <div class="col-md-4 col-lg-2">
                                <?= \yii\helpers\Html::input('text', 'timestamp[month]', '', ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'm']); ?>
                            </div>
                            <div class="col-md-4 col-lg-2">
                                <?= \yii\helpers\Html::input('text', 'timestamp[day]', '', ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'd']); ?>
                            </div>
                            <div class="col-md-4 col-lg-2">
                                <?= \yii\helpers\Html::input('text', 'timestamp[hour]', '', ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'H']); ?>
                            </div>
                            <div class="col-md-4 col-lg-2">
                                <?= \yii\helpers\Html::input('text', 'timestamp[minute]', '', ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'i']); ?>
                            </div>
                            <div class="col-md-4 col-lg-2">
                                <?= \yii\helpers\Html::input('text', 'timestamp[second]', '', ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 's']); ?>
                            </div>
                        </div>
                        <br/>
                        <?= \yii\helpers\Html::submitButton('Конвертировать', ['class' => 'btn btn-default']); ?>
                        <?= \yii\helpers\Html::endForm(); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <?= $this->render('_listTools', ['active' => 'timestamp']); ?>
        </div>
    </div>
</div>

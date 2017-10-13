<?php

/* @var $this yii\web\View */
/* @var $question app\models\Question */
/* @var $answer app\models\Answer */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Brain Calibrator');

?>
<div class="site-index">
    <div class="jumbotron">
        <h1><?=$question->text?></h1>
        <?php if ($question->submitter): ?>
        <div class="help-block">
            <?=Yii::t('app', 'Submitter:')?> <?=Html::a($question->submitter->name, ['user/view', 'id' => $question->submitterId])?>
        </div>
        <?php endif ?>
        <div class="row" style="margin-top: 3em">     
            <?php $form = ActiveForm::begin([
                'id' => 'answer-form',
                'action' => ['site/index'],
                'options' => ['class' => 'form-inline'],
                'fieldConfig' => [
                    'template' => "{input}",
                ],
                'enableAjaxValidation' => true
            ]); ?>
            <div class="form-group">
                <label for="exampleInputAmount"><?=Yii::t('app', '90%')?></label>
                <div class="input-group">
                    <div class="input-group-addon"><?=Yii::t('app', 'From')?></div>
                    <?=$form->field($answer, 'ninetyStart')->textInput(['pattern' => '[0-9 ]+', 'class' => 'form-control commas-input', 'autocomplete' => 'off', 'type' => 'number'])?>
                </div>
                <div class="input-group">
                    <div class="input-group-addon"><?=Yii::t('app', 'To')?></div>
                    <?=$form->field($answer, 'ninetyEnd')->textInput(['pattern' => '[0-9 ]+', 'class' => 'form-control commas-input', 'autocomplete' => 'off', 'type' => 'number'])?>
                </div>
            </div>
            <div class="form-group">
                <label for="exampleInputAmount"><?=Yii::t('app', '50%')?></label>
                <div class="input-group">
                    <div class="input-group-addon"><?=Yii::t('app', 'From')?></div>
                    <?=$form->field($answer, 'fiftyStart')->textInput(['pattern' => '[0-9 ]+', 'class' => 'form-control commas-input', 'autocomplete' => 'off', 'type' => 'number'])?>
                </div>
                <div class="input-group">
                    <div class="input-group-addon"><?=Yii::t('app', 'To')?></div>
                    <?=$form->field($answer, 'fiftyEnd')->textInput(['pattern' => '[0-9 ]+', 'class' => 'form-control commas-input', 'autocomplete' => 'off', 'type' => 'number'])?>
                </div>
            </div>
            <div class="form-group" style="margin: 3em 0; display: block">
                <?=$form->errorSummary($answer)?>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <?=$form->field($answer, 'questionId')->hiddenInput()?>
                    <?= Html::submitButton(Yii::t('app', 'Send answer'), ['class' => 'btn btn-lg btn-primary', 'name' => 'submit-button']) ?>
                    <?= Html::a(Yii::t('app', 'Skip question'), ['site/index', 'skip' => 1], ['class' => 'btn btn-lg btn-default']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php // var_dump(\app\models\Answer::find()->all()) ?>

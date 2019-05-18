<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Service';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-service">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields what would add site:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'service-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        ],
    ]); ?>

        <?= $form->field($model, 'secretKey')->passwordInput(['autofocus' => true])->label('Secret Key') ?>
        <?= $form->field($model, 'site')->textInput() ?>
        <?= $form->field($model, 'login')->textInput() ?>
        <?= $form->field($model, 'pass')->passwordInput() ?>
        <?= $form->field($model, 'field_login')->textInput() ?>
        <?= $form->field($model, 'field_pass')->textInput() ?>
        <?= $form->field($model, 'attribute')->radioList([0 => 'name', 1 => 'id']) ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('add', ['class' => 'btn btn-primary', 'name' => 'add-button']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html as HtmlHelper;
use yii\bootstrap\Html as HtmlBootstrap;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\CostInterval;
use common\modules\ApiRgs\models\Program;
use common\modules\ApiRgs\models\Sum2dict;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiRgs\models\Sum */
/* @var $form yii\widgets\ActiveForm */
/* @var $id integer */
?>

<div class="sum-form">
    <?php
    $form = ActiveForm::begin();

    echo $form->field($model, 'ext_id')->textInput([
        'disabled' => !empty($model->ext_id)
    ]);
    echo $form->field($model, 'title')->textInput();
    echo $form->field($model, 'sum')->textInput();
    echo $form->field($model, 'program_id')->dropDownList(ArrayHelper::map(Program::find()->asArray()->all(), 'id', 'title'));
    echo $form->field($model, 'enabled')->dropDownList([
        0 => 'Запрещена',
        1 => 'Разрешена'
    ]);
    echo $form->field($model, 'manual')->dropDownList([
        0 => 'Нет',
        1 => 'Да'
    ]);

    $costIntervals = CostInterval::find()->all();
    foreach ($costIntervals as $interval) {
        echo '<div class="col-xs-4">';
        echo HtmlBootstrap::checkbox('CostInterval[]', (bool) (isset($id) && Sum2dict::find()->where(['sum_id' => $id, 'internal_id' => $interval->id])->count()), ['value' => $interval->id, 'id' => 'interval-' . $interval->id]);
        echo HtmlBootstrap::label($interval->name, 'interval-' . $interval->id);
        echo '</div>';
    }

    echo '<div class = "form-group">';
    echo HtmlHelper::submitButton('Сохранить', ['class' => 'btn btn-primary']);
    echo '</div>';

    ActiveForm::end();
    ?>
</div>
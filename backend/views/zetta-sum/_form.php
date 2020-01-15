<?php

use yii\helpers\Html as HtmlHelper;
use yii\bootstrap\Html as HtmlBootstrap;
use yii\widgets\ActiveForm;
use common\models\CostInterval;
use common\modules\ApiZetta\models\Sum2dict;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiZetta\models\Sum */
/* @var $form yii\widgets\ActiveForm */
/* @var $id integer */
?>

<div class="sum-form">
    <?php
    $form = ActiveForm::begin();

    echo $form->field($model, 'enabled')->dropDownList([
        0 => 'Запрещена',
        1 => 'Разрешена'
    ]);

    $sums = CostInterval::find()->all();
    foreach ($sums as $sum) {
        echo '<div>';
        echo HtmlBootstrap::checkbox('CostInterval[]', (bool) Sum2dict::find()->where(['sum_id' => $id, 'internal_id' => $sum->id])->count(), ['value' => $sum->id, 'id' => 'sum-' . $sum->id]);
        echo HtmlBootstrap::label($sum->name, 'sum-' . $sum->id);
        echo '</div>';
    }

    echo '<br /><div class = "form-group">';
    echo HtmlHelper::submitButton('Сохранить', ['class' => 'btn btn-primary']);
    echo '</div>';

    ActiveForm::end();
    ?>
</div>
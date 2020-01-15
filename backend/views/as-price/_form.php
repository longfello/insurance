<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \yii\helpers\ArrayHelper;
use common\modules\ApiAlphaStrah\models\Regions;
use common\modules\ApiAlphaStrah\models\Amount;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiAlphaStrah\models\Price */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="price-form">

    <?php $form = ActiveForm::begin(); ?>

  	<?php echo $form->errorSummary($model); ?>

  	<?= $this->render('__form', ['model' => $model, 'form' => $form]); ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

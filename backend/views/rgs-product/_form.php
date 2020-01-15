<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use trntv\filekit\widget\Upload;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiRgs\models\Product */
/* @var $id integer */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="product-form">
    <?php
    $form = ActiveForm::begin();
    echo $form->errorSummary($model);
    echo $form->field($model, 'ext_id')->textInput();
    echo $form->field($model, 'title')->textInput();
    echo $form->field($model, 'rule')->widget(Upload::className(), [
        'url' => ['/file-storage/upload'],
        'maxFileSize' => 5000000, // 5 MiB
    ]);
    echo $form->field($model, 'police')->widget(Upload::className(), [
        'url' => ['/file-storage/upload'],
        'maxFileSize' => 5000000, // 5 MiB
    ]);
    ?>
    <div class="form-group">
        <?php echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
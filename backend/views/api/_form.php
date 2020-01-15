<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Api */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="api-form">

    <?php $form = ActiveForm::begin(); ?>

    <p class="alert alert-danger">Будьте внимательны! Если не уверены в том, что вы делаете, лучше не сохраняйте изменения! Некорректные или ошибочные настройки класса модуля api могут затруднить работу системы!</p>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'thumbnail')->widget(
      \trntv\filekit\widget\Upload::className(),
      [
        'url' => ['/file-storage/upload'],
        'maxFileSize' => 5000000, // 5 MiB
      ]);
    ?>

    <?= $form->field($model, 'class')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'rate_expert')->textInput(['maxlength' => true]) ?>

  	<?= $form->field($model, 'rate_asn')->textInput(['maxlength' => true]) ?>

  	<?= $form->field($model, 'enabled')->dropDownList(['1' => 'Разрешено', '0' => 'Запрещено']) ?>

    <?php echo $form->field($model, 'actions')->widget(
      \yii\imperavi\Widget::className(),
      [
        'plugins' => ['fullscreen', 'fontcolor', 'video'],
        'options' => [
          'minHeight' => 400,
          'maxHeight' => 400,
          'buttonSource' => true,
          'convertDivs' => false,
          'removeEmptyTags' => false,
          'imageUpload' => Yii::$app->urlManager->createUrl(['/file-storage/upload-imperavi'])
        ]
      ]
    ) ?>

    <?php echo $form->field($model, 'description')->widget(
      \yii\imperavi\Widget::className(),
      [
        'plugins' => ['fullscreen', 'fontcolor', 'video'],
        'options' => [
          'minHeight' => 400,
          'maxHeight' => 400,
          'buttonSource' => true,
          'convertDivs' => false,
          'removeEmptyTags' => false,
          'imageUpload' => Yii::$app->urlManager->createUrl(['/file-storage/upload-imperavi'])
        ]
      ]
    ) ?>

	<?= $form->field($model, 'service_center_url')->textInput(['maxlength' => true]) ?>

  <div class="form-group">
	    <?php if (!$model->isNewRecord) { ?>
          <?= Html::a('Редактировать номера телефонов', ['/api-phone/index', 'api_id' => $model->id], ['class' => 'btn btn-success']) ?>
          <?= Html::a('Редактировать файлы', ['/api-files/index', 'api_id' => $model->id], ['class' => 'btn btn-success']) ?>
          <br><br>
	    <?php } ?>

      <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

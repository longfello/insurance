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

<?php echo $form->field($model, 'program_id', ['template' => '{input}'])->hiddenInput() ?>

<?php echo $form->field($model, 'amount_id')->dropDownList(ArrayHelper::map(Amount::find()->all(),'id','amount')) ?>

<?php echo $form->field($model, 'region_id')->dropDownList(ArrayHelper::map(Regions::find()->all(),'id','name')) ?>
<?php
$data = [];
foreach(\common\modules\ApiAlphaStrah\models\StruhSum::findAll(['program_id' => $model->program_id]) as $one){
	/** @var $one \common\modules\ApiAlphaStrah\models\StruhSum */
	$data[$one->id] = $one->risk.' ( '.$one->getAmountPrint().' '.$one->valutaCode.' )';
}
?>

<?php echo $form->field( $model, 'struh_sum_id' )->dropDownList( $data ) ?>

<?php
$data = [
	null => '  -- Не применимо к программе --',
	\common\modules\ApiAlphaStrah\models\Price::SUM_INCLUDED => '  -- Сумма включена в программу --'
];
foreach(\common\modules\ApiAlphaStrah\models\StruhSum::findAll(['riskUID' => '1d71999c-be21-4bc9-a55d-d6af8129c3bf']) as $one){
	/** @var $one \common\modules\ApiAlphaStrah\models\StruhSum */
	$data[$one->id] = $one->risk.' - '. $one->variant .' ( '.$one->getAmountPrint().' '.$one->valutaCode.' )';
}
?>
<?php echo $form->field( $model, 'accident_sum_id' )->dropDownList( $data ) ?>

<?php
$data = [
	null => '  -- Не применимо к программе --',
	\common\modules\ApiAlphaStrah\models\Price::SUM_INCLUDED => '  -- Сумма включена в программу --'
];
foreach(\common\modules\ApiAlphaStrah\models\StruhSum::findAll(['riskUID' => '9f1bbb12-e28d-4f36-92ba-ecf225af967e']) as $one){
	/** @var $one \common\modules\ApiAlphaStrah\models\StruhSum */
	$data[$one->id] = $one->risk.' - '. $one->variant .' ( '.$one->getAmountPrint().' '.$one->valutaCode.' )';
}
?>
<?php echo $form->field( $model, 'luggage_sum_id' )->dropDownList( $data ) ?>

<?php
$data = [
	null => '  -- Не применимо к программе --',
	\common\modules\ApiAlphaStrah\models\Price::SUM_INCLUDED => '  -- Сумма включена в программу --'
];
foreach(\common\modules\ApiAlphaStrah\models\StruhSum::findAll(['riskUID' => '22e49815-4f76-4b84-a655-a9c19424c4b7']) as $one){
	/** @var $one \common\modules\ApiAlphaStrah\models\StruhSum */
	$data[$one->id] = $one->risk.' - '. $one->variant .' ( '.$one->getAmountPrint().' '.$one->valutaCode.' )';
}
?>
<?php echo $form->field( $model, 'civil_sum_id' )->dropDownList( $data ) ?>

<?php echo $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

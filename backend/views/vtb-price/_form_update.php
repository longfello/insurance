<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \yii\helpers\ArrayHelper;
use \common\modules\ApiVtb\models\Amount;
use \common\modules\ApiVtb\models\Period;
use \common\modules\ApiVtb\models\Regions;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiVtb\models\Price */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="price-form">
	<?php $form = ActiveForm::begin(); ?>

  <ul class="nav nav-tabs">
    <li class="active"><a href="#tab1" data-toggle="tab">Основная информация</a></li>
    <li><a href="#tab2" data-toggle="tab">Риски</a></li>
  </ul>

  <div class="tab-content ">
    <div class="tab-pane active" id="tab1">
		<?php echo $form->errorSummary( $model ); ?>

		<?php echo $form->field( $model, 'program_id', [ 'template' => '{input}' ] )->hiddenInput() ?>

		<?php echo $form->field( $model, 'amount_id' )->dropDownList( ArrayHelper::map( Amount::find()->all(), 'id', 'amount' ) ) ?>

		<?php echo $form->field( $model, 'period_id' )->dropDownList( ArrayHelper::map( Period::find()->all(), 'id', 'asText' ) ) ?>

		<?php echo $form->field( $model, 'region_id' )->dropDownList( ArrayHelper::map( Regions::find()->all(), 'id', 'name' ) ) ?>

		<?php echo $form->field( $model, 'price' )->textInput( [ 'maxlength' => true ] ) ?>
    </div>
    <div class="tab-pane" id="tab2">

      <div class="container-fluid">
        <div class="col-xs-1">Вкл.</div>
        <div class="col-xs-1">Страховая сумма</div>
        <div class="col-xs-10">Риск</div>
      </div>

		<?php
		$risks = \common\modules\ApiVtb\models\Risk::find()->orderBy( [ 'name' => SORT_ASC ] )->all();
		foreach ( $risks as $risk ) {
		  /** @var $risk \common\modules\ApiVtb\models\Risk */
			$p2r     = \common\modules\ApiVtb\models\Price2risk::findOne( [
				'risk_id'    => $risk->id,
				'price_id' => $model->id
			] );
			$checked = $p2r ? "checked='checked'" : "";
			?>
          <div class="container-fluid">
            <div class="col-xs-1"><input <?= $checked ?> type="checkbox" name="risk[]" value="<?= $risk->id ?>"></div>
            <div class="col-xs-1">
				<?=
				\kartik\widgets\TouchSpin::widget( [
					'name'          => 'price_for_' . $risk->id,
					'value'         => $p2r ? $p2r->amount : 0,
					'options'       => [
						'autocomplete' => 'off'
					],
					'pluginOptions' => [
						'min'             => 0,
						'max'             => 10000000,
						'step'            => 1,
						'decimals'        => 0,
						'verticalbuttons' => true
					],
				] );
				?>
            </div>
            <div class="col-xs-10">
				<?php
				$name    = $risk->name;
				if ($internalRisks = $risk->internalRisks){
			$name = "<span class='text-green'>$name</span>";
				  $name .= ", соответствует внутренним рискам: <ul>";
				  foreach ($internalRisks as $internalRisk){
			      $rname = $internalRisk->name;
			      $rname.= $internalRisk->category ? " - {$internalRisk->category->name}" : "";
				    $name .= "<li>{$rname}</li>";
          }
				  $name .= "</ul>";
        } else {
    			$name = "<span class='text-red'>$name</span>";
				  $name .= "<div class='text-red'>Нет соответствия рискам из внутреннего справочника</div>";
        }
				?>
				<?= $name ?>
            </div>
          </div>
			<?php
		}
		?>
    </div>
  </div>

  <div class="form-group">
	  <?php echo Html::submitButton( $model->isNewRecord ? 'Добавить' : 'Сохранить', [ 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary' ] ) ?>
  </div>

	<?php ActiveForm::end(); ?>

</div>

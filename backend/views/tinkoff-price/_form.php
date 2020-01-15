<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \yii\helpers\ArrayHelper;
use \common\modules\ApiTinkoff\models\Risk;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiTinkoff\models\Price */
/* @var $productModel common\modules\ApiTinkoff\models\Product */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="price-form">
    <?php $form = ActiveForm::begin(); ?>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab1" data-toggle="tab">Основная информация</a></li>
        <li><a href="#tab2" data-toggle="tab">Риски</a></li>
        <li><a href="#tab3" data-toggle="tab">Регионы и страны</a></li>
    </ul>

    <div class="tab-content ">
        <div class="tab-pane active" id="tab1">
            <?php echo $form->errorSummary( $model ); ?>

            <?php echo $form->field($model, 'product_id', ['template' => '{input}'])->hiddenInput() ?>

            <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?php echo $form->field($model, 'AssistanceLevel')->dropDownList(ArrayHelper::map($productModel->AssistanceLevel,'Value','Display')); ?>
            <?php echo $form->field($model, 'Currency')->dropDownList(ArrayHelper::map($productModel->Currency,'Value','Display')); ?>
            <?php echo $form->field($model, 'TravelMedicineLimit')->dropDownList(ArrayHelper::map(Risk::find()->where(['Code'=>'TravelMedicineLimit'])->one()->TypeValues['AvailableValue'],'Value','Display')) ?>
            <?php echo $form->field($model, 'DeductibleAmount')->dropDownList(ArrayHelper::map(Risk::find()->where(['Code'=>'DeductibleAmount'])->one()->TypeValues['AvailableValue'],'Value','Display')) ?>
        </div>
        <div class="tab-pane" id="tab2">
            <br/>
            <?php
            $risks = \common\modules\ApiTinkoff\models\Risk::find()->where(['parent_id'=>0])->andWhere(['enabled'=>1])->orderBy( [ 'id' => SORT_ASC ] )->all();
            foreach ( $risks as $risk ) {
                /** @var $risk \common\modules\ApiTinkoff\models\Risk */
                $p2r     = \common\modules\ApiTinkoff\models\Price2Risk::findOne( [
                    'risk_id'    => $risk->id,
                    'price_id' => $model->id
                ] );
                $checked = ($p2r || $risk->Code=='TravelMedicine') ? "checked='checked'" : "";
                $sub_risks = \common\modules\ApiTinkoff\models\Risk::find()->where(['parent_id'=>$risk->id])->andWhere(['!=','Code','PropertyAddress'])->andWhere(['enabled'=>1])->orderBy( [ 'id' => SORT_ASC ] )->all();
                ?>
                <div class="container-fluid" style="display: flex; justify-content: center;align-items: center;">
                    <div class="col-xs-1" style="text-align: right;"><input <?= $checked ?> type="checkbox" name="risk[]" value="<?= $risk->id ?>" <?= ($risk->Code=='TravelMedicine')?'onclick="return false;"':''; ?>></div>
                    <div class="col-xs-2">
                        <?php
                        if ($risk->Code!='TravelMedicine') {
                            if (count($sub_risks) == 0) {
                                echo \kartik\widgets\TouchSpin::widget([
                                    'name' => 'price_for_' . $risk->id,
                                    'value' => $p2r ? $p2r->amount : 0,
                                    'options' => [
                                        'autocomplete' => 'off'
                                    ],
                                    'pluginOptions' => [
                                        'min' => 0,
                                        'max' => 10000000,
                                        'step' => 1,
                                        'decimals' => 0,
                                        'verticalbuttons' => true
                                    ],
                                ]);
                            } else {
                                foreach ($sub_risks as $srisk) {
                                    /** @var $srisk \common\modules\ApiTinkoff\models\Risk */

                                    $p2sr = \common\modules\ApiTinkoff\models\Price2Risk::findOne([
                                        'risk_id' => $srisk->id,
                                        'price_id' => $model->id
                                    ]);
                                    if ($srisk->Type == 'DECIMAL') {
                                        echo "<div style='text-align: center'>" . $srisk->Name . " от " . $srisk->TypeValues['MinValue'] . " до " . $srisk->TypeValues['MaxValue']." ".$model->Currency."</div>";
                                        echo \kartik\widgets\TouchSpin::widget([
                                            'name' => 'price_for_' . $srisk->id,
                                            'value' => $p2sr ? $p2sr->amount : $srisk->TypeValues['MinValue'],
                                            'options' => [
                                                'autocomplete' => 'off'
                                            ],
                                            'pluginOptions' => [
                                                'min' => $srisk->TypeValues['MinValue'],
                                                'max' => $srisk->TypeValues['MaxValue'],
                                                'step' => 1,
                                                'decimals' => 0,
                                                'verticalbuttons' => true
                                            ],
                                        ]);
                                    } elseif ($srisk->Type == 'LIST') {
                                        echo "<div style='text-align: center'>" . $srisk->Name . "</div>";
                                        echo Html::dropDownList('price_for_' . $srisk->id, $p2sr ? $p2sr->amount : null, ArrayHelper::map($srisk->TypeValues['AvailableValue'], 'Value', 'Display'));
                                    }
                                    echo "<br/>";
                                }
                            }
                        } else {
                            echo "<ul>";
                            echo "<li>Страховой лимит: ".$model->TravelMedicineLimit." ".$model->Currency."</li>";
                            echo "<li>Размер франшизы: ".$model->DeductibleAmount." ".$model->Currency."</li>";
                            echo "</ul>";
                        }
                        ?>
                    </div>
                    <div class="col-xs-9">
                        <?php
                        $name    = $risk->Name;
                        if ($internalRisks = $risk->internalRisks){
                            $name = "<span class='text-green'><b>$name</b></span>";
                            $name .= ", соответствует внутренним рискам: <ul>";
                            foreach ($internalRisks as $internalRisk){
                                $rname = $internalRisk->name;
                                $rname.= $internalRisk->category ? " - {$internalRisk->category->name}" : "";
                                $name .= "<li>{$rname}</li>";
                            }
                            $name .= "</ul>";
                        } else {
                            $name = "<span class='text-red'><b>$name</b></span>";
                            $name .= "<div class='text-red'>Нет соответствия рискам из внутреннего справочника</div>";
                        }
                        ?>
                        <?= $name ?>
                    </div>
                </div>
                <hr/>
                <?php
            }
            ?>
        </div>
        <div class="tab-pane" id="tab3">
            <div class="container-fluid">
                <?php
                $areas = \common\modules\ApiTinkoff\models\Area::find()->where(['enabled'=>1])->orderBy( [ 'Display' => SORT_ASC ] )->all();
                foreach ( $areas as $area ) {
                    /** @var $area \common\modules\ApiTinkoff\models\Area */
                    $a2r     = \common\modules\ApiTinkoff\models\Price2Area::findOne( [
                        'area_id'  => $area->id,
                        'price_id' => $model->id
                    ] );
                    $checked = $a2r ? "checked='checked'" : "";
                    ?>
                    <div class="col-xs-3">
                        <input <?= $checked ?> type="checkbox" name="area[]" value="<?= $area->id ?>" id="area_<?= $area->id ?>">
                        <label for="area_<?= $area->id ?>"><?= $area->Display; ?></label>
                    </div>
                <?php } ?>
            </div>
            <hr/>
            <div class="container-fluid">
                <div class="col-xs-12" style="text-align: center;"><a id="check_all" href="#">Выбрать все страны</a> / <a id="uncheck_all" href="#">Отменить выбор стран</a></div>
                <?php
                $countries = \common\modules\ApiTinkoff\models\Country::find()->where(['enabled'=>1])->orderBy( [ 'Display' => SORT_ASC ] )->all();
                foreach ( $countries as $country ) {
                    /** @var $country \common\modules\ApiTinkoff\models\Country */
                    $a2r     = \common\modules\ApiTinkoff\models\Price2Country::findOne( [
                        'country_id'  => $country->id,
                        'price_id' => $model->id
                    ] );
                    $checked = $a2r ? "checked='checked'" : "";
                    ?>
                    <div class="col-xs-4">
                        <input <?= $checked ?> type="checkbox" name="country[]" value="<?= $country->id ?>" id="country_<?= $country->id ?>">
                        <label for="country_<?= $country->id ?>"><?= $country->Display; ?></label>
                    </div>
                <?php } ?>
            </div>
        </div>

    </div>

    <div class="form-group">
        <?php echo Html::submitButton( $model->isNewRecord ? 'Добавить' : 'Сохранить', [ 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary' ] ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs("
  $(document).on('click', '#check_all', function(e){
      e.preventDefault();
      if (confirm('Выбрать все страны?')) {
         $(this).parent().parent().find('input[name=\"country[]\"]').prop('checked',true);
      }
  });

    $(document).on('click', '#uncheck_all', function(e){
      e.preventDefault();
      if (confirm('Отменить выбор стран?')) {
         $(this).parent().parent().find('input[name=\"country[]\"]').prop('checked',false);
      }
  });
");
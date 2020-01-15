<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $program \common\modules\ApiAlphaStrah\models\InsuranceProgramm */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $form yii\widgets\ActiveForm */

$this->title                   = 'Редактирование рисков "'.$risk->risk.'" програмы страхования: ' . $program->insuranceProgrammName;
$this->params['breadcrumbs'][] = [ 'label' => 'Програмы страхования', 'url' => [ 'index' ] ];
$this->params['breadcrumbs'][] = [ 'label' => $program->insuranceProgrammName, 'url' => [ 'update', 'id' => $program->insuranceProgrammID ] ];
$this->params['breadcrumbs'][] = 'Редактирование '.$risk->risk;
?>
<div class="program-edit">
    <?php $form = ActiveForm::begin(); ?>

    <div class="container-fluid">
        <div class="col-xs-1">Вкл.</div>
        <div class="col-xs-10">Риск</div>
    </div>

    <?php
    $risks = \common\models\Risk::find()->orderBy(['category_id' => SORT_ASC, 'name' => SORT_ASC])->all();
    foreach($risks as $risk) {
        //$p2r     = \common\modules\ApiErv\models\Program2Risk::findOne(['risk_id' => $risk->id, 'program_id' => $model->id]);
        $checked = in_array($risk->id, $linked_risks)?"checked='checked'":"";
        ?>
        <div class="container-fluid">
            <div class="col-xs-1"><input <?= $checked ?> type="checkbox" name="risk[]" value="<?= $risk->id ?>" id="risk_<?= $risk->id ?>"></div>
            <div class="col-xs-11">
                <?php
                $name = $risk->name;
                $name .= $risk->category?" - {$risk->category->name}":"";
                ?>
                <label for="risk_<?= $risk->id ?>"><?= $name ?></label>
            </div>
        </div>
        <?php
    }
    ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
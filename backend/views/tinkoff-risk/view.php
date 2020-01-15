<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use common\modules\ApiTinkoff\models\Risk2Internal;

/* @var $this yii\web\View */
/* @var $risk_model \common\modules\ApiTinkoff\models\Risk */
/* @var $childRisksDataProvider yii\data\ActiveDataProvider */
/* @var $form yii\widgets\ActiveForm */

$this->title = $risk_model->Name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Риски'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="risk-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab1" data-toggle="tab">Основная информация</a></li>
        <li><a href="#tab2" data-toggle="tab">Внутренние риски</a></li>
    </ul>

    <div class="tab-content ">
    <div class="tab-pane active" id="tab1">
        <?= DetailView::widget([
            'model' => $risk_model,
            'attributes' => [
                'Name',
                'Code',
                [
                    'format' => 'raw',
                    'label' => 'Разрешен',
                    'value' => function($model){
                        return ($model->enabled==1)?'Да':'Нет';
                    }
                ]
            ],
        ]) ?>

        <?php if ($childRisksDataProvider->getCount()>0) {?>
            <h2>Вложенные опции</h2>

            <?= \kartik\grid\GridView::widget([
                'dataProvider' => $childRisksDataProvider,
                'columns' => [
                        'Name',
                        'Code',
                        'Type',
                        'values' => [
                            'format' => 'html',
                            'label' => 'Значения',
                            'value' => function($model){
                                $result = '';
                                if ($model->Type=='DECIMAL') {
                                    $result = 'от '.$model->TypeValues['MinValue']." до ".$model->TypeValues['MaxValue'];
                                } elseif ($model->Type=='LIST') {
                                    $result .= "<ul>";
                                    foreach ($model->TypeValues['AvailableValue'] as $val) {
                                        $result .= "<li>".$val['Display']."</li>";
                                    }
                                    $result .= "</ul>";
                                }
                                return $result;
                            }
                        ],
                        'functions' => [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '',
                        ]
                    ],
                ]); ?>
        <?php } ?>
    </div>
    <div class="tab-pane" id="tab2">
    <?php $form = ActiveForm::begin(); ?>
    <?php echo $form->errorSummary($risk_model); ?>
    <?php
    $risks = \common\models\Risk::find()->orderBy(['name' => SORT_ASC])->all();
    foreach ($risks as $risk){
        /** @var $risk \common\models\Risk */
        ?>

        <div class="col-xs-6">
            <label for="risk-<?=$risk->id ?>">
                <input id="risk-<?=$risk->id ?>" name="Risk2internal[]" value="<?= $risk->id ?>" type="checkbox" <?php if (Risk2internal::findOne(['risk_id' => $risk_model->id, 'internal_id' => $risk->id])){ echo('checked'); } ?>>
                <?=$risk->name ?>
            </label>
        </div>
        <?php
    }
    ?>
        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    </div>


</div>
<?php

$this->registerJs("
  $(document).on('change', '.ajaxify', function(){

    var form = $(this).parents('form');
    $(form).blur();
    var data = $(form).serializeArray();

    $.post($(form).attr('action'), data, function(){

    });

  });
");
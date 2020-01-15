<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\popover\PopoverX;
use kartik\editable\Editable;
use common\modules\ApiZetta\models\ProgramRisk;
use common\modules\ApiZetta\models\ProgramSum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Програмы страхования';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="country-index">
    <p>
        <?= Html::a(Yii::t('backend', 'Обновить список програм страхования'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'title',
            [
                'format' => 'raw',
                'label' => 'Соответствие справочнику рисков',
                'value' => function($model) {
                    $risks = ProgramRisk::find()->where(['program_id' => $model->id])->all();
                    $res = [];
                    foreach ($risks as $risk) {
                        /** @var $risk \common\modules\ApiZetta\models\ProgramRisk */
                        $res[] = $risk->riskModel->title;
                    }
                    sort($res);
                    return Html::ul($res);
                }
            ],
            [
                'format' => 'raw',
                'label' => 'Соответствие справочнику страховых сумм',
                'value' => function($model) {
                    $sums = ProgramSum::find()->where(['program_id' => $model->id])->all();
                    $res = [];
                    foreach ($sums as $sum) {
                        /** @var $sum common\modules\ApiZetta\models\ProgramSum */
                        $sumModel = $sum->sumModel;
                        $currency = $sumModel->currencyModel;
                        if ($sumModel->enabled && $currency->enabled) {
                            $res[] = $sumModel->sum;
                        }
                    }
                    sort($res);
                    return Html::ul($res);
                }
            ],
            [
                'class' => EditableColumn::className(),
                'attribute' => 'priority',
                'pageSummary' => true,
                'editableOptions' => function ($model, $key, $index) {
                    return [
                        'size' => PopoverX::SIZE_MEDIUM,
                        'inputType' => Editable::INPUT_TEXT
                    ];
                }
            ],
            [
                'class' => EditableColumn::className(),
                'attribute' => 'enabled',
                'pageSummary' => true,
                'editableOptions' => function ($model, $key, $index) {
                    return [
                        'size' => PopoverX::SIZE_MEDIUM,
                        'inputType' => Editable::INPUT_DROPDOWN_LIST,
                        'data' => [
                            0 => 'Запрещена',
                            1 => 'Разрешена'
                        ],
                        'displayValueConfig' => [
                            0 => 'Запрещена',
                            1 => 'Разрешена'
                        ]
                    ];
                }
            ],
            'functions' => [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}'
            ]
        ]
    ]);
    ?>
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

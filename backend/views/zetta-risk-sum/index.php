<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\popover\PopoverX;
use kartik\editable\Editable;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\db\Expression;

use common\modules\ApiZetta\models\Program;
use common\modules\ApiZetta\models\Risk;
use common\modules\ApiZetta\models\ProgramRiskSum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Суммы покрытия';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="program-risk-sum-index">
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'program',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Program::find()->orderBy('id')->asArray()->all(), 'id', 'title'),
                'filterInputOptions' => ['placeholder' => 'Все программы'],
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ]
            ],
            [
                'attribute' => 'risk',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Risk::find()->alias('azr')->innerJoin('api_zetta_program_risk azpr', 'azr.id=azpr.risk_id')->where($searchModel->program ? ['azpr.program_id' => $searchModel->program] : '')->orderBy('azr.id')->asArray()->all(), 'id', 'title'),
                'filterInputOptions' => ['placeholder' => 'Все риски'],
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ]
            ],
            [
                'attribute' => 'sum',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map((new Query)->select(['azs.*', new Expression('CONCAT(azs.sum, " ", azc.title) sum_currency')])->from('api_zetta_sum azs')->innerJoin('api_zetta_currency azc', 'azs.currency_id=azc.id')->innerJoin('api_zetta_program_sum azps', 'azs.id=azps.sum_id')->where($searchModel->program ? ['azps.program_id' => $searchModel->program] : '')->groupBy('sum_currency')->orderBy(['azs.currency_id' => SORT_ASC, 'azs.sum' => SORT_ASC])->all(), 'id', 'sum_currency'),
                'filterInputOptions' => ['placeholder' => 'Все суммы'],
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ]
            ],
            [
                'class' => EditableColumn::className(),
                'label' => 'Страховое покрытие',
                'value' => function ($model) {
                    $model = ProgramRiskSum::findOne([
                        'program_id' => $model['program_id'],
                        'risk_id' => $model['risk_id'],
                        'sum_id' => $model['sum_id']
                    ]);

                    if ($model !== null) {
                        return $model->sum;
                    }

                    return null;
                },
                'editableOptions' => function ($model, $key, $index) {
                    return [
                        'name' => 'Sum[' . $model['program_id'] . '-' . $model['risk_id'] . '-' . $model['sum_id'] . ']',
                        'size' => PopoverX::SIZE_MEDIUM,
                        'inputType' => Editable::INPUT_TEXT
                    ];
                }
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

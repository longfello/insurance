<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\popover\PopoverX;
use kartik\editable\Editable;
use yii\helpers\ArrayHelper;
use common\modules\ApiZetta\models\Currency;
use common\modules\ApiZetta\models\Sum2dict;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Страховые суммы';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="sum-index">
    <p>
        <?= Html::a(Yii::t('backend', 'Обновить список страховых сумм'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'attribute' => 'currency_id',
                'label' => 'Валюта',
                'value' => function ($model) {
                    return $model->currencyModel->title;
                }
            ],
            'sum',
            [
                'format' => 'raw',
                'label' => 'Соответствие внутреннему справочнику',
                'value' => function($model) {
                    $sums = Sum2dict::find()->where(['sum_id' => $model->id])->all();
                    $res = [];
                    foreach ($sums as $sum) {
                        $res[] = $sum->costIntervalModel->name;
                    }
                    sort($res);
                    return Html::ul($res);
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

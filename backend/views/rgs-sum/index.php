<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\popover\PopoverX;
use kartik\editable\Editable;

use common\modules\ApiRgs\models\Sum2dict;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Суммы страхования';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="sum-index">
    <p>
        <?= Html::a(Yii::t('backend', 'Добавить'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('backend', 'Обновить список страховых сумм'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'title',
            [
                'format' => 'raw',
                'label' => 'Программа страхования',
                'value' => function ($model) {
                    $res = 'Не указана';

                    $program = $model->programModel;
                    if ($program) {
                        $res = $program->title;
                    }

                    return $res;
                }
            ],
            [
                'format' => 'raw',
                'label' => 'Соответствие внутреннему справочнику',
                'value' => function($model) {
                    $intervals = Sum2dict::find()->where(['sum_id' => $model->id])->all();
                    $res = [];
                    foreach ($intervals as $interval) {
                        /** @var $interval common\modules\ApiRgs\models\Sum2dict */
                        $res[] = $interval->costIntervalModel->name;
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
                            0 => 'Нет',
                            1 => 'Да'
                        ],
                        'displayValueConfig' => [
                            0 => 'Нет',
                            1 => 'Да'
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

<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\popover\PopoverX;
use kartik\editable\Editable;

use common\modules\ApiRgs\models\ProgramRisk;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Программы страхования';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="program-index">
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
                'label' => 'Вид риска',
                'value' => function ($model) {
                    $res = 'Не указан';

                    $riskType = $model->riskType;
                    if ($riskType) {
                        $res = $riskType->title;
                    }

                    return $res;
                }
            ],
            [
                'format' => 'raw',
                'label' => 'Соответствие внутреннему справочнику рисков',
                'value' => function($model) {
                    $risks = ProgramRisk::find()->where(['program_id' => $model->id])->all();
                    $res = [];
                    foreach ($risks as $risk) {
                        /** @var $risk common\modules\ApiRgs\models\ProgramRisk */
                        $res[] = $risk->riskModel->name;
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

<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\popover\PopoverX;
use kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Дополнительные условия';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="additional-condition-index">
    <p>
        <?= Html::a(Yii::t('backend', 'Обновить список доп. условий'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'title',
            [
                'format' => 'raw',
                'label' => 'Вид доп. условия',
                'value' => function ($model) {
                    $res = 'Не указан';

                    $acType = $model->additionalConditionTypeModel;
                    if ($acType) {
                        $res = $acType->title;
                    }

                    return $res;
                }
            ],
            [
                'class' => EditableColumn::className(),
                'attribute' => 'default',
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

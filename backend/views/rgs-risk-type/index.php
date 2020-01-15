<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\popover\PopoverX;
use kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Виды рисков';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="risk-type-index">
    <p>
        <?= Html::a(Yii::t('backend', 'Обновить список видов рисков'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'title',
            [
                'class' => EditableColumn::className(),
                'attribute' => 'main',
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

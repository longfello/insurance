<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\popover\PopoverX;
use kartik\editable\Editable;
use common\modules\ApiZetta\models\Sport;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Спорт';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="country-index">
    <p>
        <?= Html::a(Yii::t('backend', 'Обновить список спортивных програм'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'title',
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

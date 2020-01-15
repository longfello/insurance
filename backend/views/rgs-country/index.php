<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\popover\PopoverX;
use kartik\editable\Editable;

use common\modules\ApiRgs\models\Country2dict;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Страны';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="country-index">
    <p>
        <?= Html::a(Yii::t('backend', 'Обновить список стран'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'title',
            [
                'format' => 'raw',
                'label' => 'Соответствие внутреннему справочнику',
                'value' => function($model) {
                    $countries = Country2dict::find()->where(['country_id' => $model->id])->all();
                    $res = [];
                    foreach ($countries as $country) {
                        /** @var $country common\modules\ApiRgs\models\Country2dict */
                        $res[] = $country->geoNameModel->name;
                    }
                    sort($res);
                    return Html::ul($res);
                }
            ],
            [
                'format' => 'raw',
                'label' => 'Территория',
                'value' => function ($model) {
                    $res = 'Не указана';

                    $territory = $model->territory;
                    if ($territory) {
                        $res = $territory->title;
                    }

                    return $res;
                }
            ],
            [
                'class' => EditableColumn::className(),
                'attribute' => 'min_sum',
                'editableOptions' => function ($model, $key, $index) {
                    return [
                        'size' => PopoverX::SIZE_MEDIUM,
                        'inputType' => Editable::INPUT_DROPDOWN_LIST,
                        'data' => [
                            0 => 'Нет',
                            50000 => '50 000 у.е.'
                        ],
                        'displayValueConfig' => [
                            0 => 'Нет',
                            50000 => '50 000 у.е.'
                        ]
                    ];
                }
            ],
            [
                'class' => EditableColumn::className(),
                'attribute' => 'enabled',
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

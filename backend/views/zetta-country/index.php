<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\popover\PopoverX;
use kartik\editable\Editable;

use common\modules\ApiZetta\models\Country2dict;
use common\modules\ApiZetta\models\CountryCurrency;
use common\modules\ApiZetta\models\CountryTerritory;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Страны';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="country-index">
    <p>
        <?= Html::a(Yii::t('backend', 'Обновить список стран'), ['import'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a(Yii::t('backend', 'Обновить валюты'), ['update-currencies'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a(Yii::t('backend', 'Обновить территории'), ['update-territories'], ['class' => 'btn btn-warning']) ?>
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
                        /** @var $country common\modules\ApiZetta\models\Country2dict */
                        $res[] = $country->geoNameModel->name;
                    }
                    sort($res);
                    return Html::ul($res);
                }
            ],
            [
                'format' => 'raw',
                'label' => 'Валюты',
                'value' => function ($model) {
                    $currencies = CountryCurrency::find()->where(['country_id' => $model->id])->all();
                    $res = [];
                    foreach ($currencies as $currency) {
                        /** @var $currency common\modules\ApiZetta\models\CountryCurrency */
                        $res[] = $currency->currencyModel->title;
                    }
                    sort($res);
                    return Html::ul($res);
                }
            ],
            [
                'format' => 'raw',
                'label' => 'Территории',
                'value' => function ($model) {
                    $territories = CountryTerritory::find()->where(['country_id' => $model->id])->all();
                    $res = [];
                    foreach ($territories as $territory) {
                        /** @var $territory common\modules\ApiZetta\models\CountryTerritory */
                        $res[] = $territory->territoryModel->title;
                    }
                    sort($res);
                    return Html::ul($res);
                }
            ],
            'type',
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

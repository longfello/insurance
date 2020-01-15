<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Страны';
$this->params['breadcrumbs'][] = $this->title;

$regions = \common\modules\ApiAlphaStrah\models\Regions::find()->orderBy(['short_name' => SORT_ASC])->all();

?>
<div class="country-index">
    <p>
        <?= Html::a(Yii::t('backend', 'Обновить список стран'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'countryID',
            // 'countryUID',
            'countryName',
            /*
            [
	            'class'=>'kartik\grid\EditableColumn',
              'attribute'=>'name',
              'pageSummary'=>true,
              'editableOptions'=> function ($model, $key, $index) {
	              return [
		              'header' => 'Локальное название',
		              'size'   => 'md',
	              ];
              }
            ],
            */
            /*
            [
	            'class'=>\kartik\grid\EditableColumn::className(),
	            'attribute'=>'parent_id',
	            'pageSummary'=>true,
              'editableOptions'=> function ($model, $key, $index) {
	              return [
		              'size'   => \kartik\popover\PopoverX::SIZE_MEDIUM,
                  'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                  'data' => [null => '(не задано)'] + \yii\helpers\ArrayHelper::map(\common\models\GeoCountry::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name'),
	              'displayValueConfig' => \yii\helpers\ArrayHelper::map(\common\models\GeoCountry::find()->asArray()->all(), 'id', 'name'),
	              ];
              }
            ],
            */
            [
              'format' => 'raw',
              'label' => 'Соответствие внутреннему справочнику',
              'value' => function($model){
                $countries = \common\modules\ApiAlphaStrah\models\Country2dict::find()->where(['api_id' => $model->countryID])->all();
                $res = [];
                foreach ($countries as $country){
                  /** @var $country \common\modules\ApiAlphaStrah\models\Country2dict */
                  $res[] = $country->geoNameModel->name;
                }
                sort($res);
                return Html::ul($res);
              }
            ],
            [
	            'class'=>\kartik\grid\EditableColumn::className(),
	            'attribute'=>'enabled',
	            'pageSummary'=>true,
              'editableOptions'=> function ($model, $key, $index) {
	              return [
		              'size'   => \kartik\popover\PopoverX::SIZE_MEDIUM,
                  'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                  'data' => [
	                  0 => 'Запрещена',
	                  1 => 'Разрешена'
                  ],
                  'displayValueConfig' => [
	                  0 => 'Запрещена',
	                  1 => 'Разрешена'
                  ],
	              ];
              }
            ],
            //
            // 'countryKV',
            // 'assistanteID',
            // 'assistanteUID',
            // 'assistanceCode',
            'assistanceName',
            'assistancePhones:raw',
            'terName',
            'region_id'=>  [
                'format' => 'raw',
                'label' => 'Регион',
                'value' => function($model){
                    return ($model->region)?$model->region->short_name:'';
                }
            ],
            'visa' => [
                'attribute' => 'visa',
                'format' => 'raw',
                'value' => function($model) {
                    $html = Html::beginForm('/as-country/save-visa', 'post', ['style' => 'width:50px']);
                    $html .= \yii\bootstrap\Html::hiddenInput('country_id', $model->countryID);
                    $html .= \yii\bootstrap\Html::checkbox('checked', (bool)$model->visa, [ 'class' => 'ajaxify' ]);
                    $html .= Html::endForm();
                    return $html;
                }
            ],
            'functions' => ['class' => 'yii\grid\ActionColumn','template' => '{view}{update}']],
    ]); ?>
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

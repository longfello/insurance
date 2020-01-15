<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Страны');
$this->params['breadcrumbs'][] = $this->title;

$regions = \common\modules\ApiErv\models\Regions::find()->orderBy(['short_name' => SORT_ASC])->all();

?>
<div class="geo-country-index">

    <?php
    $regionsFields = [];
    foreach ($regions as $region){
	    $regionsFields['region_'.$region->id] = [
	      'label' => Html::tag('span', $region->short_name, ['title' => $region->name]),
	      'encodeLabel' => false,
        'format' => 'raw',
	      'value' => function($model) use ($region) {
	        $html = Html::beginForm('/erv-country/save', 'post', ['style' => 'width:50px']);
	        $l_model = \common\modules\ApiErv\models\Region2Country::find()->where(['region_id' => $region->id, 'country_id' => $model->id])->one();
	        $html .= \yii\bootstrap\Html::hiddenInput('country_id', $model->id);
	        $html .= \yii\bootstrap\Html::hiddenInput('region_id', $region->id);
	        $html .= \yii\bootstrap\Html::checkbox('checked', (bool)$l_model, [ 'class' => 'ajaxify' ]);
          $html .= Html::endForm();
	        return $html;
        }
      ];
    }

    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'iso_alpha2',
            'iso_alpha3',
            //'iso_numeric',
            //'fips_code',
            'name',
            // 'capital',
            // 'areainsqkm',
            // 'population',
            // 'continent',
            // 'tld',
            // 'currency',
            // 'currencyName',
            // 'Phone',
            // 'postalCodeFormat',
            // 'postalCodeRegex',
            // 'languages',
            // 'neighbours',
            // 'slug',
        ] + $regionsFields,
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


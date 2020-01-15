<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Страны';
$this->params['breadcrumbs'][] = $this->title;

$regions = \common\modules\ApiVtb\models\Regions::find()->orderBy(['short_name' => SORT_ASC])->all();

?>
<div class="country-index">

	<?php
	$regionsFields = [];
	foreach ($regions as $region){
		$regionsFields['region_'.$region->id] = [
			'label' => Html::tag('span', $region->short_name, ['title' => $region->name]),
			'encodeLabel' => false,
			'format' => 'raw',
			'value' => function($model) use ($region) {
				$html = Html::beginForm('/vtb-country/save', 'post', ['style' => 'width:50px']);
				$l_model = \common\modules\ApiVtb\models\Region2Country::find()->where(['region_id' => $region->id, 'country_id' => $model->id])->one();
				$html .= \yii\bootstrap\Html::hiddenInput('country_id', $model->id);
				$html .= \yii\bootstrap\Html::hiddenInput('region_id', $region->id);
				$html .= \yii\bootstrap\Html::checkbox('checked', (bool)$l_model, [ 'class' => 'ajaxify' ]);
				$html .= Html::endForm();
				return $html;
			}
		];
	}

	?>

    <p>
        <?php echo Html::a('Импорт', ['import'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'code',
            [
              'format' => 'raw',
              'label' => 'Соответствие внутреннему справочнику',
              'value' => function($model){
                $countries = \common\modules\ApiVtb\models\Country2dict::find()->where(['api_id' => $model->id])->all();
                $res = [];
                foreach ($countries as $country){
                  /** @var $country \common\modules\ApiAlphaStrah\models\Country2dict */
                  $res[] = $country->geoNameModel->name;
                }
                sort($res);
                return Html::ul($res);
              }
            ],
            'minInsuranceSum' => [
              'attribute' => 'minInsuranceSum',
              'value' => function($model){ return $model->minInsuranceSum?$model->minInsuranceSum:''; }
            ],
            'shengen' => [
              'attribute' => 'shengen',
              'value' => function($model){ return $model->shengen?'Да':''; }
            ],
            'war' => [
              'attribute' => 'war',
              'value' => function($model){ return $model->war?'Да':''; }
            ],
            'currencies',
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
        ] + $regionsFields + ['functions' => ['class' => 'yii\grid\ActionColumn','template' => '{update}']],
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

<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Страны');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="geo-country-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('backend', 'Добавить'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'iso_alpha2',
            'iso_alpha3',
            //'iso_numeric',
            //'fips_code',
            'name',
            'capital',
            // 'areainsqkm',
            // 'population',
            // 'continent',
            // 'tld',
            'currency',
            // 'currencyName',
            // 'Phone',
            // 'postalCodeFormat',
            // 'postalCodeRegex',
            'languages',
            // 'neighbours',
            // 'slug',
            'type' => [
              'attribute' => 'type',
              'value' => function($model){
                  if ($model->type && isset($model->types[$model->type])) {
                    return $model->types[$model->type];
                  } else return '???';
              }
            ],
            'shengen' => [
                'attribute' => 'shengen',
                'format' => 'raw',
                'value' => function($model) {
                    $html = Html::beginForm('/geo-country/save-shengen', 'post', ['style' => 'width:50px']);
                    $html .= \yii\bootstrap\Html::hiddenInput('country_id', $model->id);
                    $html .= \yii\bootstrap\Html::checkbox('checked', (bool)$model->shengen, [ 'class' => 'ajaxify' ]);
                    $html .= Html::endForm();
                    return $html;
                }
            ],
            'is_popular' => [
                'attribute' => 'is_popular',
                'format' => 'raw',
                'value' => function($model) {
                    $html = Html::beginForm('/geo-country/save-popular', 'post', ['style' => 'width:50px']);
                    $html .= \yii\bootstrap\Html::hiddenInput('country_id', $model->id);
                    $html .= \yii\bootstrap\Html::checkbox('checked', (bool)$model->is_popular, [ 'class' => 'ajaxify' ]);
                    $html .= Html::endForm();
                    return $html;
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
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

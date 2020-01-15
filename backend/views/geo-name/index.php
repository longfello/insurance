<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\geo\models\GeoNameSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Города';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="geo-name-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'zone_id' => [
              'attribute' => 'zone_id',
              'value' => function($model){
                  /** @var $model \common\modules\geo\models\GeoName */
                  return $model->zoneModel?$model->zoneModel->name:'(не задано)';
              },
              'filter' => \yii\helpers\ArrayHelper::map(\common\modules\geo\models\GeoZone::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name')
            ],
            'country_id' => [
	            'attribute' => 'country_id',
	            'value' => function($model){
		            /** @var $model \common\modules\geo\models\GeoName */
		            return $model->countryModel?$model->countryModel->name:'(не задано)';
	            },
              'filter' => \yii\helpers\ArrayHelper::map(\common\modules\geo\models\GeoCountry::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name')
            ],
            'population',
            'slug',
            'domain',
            // 'synonyms',
            // 'google_id',
            // 'big_banner_url:url',
            // 'small_banner_url:url',

            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{update}'
            ],
        ],
    ]); ?>
</div>

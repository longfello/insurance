<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiAlphaStrah\models\Country */

$this->title = $model->countryName;
$this->params['breadcrumbs'][] = ['label' => 'Страны', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="country-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'countryID',
            'countryUID',
            'countryName',
            'name',
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
            'terName',
            'region_id'=>  [
                'format' => 'raw',
                'label' => 'Регион',
                'value' => function($model){
                    return ($model->region)?$model->region->short_name:'';
                }
            ],
            'countryKV',
            'assistanteID',
            'assistanteUID',
            'assistanceCode',
            'assistanceName',
            'assistancePhones',
        ],
    ]) ?>

</div>

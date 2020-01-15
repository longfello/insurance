<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 23.12.16
 * Time: 14:58
 */
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Область действия страховой суммы '.$amount.' в продукте '.$product_model->productName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Риски'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $risk_model['riskName'], 'url' => ['view','id'=>$risk_model['riskId']]];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= \kartik\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
            'id_area',
            'name',
            [
                'format' => 'raw',
                'label' => 'Соответствие внутреннему справочнику',
                'value' => function($model){
                    $countries = \common\modules\ApiLiberty\models\Territory2Dict::find()->where(['id_area' => $model->id_area])->all();
                    $res = [];
                    foreach ($countries as $country){
                        $res[] = $country->geoNameModel->name;
                    }
                    sort($res);
                    return Html::ul($res);
                }
            ],
            'territoryGroupId',
        'functions' => ['class' => 'yii\grid\ActionColumn','template' => '']
    ],
]); ?>

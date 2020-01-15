<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Програмы страхования');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="insurance-programm-index">

    <p>
	    <?= Html::a(Yii::t('backend', 'Обновить програмы страхования'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'insuranceProgrammID',
            'insuranceProgrammName',
//            'insuranceProgrammPrintName',
//            'insuranceProgrammUID',
            /*[
              'format' => 'raw',
              'label'  => 'риски',
              'value'  => function($model){
                  $datas = \common\modules\ApiAlphaStrah\models\Risk2program::findAll(['program_id' => $model->insuranceProgrammID]);
                  $res = [];
                  foreach ($datas as $data){
                    $res[] = $data->risk->risk;
                  }
                  sort($res);
                  return \yii\bootstrap\Html::ul($res);
              }
            ],*/
            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}{update}'],
        ],
    ]); ?>
</div>

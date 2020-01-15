<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\modules\ApiSberbank\models\Territory2Program;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Програмы страхования';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-index">

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'insProgram',
            'name',
            [
                'format' => 'raw',
                'label' => 'Территории',
                'value' => function($model){
                    $t2p = Territory2Program::find()->where(['program_id' => $model->id])->all();
                    $res = [];
                    foreach ($t2p as $one_t2p){
                        $res[] = $one_t2p->territory->name;
                    }
                    sort($res);
                    return Html::ul($res);
                }
            ],
            [
                'format' => 'raw',
                'label' => 'Страховая сумма',
                'value' => function($model){
                    return ($model->costInterval)?$model->costInterval->name:'(нет)';
                }
            ],
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{update} {delete}'
            ],
        ],
    ]); ?>
</div>

<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Ассисстенты');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assistance-index">
    <p>
	    <?= Html::a(Yii::t('backend', 'Обновить ассистентов'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'assistanteID',
            'assistanteUID',
            'assistanceCode',
            'assistanceName',
            'assistancePhones',

	        ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
    ]); ?>
</div>

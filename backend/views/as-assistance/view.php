<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiAlphaStrah\models\Assistance */

$this->title = Yii::t('backend', 'Ассисстенты').' '.$model->assistanteID;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ассисстенты'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assistance-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'assistanteID',
            'assistanteUID',
            'assistanceCode',
            'assistanceName',
            'assistancePhones',
        ],
    ]) ?>

</div>

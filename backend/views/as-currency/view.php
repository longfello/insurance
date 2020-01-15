<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Currency */

$this->title = $model->currency;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Валюты'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'currencyID',
            'currency',
            'currencyName',
        ],
    ]) ?>

</div>

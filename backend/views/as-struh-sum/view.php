<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiAlphaStrah\models\StruhSum */

$this->title = Yii::t('backend', 'Страховые суммы').' - '.$model->risk;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Страховые суммы'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="struh-sum-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'riskID',
            'risk',
            'riskUID',
            'strahSummFrom',
            'strahSummTo',
            'valutaCode',
            'variant',
            'variantUid',
        ],
    ]) ?>

</div>

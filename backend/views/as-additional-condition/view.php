<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiAlphaStrah\models\AdditionalCondition */

$this->title = $model->additionalConditionValue;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Дополнительные условия'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="additional-condition-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'additionalConditionID',
            'additionalCondition',
            'additionalConditionUID',
            'additionalConditionValue',
        ],
    ]) ?>

</div>

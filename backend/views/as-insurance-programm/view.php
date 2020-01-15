<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiAlphaStrah\models\InsuranceProgramm */

$this->title = $model->insuranceProgrammName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Програмы страхования'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="insurance-programm-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'insuranceProgrammID',
            'insuranceProgrammName',
            'insuranceProgrammPrintName',
            'insuranceProgrammUID',
        ],
    ]) ?>

</div>

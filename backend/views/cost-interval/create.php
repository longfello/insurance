<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CostInterval */

$this->title = Yii::t('backend', 'Добавить интервал страховых сумм');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Интервалы страховых сумм'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cost-interval-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\InsuranceType */

$this->title = 'Добавление типа страхования';
$this->params['breadcrumbs'][] = ['label' => 'Типы страхования', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="insurance-type-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

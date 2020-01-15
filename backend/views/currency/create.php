<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Currency */

$this->title = 'Добавить валюту';
$this->params['breadcrumbs'][] = ['label' => 'Валюты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

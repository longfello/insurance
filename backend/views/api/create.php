<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Api */

$this->title = 'Добавить Api';
$this->params['breadcrumbs'][] = ['label' => 'Api', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

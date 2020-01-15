<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiTinkoff\models\Area */

$this->title = 'Редактировать регион: ' . $model->Display;
$this->params['breadcrumbs'][] = ['label' => 'Регионы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->Display;
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="program-update">

    <?= $this->render('_form', [
        'model' => $model,
        'id' => $id,
    ]) ?>

</div>

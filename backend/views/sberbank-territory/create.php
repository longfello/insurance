<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\ApiSberbank\models\Territory */

$this->title = 'Добавить территорию';
$this->params['breadcrumbs'][] = ['label' => 'Програмы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

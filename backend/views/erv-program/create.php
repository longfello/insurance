<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\ApiErv\models\Program */

$this->title = 'Добавить програму';
$this->params['breadcrumbs'][] = ['label' => 'Програмы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

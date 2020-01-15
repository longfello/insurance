<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\ApiVtb\models\Period */

$this->title = 'Добавление периода';
$this->params['breadcrumbs'][] = ['label' => 'Периоды', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="period-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\ApiVtb\models\Risk */

$this->title = 'Добавить страховой риск';
$this->params['breadcrumbs'][] = ['label' => 'Страховые риски', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="risk-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

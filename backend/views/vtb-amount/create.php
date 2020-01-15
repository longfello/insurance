<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\ApiVtb\models\Amount */

$this->title = 'Добавление страховой суммы';
$this->params['breadcrumbs'][] = ['label' => 'Страховые суммы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="amount-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

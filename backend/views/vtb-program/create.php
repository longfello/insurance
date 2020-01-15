<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\ApiVtb\models\Program */

$this->title = 'Добавление програмы страхования';
$this->params['breadcrumbs'][] = ['label' => 'Програмы страхования', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

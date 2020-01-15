<?php
/* @var $this yii\web\View */
/* @var $model common\modules\ApiRgs\models\Sum */

$this->title = 'Добавление суммы';
$this->params['breadcrumbs'][] = ['label' => 'Суммы страхования', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="classifier-create">

    <?php
    echo $this->render('_form', [
        'model' => $model,
    ]);
    ?>

</div>

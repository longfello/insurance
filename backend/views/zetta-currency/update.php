<?php
/* @var $this yii\web\View */
/* @var $model \common\modules\ApiZetta\models\Currency */

$this->title = 'Редактировать валюту: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Валюты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="currency-update">

    <?=
    $this->render('_form', [
        'model' => $model,
        'id' => $id
    ]);
    ?>

</div>

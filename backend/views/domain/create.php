<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Domain */

$this->title = 'Добавление домена';
$this->params['breadcrumbs'][] = ['label' => 'Домены', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="domain-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

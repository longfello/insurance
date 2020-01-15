<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GeoCountry */

$this->title = Yii::t('backend', 'Create Geo Country');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Geo Countries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="geo-country-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

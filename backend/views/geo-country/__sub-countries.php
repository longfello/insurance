<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GeoCountry */
/* @var $form yii\widgets\ActiveForm */
?>
<h3>Включает в себя страны:</h3>
<div class="container-fluid">
<?php
$countries = \common\models\GeoCountry::find()->where(['type' => \common\models\GeoCountry::TYPE_COUNTRY])->orderBy( [ 'name' => SORT_ASC ] )->all();
foreach ( $countries as $country ) {
	/** @var $risk \common\modules\ApiVtb\models\Risk */
	$p2r = \common\models\GeoTerritory2country::findOne( [
		'geo_territory_id'  => $model->id,
		'geo_country_id' => $country->id
	] );
	$checked = $p2r ? "checked='checked'" : "";
    ?>
      <div class="col-xs-3">
        <lablel for="subcountry-<?= $country->id ?>">
            <input id="subcountry-<?= $country->id ?>" <?= $checked ?> type="checkbox" name="subcountry[]" value="<?= $country->id ?>">
            <?= $country->name ?>
        </lablel>
      </div>
    <?php
}
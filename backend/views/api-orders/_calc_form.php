<?php

  /** @var $model \common\models\Orders */
  /** @var $this \yii\web\View */

?>
<table class="table table-responsive table-hover table-striped">
	<tr>
		<td>Страна путешествия</td>
		<td>
			<?php
			  $countries = [];
			  foreach($model->calc_form->countries as $id){
				  $country = \common\models\GeoCountry::findOne(['id' => $id]);
				  $countries[] = $country->name;
			  }
			  echo \yii\bootstrap\Html::ul($countries);
			?>
		</td>
	</tr>
	<tr>
		<td>Даты путешествия</td>
		<td><?= $model->calc_form->dates ?></td>
	</tr>
	<tr>
		<td>Страхователи</td>
		<td>
			<?php
			$list = [];
			foreach($model->calc_form->travellers as $traveller){
				$list[] = $traveller->first_name.' '.$traveller->last_name.' ('.$traveller->birthdayAsDate().')';
			}
			echo \yii\bootstrap\Html::ul($list);
			?>
		</td>
	</tr>
</table>

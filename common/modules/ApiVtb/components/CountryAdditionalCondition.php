<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 10.04.17
 * Time: 11:12
 */

namespace common\modules\ApiVtb\components;


use common\modules\ApiVtb\components\AdditionalConditionPrototype;
use common\modules\ApiVtb\models\Country2dict;
use common\components\Calculator\forms\TravelForm;
use frontend\models\PersonInfo;
use yii\base\Component;

/**
 * Class CountryAdditionalCondition Дополнительный параметр страна
 * @package common\modules\ApiVtb\components
 */
class CountryAdditionalCondition extends AdditionalConditionPrototype {

    /**
     * @inheritdoc
     * @return int|mixed
     */
    public function getKoef(){
	  $koeficient = 1;

	  foreach($this->params as $koef => $countries){
	  	$vtbCountries = [];
        $models = Country2dict::findAll(['internal_id' => $this->form->countries]);
		foreach($models as $model) {
			$vtbCountries[] = $model->api_id;
		}
	  	if (array_intersect($countries, $vtbCountries)){
	  		$koeficient = max($koeficient, $koef);
	    }
	  }

	  return $koeficient;
  }
}
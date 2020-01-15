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

namespace common\modules\ApiAlphaStrah\components;


use common\modules\ApiAlphaStrah\components\AdditionalConditionPrototype;
use common\components\Calculator\forms\TravelForm;
use frontend\models\PersonInfo;
use yii\base\Component;

/**
 * Class AgeAdditionalCondition Дополнительный параметр - возраст
 * @package common\modules\ApiAlphaStrah\components
 */
class AgeAdditionalCondition extends AdditionalConditionPrototype {

    /**
     * @inheritdoc
     */
    public function getKoef(){
	  $koeficient = 1;
	  if ($this->form->travellers) {
		  $k = array_fill(0, $this->form->travellersCount, 1);
		  foreach($this->form->travellers as $key => $traveller){
			  $birthday = \DateTime::createFromFormat('Y-m-d', $traveller->birthday);
			  if ($birthday) {
				  $now = new \DateTime();
				  $i = $birthday->diff($now);

				  foreach($this->params as $old => $koef){
					  if ($i->y >= $old){
						  $k[$key] = $koef;
					  };
				  }
			  }
		  }

		  $ek = 0;
		  foreach ($k as $key => $koef){
			  $ek += $koef;
		  }
		  $koeficient = $ek / $this->form->travellersCount;
	  }

	  return $koeficient;
  }
}
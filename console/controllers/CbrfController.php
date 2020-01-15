<?php

namespace console\controllers;

use common\models\Currency;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class CbrfController extends Controller
{
    public function actionUpdate(){
    	Console::output("Update rates from CBRF...");
	    $currencies = Yii::$app->CbRF->all();
	    foreach($currencies as $currency){
	    	unset($currency['id']);
	    	$model = Currency::findOne(['char_code' => $currency['char_code']]);
	    	if (!$model){
	    		$model = new Currency();
		    }
		    $model->setAttributes($currency);
	    	$model->save();
	    	Console::output("{$model->char_code} => {$model->value}");
	    }
    }
}

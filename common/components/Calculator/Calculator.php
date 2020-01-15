<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 20.02.17
 * Time: 17:01
 */

namespace common\components\Calculator;


use common\components\Calculator\widgets\travel\CalculatorWidget;
use common\models\InsuranceType;
use yii\base\Component;
use yii\base\Object;

/**
 * Class Calculator Хелпер виджетов калькуляторов
 * @package common\components\Calculator
 */
class Calculator extends Object {
    /** @var InsuranceType[] Перечень доступных типов страхования */
    public $availableTypes = [];

    /**
     * Инициализация свойств
     */
    public function init(){
    	parent::init();

	    foreach (InsuranceType::find()->all() as $one){
	    	$this->availableTypes[$one->slug] = $one;
	    }
    }
  
    /**
     * @return string вызов виджета главной страницы
     */
    public static function homePageForm(){
		return CalculatorWidget::widget();
	}

    /**
     * @return string вызов виджета расширенного вида
     */
    public static function calcPageForm(){
		return CalculatorWidget::widget([ 'layout' => CalculatorWidget::LAYOUT_CALC]);
	}

}
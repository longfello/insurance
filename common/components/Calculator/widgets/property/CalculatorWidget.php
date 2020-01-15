<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 20.02.17
 * Time: 17:03
 */
namespace common\components\Calculator\widgets\property;

use common\components\Calculator\forms\TravelForm;
use yii\bootstrap\Widget;

/**
 * Class CalculatorWidget Калькулятор Страхование собственности
 * @package common\components\Calculator\widgets\property
 */
class CalculatorWidget extends Widget {
    /**
     * Лайаут главного вида
     */
    const LAYOUT_HOME = 'home';
    /**
     * Лайаут расширенного вида
     */
    const LAYOUT_CALC = 'calc';

    /**
     * @var string Лайаут для отображения
     */
	public $layout = self::LAYOUT_HOME;

    /**
     * @inheritdoc
     */
	public function run(){
		/*
		$model = new TravelForm();
		$model->scenario = $this->layout;

		$model->load(\Yii::$app->request->post());
		*/
		return $this->render('calculator/'.$this->layout, [/*'model' => $model*/]);
	}
}
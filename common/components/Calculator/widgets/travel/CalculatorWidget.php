<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 20.02.17
 * Time: 17:03
 */
namespace common\components\Calculator\widgets\travel;

use common\components\Calculator\forms\TravelForm;
use yii\bootstrap\Widget;

/**
 * Class CalculatorWidget Калькулятор Туристическое страхование
 * @package common\components\Calculator\widgets\travel
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
	 * Лайаут лендинга
	 */
	const LAYOUT_LANDING = 'landing';
    /**
     * Лайаут главного вида (новый)
     */
    const LAYOUT_HOME_NEW = 'home_new';

    /**
     * @var string Лайаут для отображения
     */
	public $layout = self::LAYOUT_HOME;

    /**
     * @var TravelForm Форма калькулятора
     */
	public $model;

    /**
     * @inheritdoc
     */
	public function run(){
		if (!$this->model) {
            $this->model = new TravelForm();
            $this->model->scenario = $this->layout;
            $this->model->load(\Yii::$app->request->post());
		}

		return $this->render('calculator/'.$this->layout, ['model' => $this->model]);
	}
}

?>



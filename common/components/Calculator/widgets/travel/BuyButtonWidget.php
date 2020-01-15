<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 20.02.17
 * Time: 17:03
 */

// namespace frontend\components\widgets;
namespace common\components\Calculator\widgets\travel;


use common\components\Calculator\forms\TravelForm;
use yii\bootstrap\Widget;

/**
 * Class BuyButtonWidget Виджет кнопки "Купить"
 * @package common\components\Calculator\widgets\travel
 */
class BuyButtonWidget extends Widget {
    /**
     * @var string Лайаут для отображения
     */
	public $layout = 'index';
    /**
     * @var \common\models\ProgramResult Программа страхования
     */
    public $program;

    /**
     * @inheritdoc
     */
	public function run(){
		return $this->render('BuyButton/'.$this->layout, ['program' => $this->program]);
	}
}
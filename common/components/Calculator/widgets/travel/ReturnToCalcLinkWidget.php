<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 20.02.17
 * Time: 17:03
 */

namespace common\components\Calculator\widgets\travel;


use common\components\Calculator\forms\TravelForm;
use common\models\ProgramResult;
use yii\bootstrap\Widget;

/**
 * Class ReturnToCalcLinkWidget Виджет кнопки возврата к калькулятору
 * @package common\components\Calculator\widgets\travel
 */
class ReturnToCalcLinkWidget extends Widget {
    /**
     * @var string Лайаут для отображения
     */
	public $layout = 'index';
    /**
     * @var ProgramResult Програма страхования
     */
    public $program;

    /**
     * @inheritdoc
     */
	public function run(){
		return $this->render('ReturnToCalcLink/'.$this->layout, ['program' => $this->program]);
	}
}
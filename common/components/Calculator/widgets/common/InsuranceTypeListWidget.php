<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 20.02.17
 * Time: 17:03
 */
namespace common\components\Calculator\widgets\common;

use common\components\Calculator\forms\TravelForm;
use common\components\Calculator\widgets\travel\CalculatorWidget;
use common\models\InsuranceType;
use common\models\Page;
use yii\bootstrap\Widget;

/**
 * Class InsuranceTypeListWidget Виджет отображения типов страхования
 * @package common\components\Calculator\widgets\common
 */
class InsuranceTypeListWidget extends Widget {
    /**
     * @var string Лайаут для отображения
     */
	public $layout = 'simple';

    /**
     * @inheritdoc
     */
	public function run(){
		return $this->render('insurance-type-list/'.$this->layout, [
			'models'   => InsuranceType::find()->orderBy(['sort_order' => SORT_ASC])->where(['enabled' => 1])->all()
		]);
	}
}